{{-- Extends layout --}}
@extends('layout.default')

@section('title')
@if($audio->name)
{{ $audio->name }}
@else
Your uploaded podcast
@endif
@endsection

@section('header-right')
<div class="last-update-div">
  <span id="last_updated"></span>
</div>
@endsection

{{-- Content --}}
@section('content')
			<div class="container-fluid pt-0 studio-tool-content">
        <div class="row">
          <div class="col-xl-12">
                <form id="saveForm" action="{{ route('studio-save', $audio->slug()) }}" method="POST"> @csrf
                  <input type="hidden" name="chapters" value="{{ $audio->chapters }}" />
                </form>

                <div id="single-song-player" class="pb-4">
                    <div class="row">
                      <div class="col-xl-6"><h3>Timeline</h3></div>
                      <div class="col-xl-6 text-right">
                        <span class="current-time">
                          <span class="amplitude-current-time"></span>
                        </span>
                        <div class="control-container">
                          <div class="amplitude-play-pause" id="play-pause"><span class="mdi mdi-play"></span><span class="mdi mdi-pause"></span></div>
                        </div>
                      </div>
                    </div>

                    <div id="time-container">
                      <div id="progress-container">
                        <progress id="song-played-progress" class="amplitude-song-played-progress"></progress>
                        <progress id="song-buffered-progress" class="amplitude-buffered-progress" value="0"></progress>
                        <div class="song-navigation">
                          <div class="timeline-thumbnails"></div>
                          <input type="range" class="range amplitude-song-slider" step=".1"/>
                          <output class="bubble add-card"></output>
                        </div>
                      </div>
                    </div>
                </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-xl-4 col-lg-4">
                <h2 class="mb-4 font-weight-bold">Chapters</h2>
            <div id="cueList" class="dz-scroll height500">
                <div class="cue-list-panel"></div>
            </div>
            
          </div>

          <div class="col-xl-4 col-lg-4">
            <h2 class="font-weight-bold header-display-none"></h2>
            <div id="card_detail" class="">
              <div class="card">
                <div class="card-body">
                  <div class="row pb-4">
                    <div class="col-xl-12 col-lg-12">
                      <label>Chapter image</label>
                      <div class="row media orig-image d-none">
                        <div class="col-xl-4 col-lg-4">
                          <div class="apple-image"><img src="" id="orig_image"/></div>
                        </div>
                        <div class="col-xl-8 col-lg-8">
                          <a id="edit_image"><i class="fa fa-pencil"></i> Edit</a>
                        </div>
                      </div>
                      <div class="upload-container">
                        <div class="small">It is best to use a square chapter image (3000 x 3000 pixels in .jpg or .png format)</div>
                        <div class="upload-box">
                          <form method="post" action="{{ route('studio-image-upload', $audio->slug()) }}" enctype="multipart/form-data"
                            class="dropzone" id="dropzone">
                            @csrf
                          </form>
                        </div>
                      </div>
                      <span id="upload_error" class="text-danger"></span>
                    </div>
                  </div>
                  <div class="row pb-4">
                    <div class="col-xl-12 col-lg-12">
                      <label for="chapter_name">Chapter title</label>
                      <input class="form-control" type="text" name="chapter_name" placeholder="Enter a title" />
                    </div>
                  </div>
                  <div class="row pb-4">
                    <div class="col-xl-12 col-lg-12 mt-2">
                      <label for="chapter_descr">Chapter description</label>
                      <textarea class="form-control" rows="8" name="chapter_descr" placeholder="Enter a description"></textarea>
                    </div>
                  </div>
                  <div class="row pb-4">
                    <div class="col-xl-12 col-lg-12">
                      <label for="chapter_url">Chapter url</label>
                      <input class="form-control" type="text" name="chapter_url" />
                    </div>
                  </div>
                  <div class="row d-none">
                    <div class="col-xl-6 col-lg-6">
                      <label for="chapter_start">Start</label>
                      <input class="form-control" type="text" name="chapter_start" value="00:00" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-4">
            <h3 class="mb-4">Chapter Preview</h3>
            <div class="text-center">
              <div id="iphone_mask" class="mb-4">
                <div id="card_preview">
                    <div class="apple-image"><img src=""/></div>
                    <div class="preview-chapter-name text-center pt-2 d-none"></div>
                </div>
              </div>
            </div>

            <div class="text-center">
              <h3 class="mb-4">Download enriched audio file</h3>
              <p>Download your audio file enriched with chapters and images. Upload this file to your podcast host and your listeners can enjoy it in apple podcasts.</p>
              <div class="download-buttons">
                <form id="download_form" class="d-inline-block" method="post" action="{{ route('studio-download', $audio->slug()) }}" >
                  @csrf
                  <input type="submit" class="btn btn-primary mb-2" value="Download mp3" />
                </form>
                <button id="generate_notes" class="btn btn-primary mb-2">Generate Show Notes</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="errorModal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">Please enter a chapter title for each chapter to proceed.</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-xs light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="noteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">Your Show Notes</div>
                    <div class="modal-body"><textarea id="show_notes" class="form-control" rows="10"></textarea></div>
                    <div class="modal-footer">
                        <button type="button" class="copy btn btn-danger btn-xs light" data-clipboard-target="#show_notes">Copy Text</button>
                        <button type="button" class="btn btn-danger btn-xs light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

      </div>
@endsection

@push('css')
<link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}"  rel="stylesheet">
<link href="{{ asset('css/media.css') }}"  rel="stylesheet">
<link href="{{ asset('css/studio-tool.css') }}"  rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/camanjs/4.1.2/caman.full.min.js"></script>
<!-- <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>
<script src="{{ asset('js/curator/amplitude.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/curator/jquery-ui.slider.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/studio/jquery.editable.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/studio/clipboard.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">var audio = '{{$audio->audio_url}}', public_url= '{{$public_url}}'</script>
<script src="{{ asset('js/studio/tool.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">$(function(){$(".studio-sidebar-item" ).addClass('mm-active').parent().addClass('mm-active');});</script>
@endpush