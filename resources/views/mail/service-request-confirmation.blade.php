<x-mail::message>
# Thank you, {{ $serviceRequest->user->name }}

We have received your request and a member of our sales team will be in touch shortly.

**Services requested:**
@foreach ($serviceRequest->services as $service)
- {{ $service->name }}
@endforeach

<x-mail::button :url="route('dashboard')">
View your dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
