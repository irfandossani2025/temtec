<x-guest-layout width="sm:max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900">{{ __('Create your account') }}</h1>
    <p class="mt-1 text-sm text-gray-600">{{ __('Tell us about your company and the services you need. Our sales team will be in touch.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-8">
        @csrf

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ __('Company & contact details') }}</h2>
            <div class="mt-3">
                @include('partials.company-fields')
            </div>
        </section>

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ __('Services') }}</h2>
            <div class="mt-3">
                @include('partials.service-picker')
            </div>
        </section>

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ __('Password') }}</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" :value="__('Password') . ' *'" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password') . ' *'" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
