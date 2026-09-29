<x-guest-layout>
    <div class="py-4 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">{{ config('app.name') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('Register your company and tell us which services you need. Our sales team will get back to you.') }}</p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex justify-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Go to dashboard') }}</a>
            @else
                <a href="{{ route('register') }}" class="inline-flex justify-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Register') }}</a>
                <a href="{{ route('login') }}" class="inline-flex justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">{{ __('Log in') }}</a>
            @endauth
        </div>
    </div>
</x-guest-layout>
