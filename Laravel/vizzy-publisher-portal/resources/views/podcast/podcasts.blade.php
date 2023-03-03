{{-- Extends layout --}}
@extends('layout.default')

@section('title')
My Podcasts
@endsection

{{-- Content --}}
@section('content')
            <!-- row -->
      <div class="container-fluid">
        <div class="row homepage-studio two-buttons">
          <div class="col-xl-6 col-lg-6">
            <div class="card shadow_1">
              <div class="card-body">
                <h3 class="card-title text-center">Add visual marketing to your podcast</h3>
                <p class="card-text text-center">Create a Vizzy for your existing podcast episodes, and remaster your back catalogue with images, links and more.</p>
                <div class="text-center">
                    <a href="{!! route('vizzies') !!}" class="btn btn-primary">Create a Vizzy</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6">
            <div class="card shadow_1">
              <div class="card-body">
                <h3 class="card-title text-center">Add chapters to your podcast file</h3>
                <p class="card-text text-center">Upload your audio to so you can add images and chapters to your podcast that is compatible with Apple Podcasts</p>
                <div class="text-center">
                    <a href="{!! route('studio') !!}?upload" class="btn btn-primary upload-podcast-button">Upload audio file</a>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="row podcasts-row">
          <div class="col-xl-12">
            <div class="card custom-card">
              <div class="flex-wrap border-0 pt-5 pb-0">
                <h3 class="fs-32 text-black font-w700 mr-auto mb-2 pr-3">My Podcasts</h3>      
              </div>

              @if(count(Auth::user()->podcasts)>0)
              <div class="card-body">  
                @foreach (Auth::user()->podcasts as $podcast)                    
                <div class="row mb-4">
                  <div class="col-sm-2"><img class="img-fluid" src="{{$podcast->image}}" /></div>
                  <div class="col-sm-10">
                    <a href="{{ route('episodes', $podcast) }}"><h4>{!!$podcast->title!!}</h4></a>
                    <div>{{ strip_tags($podcast->description) }}</div>
                    <div class="mt-4">
                      <span><b># Episodes:</b> <span id="ep{{$podcast->id}}" class="episode-count">{{$podcast->episodes}}</span></span>
                      <span class="ml-4"><b>Categories:</b>&nbsp;&nbsp;
                        @foreach ($podcast->categories as $category)
                          <span class="badge badge-success light">{!! $category->category !!}</span>
                        @endforeach
                      </span>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              <div>
                <a href="{{ route('add-podcast') }}" class="btn podcast-button">Claim more podcast</a>
              </div>
              @else
              <div>
                <p class="fs-18 text-black font-w400 mr-auto my-4 pr-3">You havn’t claimed any podcasts yet. Claim a podcast to get started creating Vizzys.</p>
                <a href="{{ route('add-podcast') }}" class="btn podcast-button">Claim a podcast</a>
              </div>
              @endif
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