{{-- Extends layout --}}
@extends('layout.default')

@section('title')
Your Podcast Studio
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
                    <a  class="btn btn-primary upload-podcast-button">Upload audio file</a>
                </div>
              </div>
            </div>
          </div>

          </div>





          <div class="row homepage-studio upload-podcast-component upload-podcast-component-hide">
            <div class="col-xl-12 col-lg-12">
              <div class="upload-container">
                <form method="post" action="{{ route('studio-upload') }}" enctype="multipart/form-data"
                  class="dropzone" id="dropzone">
                  @csrf
                </form>

                <div class="with-meta text-center d-none">
                  <h2>Podcast Chapters</h2>
                  <p>
                    This file has existing chapters. Do you want to keep it?<br />
                    This will not change the original file.
                  </p>
                  <div class="meta-buttons">
                    <a href="" class="btn btn-primary">Keep metadata</a>
                    <a href="" class="btn btn-default">Delete metadata</a>
                  </div> 
                </div>

              </div>
            </div>
            @if(count(Auth::user()->audios)==0)
            <div id="simpleModal" class="modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="row">
                      <div class="col-xl-12 col-lg-12">
                        <div class="upload-container">
                          <form method="post" action="{{ route('studio-upload') }}" enctype="multipart/form-data"
                            class="dropzone" id="dropzone">
                            @csrf
                          </form>
                          <div class="with-meta text-center d-none">
                            <h2>Podcast Chapters</h2>
                            <p>
                              This file has existing chapters. Do you want to keep it?<br />
                              This will not change the original file.
                            </p>
                            <div class="meta-buttons">
                              <a href="" class="btn btn-primary">Keep metadata</a>
                              <a href="" class="btn btn-default">Delete metadata</a>
                            </div> 
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
          </div>     
          @endif
        </div>   
      </div>
     

      <div class="container-fluid studio-index-table-container">
        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-lg-12">
              <div class="row">
                <div class="col-xl-12">
                  <h3 class="fs-32 text-black font-w700 mr-auto mb-2 pr-3">Podcasts uploaded</h3>

                  @if(count(Auth::user()->audios)>0)
                  <div class="table-responsive">
                      <table id="uploadedAudio" class="display table-responsive-md">
                          <thead>
                              <tr>
                                  <th scope="col">Podcast</th>
                                  <th scope="col">Title</th>
                                  <th scope="col">Created</th>
                                  <th scope="col">Last updated</th>
                                  <th scope="col">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach (Auth::user()->audios as $audio)
                              <tr>
                                  <td>{{ $audio->filename }}</td>
                                  <td>{{ $audio->name }}</td>
                                  <td>{{ $audio->created_at->format('Y-m-d H:i') }}</td>
                                  <td>{{ $audio->updated_at->format('Y-m-d H:i') }}</td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <a href="{{ route('studio-tool', $audio->slug()) }}" class="btn btn-primary btn-rounded btn-sm mr-4">Edit</a>
                                          <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" ><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                              <li class="dropdown-item">
                                                <form action="{{ route('studio-delete', $audio->slug()) }}" method="post">
                                                    @csrf
                                                    <input class="audio-delete" type="submit" value="Delete" />
                                                </form>
                                              </li>
                                            </ul>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  @else 
                  <div>
                    <p class="fs-18 text-black font-w400 mr-auto my-4 pr-3">You havn’t uploaded any podcast episodes yet.</p>
                  </div>
                  @endif
                  
                  

                </div>
              </div>
            </div>
          </div>

        </div>
@endsection

@push('css')
<link href="{{ asset('/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>
<script type="text/javascript">
    $('#uploadedAudio').dataTable({"aaSorting": []});
    let button = '<a class="btn btn-primary btn-rounded">Select a file</a>'
    Dropzone.options.dropzone =
    {
        dictDefaultMessage: '<h2>Upload a podcast file</h2><p>Drop .mp3 of your podcast recording here or click to select a file from your PC</p>'+ button,
        maxFilesize: 100,
        maxFiles: 1,
        acceptedFiles: ".mp3",
        timeout: 60000,
        success: function (file, response) {
            this.removeFile(file)
            if (!response.meta) {
              window.location.href = response.edit_url;
            } else {
              $('.dropzone').addClass('d-none');
              $('.with-meta').removeClass('d-none');
              $('.with-meta .btn-primary').attr('href', response.edit_url);
              $('.with-meta .btn-default').attr('href', response.edit_url + '?fresh=1');
            }
        },
        error: function (file, response) {
            return false;
        }
    };
</script>

  <script type="text/javascript">
    window.onload = function () {
      let query = window.location.search.replace(/^\?/, "");

      let uploadPodcastComponent = document.querySelector('.upload-podcast-component');
      let studioTwoButtons = document.querySelector('.two-buttons');
      let uploadButtons = document.querySelector('.upload-podcast-button');
  
      if ( query == "upload") {
        showUploadContainer();
      }
    };
    
    
    function OpenBootstrapPopup() {
        $("#simpleModal").modal('show');
    }

    let uploadPodcastComponent = document.querySelector('.upload-podcast-component');
    let studioTwoButtons = document.querySelector('.two-buttons');
    let uploadButtons = document.querySelector('.upload-podcast-button');

    function showUploadContainer() {
      studioTwoButtons.classList.add('two-buttons-hide');
      uploadPodcastComponent.classList.remove('upload-podcast-component-hide');
      uploadPodcastComponent.classList.add('upload-podcast-component-show');
      OpenBootstrapPopup();
    }

    uploadButtons.addEventListener('click', e=>{
      e.preventDefault();
      showUploadContainer();
    })

    $(function() {
        $( ".studio-sidebar-item" ).addClass('mm-active').parent().addClass('mm-active');
    });
</script>

@endpush