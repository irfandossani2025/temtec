@php($user = $serviceRequest->user)
<x-mail::message>
# New service request #{{ $serviceRequest->id }}

**Company:** {{ $user->company_name ?: '—' }}
**Contact:** {{ $user->name }}{{ $user->job_title ? ' ('.$user->job_title.')' : '' }}
**Email:** {{ $user->email }}
**Phone:** {{ $user->phone ?: '—' }}
**Website:** {{ $user->website ?: '—' }}
**Address:** {{ collect([$user->address, $user->city, $user->country])->filter()->implode(', ') ?: '—' }}

## Services requested
@foreach ($serviceRequest->services as $service)
- {{ $service->name }}
@endforeach

@if ($serviceRequest->notes)
## Client notes
{{ $serviceRequest->notes }}
@endif

<x-mail::button :url="url('/admin/service-requests/'.$serviceRequest->id)">
Open in admin
</x-mail::button>

Reply to this email to contact the client directly.
</x-mail::message>
