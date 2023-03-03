@component('mail::message')
# Podcast ownership validation

Please use below code to verify you are the owner of the podcast:
<br />
## {{ $podcastVerification->title}}
<br />
Verification code:
<b>{{ $podcastVerification->code}}</b>
<br />
<br />
<br />
If you did not make this request, please ignore this email, no further action is required.<br />
<br />
Thanks,<br>
Vizzy for Podcasters
@endcomponent
