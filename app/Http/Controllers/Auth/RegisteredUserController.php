<?php

namespace App\Http\Controllers\Auth;

use App\Actions\SubmitServiceRequest;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Support\CompanyProfileRules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', ['services' => Service::active()->get()]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, SubmitServiceRequest $submitServiceRequest): RedirectResponse
    {
        $validated = $request->validate([
            ...CompanyProfileRules::rules(),
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ...CompanyProfileRules::serviceRules(),
        ], [
            'services.required' => 'Please select at least one service you are interested in.',
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        // Nested transaction: the user and their first request are saved together;
        // emails go out from notify() only once both are committed.
        [$user, $serviceRequest] = DB::transaction(function () use ($validated, $submitServiceRequest) {
            $user = User::create([
                ...collect($validated)->only([...CompanyProfileRules::fields(), 'email'])->all(),
                'password' => $validated['password'],
            ]);

            return [$user, $submitServiceRequest->store($user, $validated['services'], $validated['notes'] ?? null)];
        });

        $submitServiceRequest->notify($serviceRequest);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('status', 'Thank you for registering! Our sales team has received your request.');
    }
}
