<?php

namespace App\Actions;

use App\Mail\ServiceRequestConfirmation;
use App\Mail\ServiceRequestReceived;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SubmitServiceRequest
{
    /**
     * @param  array<int>  $serviceIds
     */
    public function handle(User $user, array $serviceIds, ?string $notes): ServiceRequest
    {
        $serviceRequest = $this->store($user, $serviceIds, $notes);

        $this->notify($serviceRequest);

        return $serviceRequest;
    }

    /**
     * @param  array<int>  $serviceIds
     */
    public function store(User $user, array $serviceIds, ?string $notes): ServiceRequest
    {
        return DB::transaction(function () use ($user, $serviceIds, $notes) {
            $serviceRequest = $user->serviceRequests()->create(['notes' => $notes]);
            $serviceRequest->services()->sync($serviceIds);

            return $serviceRequest;
        });
    }

    public function notify(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->loadMissing('services', 'user');
        $user = $serviceRequest->user;

        // Mail is sent synchronously (no queue worker on Plesk). A mail failure
        // must not lose the request — it is already saved and visible in /admin.
        $this->send(fn () => Mail::to(config('temtec.sales_emails'))->send(new ServiceRequestReceived($serviceRequest)),
            'sales', $serviceRequest, config('temtec.sales_emails') !== []);
        $this->send(fn () => Mail::to($user)->send(new ServiceRequestConfirmation($serviceRequest)),
            'client', $serviceRequest);
    }

    private function send(callable $callback, string $recipient, ServiceRequest $serviceRequest, bool $enabled = true): void
    {
        if (! $enabled) {
            Log::warning('SALES_EMAILS is not configured; sales notification skipped.', ['service_request_id' => $serviceRequest->id]);

            return;
        }

        try {
            $callback();
        } catch (Throwable $e) {
            Log::error("Failed to send {$recipient} email for service request.", [
                'service_request_id' => $serviceRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
