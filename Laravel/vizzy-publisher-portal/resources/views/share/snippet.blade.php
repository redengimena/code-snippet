@if ($episode)
<img src="{{ $episode->getArtWork()->getUri() }}" width="200"/><br />
@else
<img src="{{ $rss->getArtWork()->getUri() }}" width="200"/><br />
@endif
<br />
{{ $rss->getTitle() }}<br />
{{ strip_tags($rss->getDescription()) }}<br />
<br />
@if ($episode)
{{ $episode->getTitle() }}<br />
{{ strip_tags($episode->getDescription()) }}<br />
@endif
