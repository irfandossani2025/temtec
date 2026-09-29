<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Your company') }}</h3>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                    </div>
                    @php($user = auth()->user())
                    <dl class="mt-4 space-y-3 text-sm">
                        @foreach ([
                            'Company' => $user->company_name,
                            'Contact' => $user->name.($user->job_title ? ' · '.$user->job_title : ''),
                            'Email' => $user->email,
                            'Phone' => $user->phone,
                            'Website' => $user->website,
                            'Location' => collect([$user->address, $user->city, $user->country])->filter()->implode(', '),
                        ] as $label => $value)
                            <div>
                                <dt class="text-gray-500">{{ __($label) }}</dt>
                                <dd class="text-gray-900 break-words">{{ $value ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Your service requests') }}</h3>
                        <a href="{{ route('service-requests.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Request more services') }}
                        </a>
                    </div>

                    @forelse ($serviceRequests as $serviceRequest)
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <p class="text-xs text-gray-500">{{ __('Submitted') }} {{ $serviceRequest->created_at->format('M j, Y') }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($serviceRequest->services as $service)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $service->name }}</span>
                                @endforeach
                            </div>
                            @if ($serviceRequest->notes)
                                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $serviceRequest->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="mt-4 text-sm text-gray-500">{{ __('You have not requested any services yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
