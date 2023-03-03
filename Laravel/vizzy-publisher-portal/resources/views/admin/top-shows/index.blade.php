{{-- Extends layout --}}
@extends('layout.default')

@section('title')
Administration
@endsection

@section('content')
<!-- row -->
<div class="container-fluid">
    <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Administration</li>
                <li class="breadcrumb-item active">Top Shows</li>
            </ol>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header flex-wrap border-0 pb-0">
                            <h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Top Shows</h3>
                            <a href="#" data-toggle="modal" data-target="#addTopShowModal" class="btn btn-primary btn-rounded mb-2">Add</a>
                        </div>
                        <div class="card-body top-show-list">
                            <div class="dd">
                                <ol class="dd-list">
                                    @foreach ($topshows as $topshow)
                                    <li class="dd-item" data-id="{{$topshow->podcast->id}}">
                                        <div class="row p-3">
                                            <div class="col-sm-1"><img class="img-fluid dd-handle" src="{{$topshow->podcast->image}}" /></div>
                                            <div class="col-sm-9">
                                                <a href="{{ route('episodes', $topshow->podcast) }}"><h4>{!!$topshow->podcast->title!!}</h4></a>
                                                <div>{{ strip_tags($topshow->podcast->description) }}</div>
                                            </div>
                                            <div class="col-sm-2 text-right"><button class="btn btn-primary btn-xxs" data-id="{{$topshow->podcast->id}}">Delete</button></div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addTopShowModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 mb-2">
                        <h4 class="card-title">Search Show</h4>
                        <hr />
                    </div>
                  <div class="col-lg-5 mb-2">
                      <div class="form-group">
                          <label class="text-label">Search for Podcast by name</label>
                          <div class="input-group">
                              <input type="text" id="feed_name" name="feed_name" class="form-control" />
                              <div class="input-group-append">
                                  <button id="search_name" class="btn btn-primary" type="button">Search</button>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-lg-2 mb-2">
                      <div class="text-center mt-4">OR</div>
                  </div>
                  <div class="col-lg-5 mb-2">
                      <div class="form-group">
                          <label class="text-label">Enter Podcast feed url</label>
                          <div class="input-group">
                              <input type="text" id="feed_url" name="feed_url" class="form-control" />
                              <div class="input-group-append">
                                  <button id="search_url" class="btn btn-primary" type="button">Search</button>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-lg-12 mt-4">
                      <div id="feed_result" class=""></div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('css')
<link href="{{ asset('vendor/nestable2/css/jquery.nestable.min.css') }}"  rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('vendor/nestable2/js/jquery.nestable.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
var added_podcasts = {!! $podcasturls !!};
</script>
<script src="{{ asset('js/admin/top-shows.min.js') }}" type="text/javascript"></script>
@endpush