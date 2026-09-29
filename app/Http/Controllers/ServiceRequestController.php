<?php

namespace App\Http\Controllers;

use App\Actions\SubmitServiceRequest;
use App\Models\Service;
use App\Support\CompanyProfileRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function create(): View
    {
        return view('service-requests.create', ['services' => Service::active()->get()]);
    }

    public function store(Request $request, SubmitServiceRequest $submitServiceRequest): RedirectResponse
    {
        $validated = $request->validate(CompanyProfileRules::serviceRules(), [
            'services.required' => 'Please select at least one service you are interested in.',
        ]);

        $submitServiceRequest->handle($request->user(), $validated['services'], $validated['notes'] ?? null);

        return redirect()->route('dashboard')
            ->with('status', 'Thank you! Our sales team has received your request.');
    }
}
