{{-- Extends layout --}}
@extends('layout.default')

@section('title')
My Podcasts
@endsection

{{-- Content --}}
@section('content')
            <!-- row -->
      <div class="container-fluid">
        <div class="d-flex flex-wrap mb-4" style="align-items:center;justify-content:space-between;">
          <div class="page-titles mb-0">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active text-primary font-w600">My Vizzy's</li>
            </ol>
          </div>
        </div>


        <div class="row">
          <div class="col-xl-12">
            <div class="card custom-card">
              <div class="card-header flex-wrap border-0 pb-0">
                <h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">My Vizzy's</h3>
              </div>
              <div class="card-body">
                @foreach ($vizzies as $vizzy)
                <div class="row mb-4">
                  <div class="col-sm-2">
                    <div class="vizzy-tile-image">
                      <img class="img-fluid" src="{{$vizzy->image}}" />
                    </div>
                  </div>
                  <div class="col-sm-10">
                    @if (!$vizzy->episode())
                    <h4>{!!$vizzy->title!!}</h4>
                    <div class="text-danger">Episode no longer exists in the Podcast feed.</div>
                    @else
                    <a href="{{ route('curator', ['podcast' => $vizzy->podcast_id, 'guid' => urlencode($vizzy->episode_guid)]) }}"><h4>{!!$vizzy->title!!}</h4></a>
                    <div>{{ strip_tags($vizzy->episode()->getDescription()) }}</div>
                    @endif
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

@endsection

@push('css')
@endpush

@push('footer-scripts')
<script src="{{ asset('js/podcast/podcasts.js') }}" type="text/javascript"></script>
@endpush