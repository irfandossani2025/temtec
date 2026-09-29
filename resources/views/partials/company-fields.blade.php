{{-- Company & contact fields. Expects optional $user for pre-filling (profile page). --}}
@php($user = $user ?? null)
@php($field = fn ($name) => old($name, $user?->{$name}))

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="company_name" :value="__('Company name') . ' *'" />
        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="$field('company_name')" required autocomplete="organization" />
        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" :value="__('Contact name') . ' *'" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="$field('name')" required autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="job_title" :value="__('Job title')" />
        <x-text-input id="job_title" name="job_title" type="text" class="mt-1 block w-full" :value="$field('job_title')" autocomplete="organization-title" />
        <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email') . ' *'" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="$field('email')" required autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone number') . ' *'" />
        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="$field('phone')" required autocomplete="tel" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="website" :value="__('Company website')" />
        <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="$field('website')" placeholder="https://" autocomplete="url" />
        <x-input-error :messages="$errors->get('website')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="$field('address')" autocomplete="street-address" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city" :value="__('City')" />
        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="$field('city')" autocomplete="address-level2" />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" :value="__('Country')" />
        <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="$field('country')" autocomplete="country-name" />
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>
</div>
