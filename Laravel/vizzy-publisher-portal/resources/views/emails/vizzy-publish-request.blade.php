@component('mail::message')
# Request Publishing of Vizzy

<b>Podcast Title:</b> {{ $vizzy->podcast->title }}<br />
<b>Vizzy Title:</b> {{ $vizzy->title }}<br />
<b>Vizzy Link:</b> <a href="{{ $vizzy->edit_url }}">Click here</a><br />
<br />
<b>Podcast Claimed by:<br />
@if ($vizzy->podcast->user)
<b>Name:</b> {{ $vizzy->podcast->user->firstname }} {{ $vizzy->podcast->user->lastname }} (id: {{ $vizzy->podcast->user->id }})<br />
<b>Email:</b> {{ $vizzy->podcast->user->email }}<br />
@else
Admin<br />
@endif
<br />
<br />
Thanks,<br>
Vizzy for Podcasters
@endcomponent
