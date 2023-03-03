@component('mail::message')
# Admin Podcast Transfer

'{{ $podcast->title }}' has be transferred to user {{ $podcast->user->firstname }} {{ $podcast->user->lastname }} (id: {{ $podcast->user->id }})
<br />
<br />
Thanks,<br>
Vizzy for Podcasters
@endcomponent
