{{-- Service checkboxes + notes. Expects $services (active Service collection). --}}
@php($selected = collect(old('services', []))->map(fn ($id) => (int) $id))

<fieldset>
    <legend class="block font-medium text-sm text-gray-700">{{ __('Services you are interested in') }} *</legend>

    @if ($services->isEmpty())
        <p class="mt-2 text-sm text-gray-500">{{ __('No services are available at the moment. Please check back soon.') }}</p>
    @else
        <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($services as $service)
                <label for="service_{{ $service->id }}" class="flex items-start gap-3 rounded-md border border-gray-200 p-3 hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                    <input id="service_{{ $service->id }}" type="checkbox" name="services[]" value="{{ $service->id }}"
                        class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        @checked($selected->contains($service->id))>
                    <span>
                        <span class="block text-sm font-medium text-gray-900">{{ $service->name }}</span>
                        @if ($service->description)
                            <span class="block text-xs text-gray-500">{{ $service->description }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    <x-input-error :messages="array_merge($errors->get('services'), $errors->get('services.*'))" class="mt-2" />
</fieldset>

<div class="mt-4">
    <x-input-label for="notes" :value="__('Tell us about your needs (optional)')" />
    <textarea id="notes" name="notes" rows="4" maxlength="5000"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
