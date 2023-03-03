{{-- Extends layout --}}
@extends('layout.default')

@section('title')
My Podcasts
@endsection

{{-- Content --}}
@section('content')
            <!-- row -->
      <div class="container-fluid">
        <div class="page-titles">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('podcasts') }}">My Shows</a></li>
            <li class="breadcrumb-item">{!!$rss->getTitle()!!}</li>
            <li class="breadcrumb-item active text-primary font-w600">My Episodes</li>
          </ol>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="card custom-card">
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-2"><img class="img-fluid" src="{{$rss->getArtwork()->getUri()}}" /></div>
                  <div class="col-sm-10">
                    <h4>{!!$rss->getTitle()!!}</h4>
                    <div>{!! strip_tags($rss->getDescription()) !!}</div>
                    <div class="mt-4">
                      <span><b># Episodes:</b> {{$rss->getEpisodes()->count()}}</span>
                      <span class="ml-4"><b>Categories:</b>&nbsp;&nbsp;
                        @foreach ($rss->getCategories() as $category)
                          <span class="badge badge-success light">{!! $category->getName() !!}</span>
                        @endforeach
                      </span>
                    </div>
                  </div>
                </div>  
              </div>
            </div>
          </div>
        </div>
        
        <div class="row mb-4">
          <div class="col-xl-12">
            <h4>My Episodes</h4>
          </div>
        </div>

        @foreach ($rss->getEpisodes() as $episode)
        <div class="row">
          <div class="col-xl-12">
            <div class="card custom-card new-arrival-content">
              <div class="card-body">
                <div class="row" style="align-items:center">
                  <div class="col-sm-8">
                    <h5 class="mb-0">{{ $episode->getEpisodeNumber() }}. {!! $episode->getTitle() !!}</h5>
                    <p><span class="item">{{ $episode->getPublishedDate()->format('j F') }}</span></p>
                    <p>{{ strip_tags($episode->getDescription()) }}</p>
                  </div>
                  <div class="col-sm-2 text-center">
                    {{ Str::duration($episode->getDuration()) }}
                  </div>
                  <div class="col-sm-2 text-center">
										@if ($podcast->getVizzyByGuid($episode->getGuid()))
										<a href="{{ route('curator', ['podcast' => $podcast->id, 'guid' => urlencode($episode->getGuid())]) }}" class="btn btn-primary" type="submit">Edit Vizzy</a>
										@else
                    <a href="{{ route('curator', ['podcast' => $podcast->id, 'guid' => urlencode($episode->getGuid())]) }}" class="btn btn-primary" type="submit">Create Vizzy</a>
										@endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach


      </div>
        
@endsection

@push('css')
@endpush

@push('footer-scripts')
@endpush