{{-- Extends layout --}}
@extends('layout.default')

@section('title')
My Podcasts
@endsection

{{-- Content --}}
@section('content')
      <!-- row -->
      <div id="app" v-cloak class="container-fluid">
        <div class="d-flex flex-wrap mb-4" style="align-items:center;justify-content:space-between;">
          <div class="page-titles mb-0">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('vizzies') }}">My Vizzy's</a></li>
              <li class="breadcrumb-item text-primary font-w600">@if ($episode){!! $episode->getTitle() !!}@endif</li>
            </ol>
          </div>
        </div>

        <div class="d-flex flex-wrap mb-4" style="align-items:center;justify-content:space-between;">
          <div class="font-weight-bold mb-0">
            Status: <span id="vizzy_status" class="mr-3">{{ $vizzy_status }}</span>
            @if ($vizzy && $vizzy->status == 'pending')
            @can('approve-vizzy')
            <form action="{{route('admin.vizzys.approve', $vizzy)}}" method="POST" class="d-inline">
               @csrf<input type="submit" class="btn btn-xs btn-success btn-rounded mr-1" value="Approve" />
            </form>
            <form action="{{route('admin.vizzys.reject', $vizzy)}}" method="POST" class="d-inline">
               @csrf<input type="submit" class="btn btn-xs btn-danger btn-rounded" value="Reject" />
            </form>
            @endcan
            @endif
          </div>
          <div class="d-sm-flex d-block">
            @if ($vizzy && $vizzy_status != 'Published' && $vizzy_status != 'Pending Approval' )
            <button class="btn btn-danger btn-rounded mr-3" type="button" data-toggle="modal" data-target="#deleteVizzyModal">Delete Vizzy</button>
            @endif
            <a href="{{ route('episodes', $podcast->id) }}" class="btn btn-dark btn-rounded">Cancel</a>
            <form id="saveForm" action="{{ route('curator-save', $podcast->id) }}" method="POST"> @csrf
              @if($episode)
              <input type="hidden" name="guid" value="{{ $episode->getGuid() }}" />
              @endif
              @if (old('cards'))
              <input type="hidden" name="cards" value="{{ old('cards') }}" />
              @else
              <input type="hidden" name="cards" value="{{ $cards }}" />
              @endif
              <input type="hidden" name="vizzy_image" value="{{ $vizzy_image }}" />
              @if ($vizzy_status != 'Published' && $vizzy_status != 'Pending Approval' )
              <button class="btn btn-primary btn-rounded ml-3" type="submit">Save Changes</button>
              @endif
            </form>
            @if ($cards)
            @if ($button_status == 'Publish')
            <form id="publishForm" action="{{ route('curator-publish', $podcast->id) }}" method="POST"> @csrf
              @if($episode)
              <input type="hidden" name="guid" value="{{ $episode->getGuid() }}" />
              @endif
              <input class="btn btn-info btn-rounded ml-3" type="submit" value="{{ $button_status }}" />
            </form>
            @endif
            @if ($button_status == 'Un-Publish')
            <form id="unpublishForm" action="{{ route('curator-unpublish', $podcast->id) }}" method="POST"> @csrf
              <input type="hidden" name="guid" value="{{ $episode->getGuid() }}" />
              <input class="btn btn-info btn-rounded ml-3" type="submit" value="{{ $button_status }}" />
            </form>
            @endif
            @endif
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-xl-12">
            <div class="accordion__header rounded-top bg-primary text-white" data-toggle="collapse" data-target="#details">
                <span class="accordion__header--text">{!! $podcast->title !!} >> @if($episode){!! $episode->getTitle() !!}@endif</span>
                <span class="accordion__header--indicator"></span>
            </div>
            <div id="details" class="collapse accordion__body rounded-bottom bg-white show">
              <div class="accordion__body--text">
                <div class="row">
                  <div class="col-sm-2">
                    <div class="font-weight-bold mb-2">Image</div>
                    <div class="text-center media-browser-target">
                        <div class="img-preview-holder mb-2">
                          <span class="img-meta">882 x 543</span>
                          <img src="{{ $vizzy_image }}" class="card-image img-preview">
                        </div>
                        @if ($vizzy_status != 'Published' && $vizzy_status != 'Pending Approval')
                        <button class="btn btn-primary btn-xxs btn-media-manager">Select</button>
                        @endif
                        <input type="hidden" name="vizzy_cover" class="@error('vizzy_image') is-invalid @enderror" value="{{ $vizzy_image }}"/>
                        @error('vizzy_image')
                          <span class="invalid-feedback animated fadeInUp" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                  </div>
                  @if($episode)
                  <div class="col-sm-1"><div class="font-weight-bold mb-2">Date</div><div>{{ $episode->getPublishedDate()->format('j F') }}</div></div>
                  <div class="col-sm-1"><div class="font-weight-bold mb-2">Length</div><div>{{ Str::duration($episode->getDuration()) }}</div></div>
                  <div class="col-sm-8"><div class="font-weight-bold mb-2">Description</div><div>{!! Str::limit(strip_tags($episode->getDescription()), 2000, $end='...') !!}</div></div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header">
                <h4 class="mb-0">Preview</h4>
              </div>
              <div class="card-body">
                <div class="main-panel">
                  <div class="media">
                    <div class="media-body">
                      <div class="row">
                        <div class="col-lg-7">
                          <div id="cardDetailPreview" class="d-none dz-scroll height400">
                            <div class="row mb-2">
                              <div class="col-sm-4 col-form-label font-w700">Title:</div>
                              <div class="col-sm-8 col-form-label vcard-title"></div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-sm-4 col-form-label font-w700">Description:</div>
                              <div class="col-sm-8 col-form-label vcard-description"></div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-sm-4 col-form-label font-w700">Time:</div>
                              <div class="col-sm-8 col-form-label vcard-time"></div>
                            </div>
                            <div id="preview-interactions">
                                <div class="default-tab mt-4 mb-4">
                                    <ul class="nav nav-tabs d-none" role="tablist">
                                        <li class="nav-item d-none" id="info-preview-tab">
                                            <a class="nav-link" data-toggle="tab" href="#info-preview"><img src="{{$s3_url}}info.png" width="30" height="30" class="d-inline"/> Information</a>
                                        </li>
                                        <li class="nav-item d-none" id="social-preview-tab">
                                            <a class="nav-link" data-toggle="tab" href="#social-preview"><img src="{{$s3_url}}social.png" width="30" height="30" class="d-inline"/> Social</a>
                                        </li>
                                        <li class="nav-item d-none" id="product-preview-tab">
                                            <a class="nav-link" data-toggle="tab" href="#product-preview"><img src="{{$s3_url}}product.png" width="30" height="30" class="d-inline"/> Product</a>
                                        </li>
                                        <li class="nav-item d-none" id="web-preview-tab">
                                            <a class="nav-link" data-toggle="tab" href="#web-preview"><img src="{{$s3_url}}web.png" width="30" height="30" class="d-inline"/> Web Link</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade pt-4" id="info-preview"></div>
                                        <div class="tab-pane fade pt-4" id="social-preview"></div>
                                        <div class="tab-pane fade pt-4" id="product-preview"></div>
                                        <div class="tab-pane fade pt-4" id="web-preview"></div>
                                    </div>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-5 text-center text-lg-right">
                          <div class="img-frame">
                            <div class="vizzy-card-tray">
                                <img src="{{$s3_url}}scissor.png" />
                                <img src="{{$s3_url}}share.png" />
                            </div>
                            <div class="vizzy-card-image">
                              @if ($episode && $episode->getArtwork())
                              <img src="{{$episode->getArtwork()->getUri()}}" class="episode-img">
                              @else
                              <img src="{{$rss->getArtwork()->getUri()}}" class="episode-img">
                              @endif
                              <img src="" class="vizzy-card-img">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card custom-card">
              <div class="card-header">
                <h4 class="mb-0">Carousel Cue List</h4>
                @if ($vizzy_status != 'Published' && $vizzy_status != 'Pending Approval')
                <button class="btn btn-primary btn-xxs add-card">Add</button>
                @endif
              </div>
              <div class="card-body">
                <div id="cueList" class="dz-scroll height400">
                    <ul class="cue-list-panel list-unstyled"></ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-body pb-2 pt-4">

                <div id="single-song-player">
                    <h4 class="mb-0">Player</h4>
                    <div class="control-container">
                      <div class="amplitude-play-pause" id="play-pause"><span class="mdi mdi-play"></span><span class="mdi mdi-pause"></span></div>
                    </div>

                    <div id="time-container">
                      <div id="progress-container">
                        <progress id="song-played-progress" class="amplitude-song-played-progress"></progress>
                        <progress id="song-buffered-progress" class="amplitude-buffered-progress" value="0"></progress>
                        <div class="song-navigation">
                          <div class="timeline-thumbnails"></div>
                          <input type="range" class="range amplitude-song-slider" step=".1"/>
                          @if ($vizzy_status != 'Published' && $vizzy_status != 'Pending Approval')
                          <output class="bubble add-card"></output>
                          @else
                          <output class="bubble d-none"></output>
                          @endif
                        </div>
                      </div>
                      <span class="current-time">
                        <span class="amplitude-current-time"></span>
                      </span>
                      <span class="duration">
                        -<span class="amplitude-time-remaining"></span>
                      </span>
                    </div>

                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="addCardModal">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="accordion" class="accordion accordion-active-header">
                            <div class="accordion__item">
                                <div class="accordion__header rounded-lg">
                                    <span class="accordion__header--text">Vizzy Card Details</span>
                                    <span class="accordion__header--close"></span>
                                </div>
                                <div id="cardDetails" class="collapse accordion__body show" data-parent="#accordion">
                                    <div class="accordion__body--text">
                                        <div class="row">
                                          <div class="col-lg-4">
                                            <h4 class="mb-4">Step 1:</h4>
                                            <input class="form-control" type="text" name="vcard_title" placeholder="Enter Card/Chapter Title"/>
                                            <div id="vcard_title-error" class="invalid-feedback animated fadeInUp">Required</div>
                                            <textarea class="form-control mt-4 mb-4" rows="8" name="vcard_content" placeholder="Optional - Add description, for admin purposes only."></textarea>
                                          </div>
                                          <div class="col-lg-4">
                                            <h4 class="mb-4">Step 2:</h4>
                                            <span class="small mb-4">The start and end time show when this Vizzy Card will show during the audio.</span>
                                            <div class="row mb-4">
                                              <div class="col-lg-6">
                                                <div class="rounded-top bg-primary text-white p-1 text-center">
                                                  <span class="font-w400 small">Start time</span>
                                                </div>
                                                <input class="form-control rounded-bottom text-center" type="text" name="start_time" />
                                                <div id="start_time-error" class="invalid-feedback animated fadeInUp"></div>
                                              </div>
                                            </div>
                                            <div class="row mb-4">
                                              <div class="col-lg-6">
                                                <div class="rounded-top bg-primary text-white p-1 text-center">
                                                  <span class="font-w400 small">End time</span>
                                                </div>
                                                <input class="form-control rounded-bottom text-center" type="text" name="end_time" />
                                                <div id="end_time-error" class="invalid-feedback animated fadeInUp"></div>
                                              </div>
                                              <div class="col-lg-6">
                                                <span class="small mb-4">Leave end time empty for card to remain visible until the next card/chapter point</span>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-lg-4">
                                            <h4 class="mb-4">Step 3:</h4>
                                            <span class="small mb-3">Add image for your Vizzy Card, which will show for the duration of this chapter.</span>
                                            <div class="text-center media-browser-target">
                                                <input type="hidden" name="vcard_cover" value=""/>
                                                <div class="img-preview-holder mb-2">
                                                  <span class="img-meta">882 x 1300</span>
                                                  <img src="" class="card-image img-preview">
                                                </div>
                                                <button class="btn btn-primary btn-xxs mb-4 btn-media-manager">Select Image</button>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row mt-4"><div class="col-sm-12">
                                          <div class="text-right">
                                            <button class="btn btn-primary btn-sm btn-next" disabled="disabled">Next</button>
                                          </div>
                                        </div></div>
                                    </div>

                                </div>
                            </div>
                            <div class="accordion__item">
                                <div class="accordion__header rounded-lg collapsed">
                                    <span class="accordion__header--text">Engagement Information</span>
                                    <span class="accordion__header--indicator"></span>
                                </div>
                                <div id="trayDetails" class="collapse accordion__body" data-parent="#accordion">
                                    <div class="accordion__body--text ">
                                        <p>Up to 3 different interaction types can be added per card only</p>

                                        <div class="default-tab mb-4">
                                            <ul class="nav nav-tabs nav-fill" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#info"><img src="{{$s3_url}}info.png" width="30" height="30" class="d-inline mr-2"/> Information</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#social"><img src="{{$s3_url}}social.png" width="30" height="30" class="d-inline mr-2"/> Social</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#product"><img src="{{$s3_url}}product.png" width="30" height="30" class="d-inline mr-2"/> Product</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#web"><img src="{{$s3_url}}web.png" width="30" height="30" class="d-inline mr-2"/> Web Link</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="info">
                                                    <div id="info-form" class="interaction-form mb-4">
                                                        <div class="row pt-4">
                                                            <div class="col-lg-4">
                                                                <input class="form-control mb-4" name="info_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                                <div class="text-center media-browser-target">
                                                                    <div class="interaction-img-preview-holder mb-2">
                                                                      <span class="img-meta">882 x 625</span>
                                                                      <img src="" class="img-preview">
                                                                    </div>
                                                                    <input type="hidden" name="info_image" value=""/>
                                                                    <button class="btn btn-primary btn-xxs mb-4 btn-media-manager">Select Image</button>
                                                                    <button class="btn btn-outline-primary btn-xxs mb-4 btn-media-remove d-none">Delete</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <textarea class="form-control mb-4" rows="10" name="info_content" placeholder="Enter the information copy here. 350 characters max." maxlength="350"></textarea>
                                                                Max 350 characters
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="social">
                                                    <div id="social-template" class="d-none">
                                                        <div class="social-group mb-4">
                                                            <div class="row pt-4 mb-2">
                                                                <div class="col-lg-4">
                                                                    <input class="form-control" name="social_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                  <p class="small">Give your link group a heading. For instance you might create a link group for a guest that is mentioned and want to add both their Twitter and Instagram handles, so call the link group their name.</p>
                                                                </div>
                                                            </div>
                                                            <div class="social-links">
                                                                <div class="social-link mb-2">
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <select name="social_type" class="form-control">
                                                                                <option value="" selected>Select social media type</option>
                                                                                <option value="facebook">Facebook</option>
                                                                                <option value="instagram">Instagram</option>
                                                                                <option value="twitter">Twitter</option>
                                                                                <option value="linkedin">LinkedIn</option>
                                                                                <option value="youtube">YouTube</option>
                                                                                <option value="tiktok">TikTok</option>
                                                                                <option value="pinterest">Pinterest</option>
                                                                                <option value="snapchat">SnapChat</option>
                                                                                <option value="github">Github</option>
                                                                                <option value="twitch">Twitch</option>
                                                                                <option value="reddit">Reddit</option>
                                                                                <option value="other">Other</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-8">
                                                                            <input class="form-control" name="social_url" placeholder="Enter Social Media handle/URL"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <a href="#" class="btn-add-social-link"><i class="fa fa-plus"></i> Add another link to this group (max 4 links per group)</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="social-form" class="interaction-form mb-4">
                                                        <div class="social-group mb-4">
                                                            <div class="row pt-4 mb-2">
                                                                <div class="col-lg-4">
                                                                    <input class="form-control" name="social_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                  <p class="small">Give your link group a heading. For instance you might create a link group for a guest that is mentioned and want to add both their Twitter and Instagram handles, so call the link group their name.</p>
                                                                </div>
                                                            </div>
                                                            <div class="social-links">
                                                                <div class="social-link mb-2">
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <select name="social_type" class="form-control">
                                                                                <option value="" selected>Select social media type</option>
                                                                                <option value="facebook">Facebook</option>
                                                                                <option value="instagram">Instagram</option>
                                                                                <option value="twitter">Twitter</option>
                                                                                <option value="linkedin">LinkedIn</option>
                                                                                <option value="youtube">YouTube</option>
                                                                                <option value="tiktok">TikTok</option>
                                                                                <option value="pinterest">Pinterest</option>
                                                                                <option value="snapchat">SnapChat</option>
                                                                                <option value="github">Github</option>
                                                                                <option value="twitch">Twitch</option>
                                                                                <option value="reddit">Reddit</option>
                                                                                <option value="other">Other</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-8">
                                                                            <input class="form-control" name="social_url" placeholder="Enter Social Media handle/URL"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <a href="#" class="btn-add-social-link"><i class="fa fa-plus"></i> Add another link to this group (max 4 links per group)</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="text-right">
                                                              <button class="btn btn-primary btn-sm btn-add-social">Add another group</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="product">
                                                    <div id="product-form" class="interaction-form mb-4">
                                                        <div class="row pt-4 mb-4">
                                                            <div class="col-lg-4">
                                                                <select id="product-type" name="product_type" class="form-control">
                                                                    <option value="" selected>Select Product Type</option>
                                                                    <option value="ticket">Ticket</option>
                                                                    <option value="book">Book</option>
                                                                    <option value="subscription-xbox">Subscription - Xbox</option>
                                                                    <option value="subscription-youtube-music">Subscription - YouTube Music</option>
                                                                    <option value="subscription-apple-tv">Subscription - Apple TV</option>
                                                                    <option value="subscription-adobe-photoshop">Subscription - Adobe Photoshop</option>
                                                                    <option value="subscription-amazon-music">Subscription - Amazon Music</option>
                                                                    <option value="subscription-amazon-prime-video">Subscription - Amazon Prime Video</option>
                                                                    <option value="subscription-audible">Subscription - Audible</option>
                                                                    <option value="subscription-hulu">Subscription - Hulu</option>
                                                                    <option value="subscription-netflix">Subscription - Netflix</option>
                                                                    <option value="subscription-robinhood">Subscription - Robinhood</option>
                                                                    <option value="subscription-showtime">Subscription - Showtime</option>
                                                                    <option value="subscription-spotify">Subscription - Spotify</option>
                                                                    <option value="subscription-stan">Subscription - Stan</option>
                                                                    <option value="subscription-canva">Subscription - Canva</option>
                                                                    <option value="subscription-google">Subscription - Google</option>
                                                                    <option value="subscription-hbo">Subscription - HBO</option>
                                                                    <option value="subscription-other">Subscription - Other</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-4 mb-2">
                                                                <input class="form-control" name="product_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                            </div>
                                                            <div class="col-lg-8 mb-2">
                                                              <p class="small">This should be the name of your product. For instance, if it is a book it would be the title, if it was a ticket, it would be the event name, if it was a subscription it would be the product name.</p>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-4">
                                                            <div class="col-lg-4 mb-2">
                                                                <div class="text-center media-browser-target">
                                                                    <div class="interaction-img-preview-holder mb-2">
                                                                      <span class="img-meta">882 x 625</span>
                                                                      <img src="" class="img-preview">
                                                                    </div>
                                                                    <input type="hidden" name="product_image" value=""/>
                                                                    <button class="btn btn-primary btn-xxs mb-4 btn-media-manager">Select Image</button>
                                                                    <button class="btn btn-outline-primary btn-xxs mb-4 btn-media-remove d-none">Delete</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8 mb-2">
                                                                <textarea class="form-control" rows="10" name="product_content" placeholder="Include some short copy here to describe your product and give listeners a reason to click through." maxlength="350"></textarea>
                                                                Max 350 characters
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <input class="form-control" name="product_url" placeholder="Enter URL"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="web">
                                                    <div id="web-template" class="d-none">
                                                        <div class="web-group mb-4">
                                                            <div class="row pt-4">
                                                                <div class="col-lg-4 mb-2">
                                                                    <input class="form-control" name="web_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                                </div>
                                                                <div class="col-lg-8 mb-2">
                                                                  <p class="small">Give the topic you are linking to a heading. It could be current affair, something in the news, a moment in history, an artist or artwork, a fitness program, a film, etc.</p>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-lg-4 mb-2">
                                                                <div class="text-center media-browser-target">
                                                                    <div class="interaction-img-preview-holder mb-2">
                                                                      <span class="img-meta">882 x 625</span>
                                                                      <img src="" class="img-preview">
                                                                    </div>
                                                                    <input type="hidden" name="web_image" value=""/>
                                                                    <button class="btn btn-primary btn-xxs mb-4 btn-media-manager">Select Image</button>
                                                                    <button class="btn btn-outline-primary btn-xxs mb-4 btn-media-remove d-none">Delete</button>
                                                                </div>
                                                                </div>
                                                                <div class="col-lg-8 mb-2">
                                                                    <textarea class="form-control" rows="10" name="web_content" placeholder="Include some short copy here to describe your topic and give listeners a reason to click through." maxlength="300"></textarea>
                                                                    Max 350 characters
                                                                </div>
                                                            </div>
                                                            <div class="web-links">
                                                                <div class="web-link">
                                                                    <div class="row mb-2">
                                                                        <div class="col-lg-4">
                                                                            <select id="web-type" name="web_type" class="form-control">
                                                                                <option value="" selected>Select link type</option>
                                                                                <option value="wikipedia">Wikipedia</option>
                                                                                <option value="amazon">Amazon</option>
                                                                                <option value="airbnb">AirBnb</option>
                                                                                <option value="booking-com">Booking.com</option>
                                                                                <option value="github">Github</option>
                                                                                <option value="imdb">IMdB</option>
                                                                                <option value="shop">Shop</option>
                                                                                <option value="other">Other</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-8">
                                                                            <input class="form-control" name="web_url" placeholder="Enter URL"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="web-form" class="interaction-form mb-4">
                                                        <div class="web-group mb-4">
                                                            <div class="row pt-4">
                                                                <div class="col-lg-4 mb-2">
                                                                    <input class="form-control" name="web_title" type="text" placeholder="Add a heading here" maxlength="30"/>
                                                                </div>
                                                                <div class="col-lg-8 mb-2">
                                                                  <p class="small">Give the topic you are linking to a heading. It could be current affair, something in the news, a moment in history, an artist or artwork, a fitness program, a film, etc.</p>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-lg-4 mb-2">
                                                                <div class="text-center media-browser-target">
                                                                    <div class="interaction-img-preview-holder mb-2">
                                                                      <span class="img-meta">882 x 625</span>
                                                                      <img src="" class="img-preview">
                                                                    </div>
                                                                    <input type="hidden" name="web_image" value=""/>
                                                                    <button class="btn btn-primary btn-xxs mb-4 btn-media-manager">Select Image</button>
                                                                    <button class="btn btn-outline-primary btn-xxs mb-2 btn-media-remove d-none">Delete</button>
                                                                </div>
                                                                </div>
                                                                <div class="col-lg-8 mb-2">
                                                                    <textarea class="form-control" rows="10" name="web_content" placeholder="Include some short copy here to describe your topic and give listeners a reason to click through." maxlength="300"></textarea>
                                                                </div>

                                                            </div>
                                                            <div class="web-links">
                                                                <div class="web-link">
                                                                    <div class="row mb-2">
                                                                        <div class="col-lg-4">
                                                                            <select id="web-type" name="web_type" class="form-control">
                                                                                <option value="" selected>Select link type</option>
                                                                                <option value="wikipedia">Wikipedia</option>
                                                                                <option value="amazon">Amazon</option>
                                                                                <option value="airbnb">AirBnb</option>
                                                                                <option value="booking-com">Booking.com</option>
                                                                                <option value="github">Github</option>
                                                                                <option value="imdb">IMdB</option>
                                                                                <option value="shop">Shop</option>
                                                                                <option value="other">Other</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-8">
                                                                            <input class="form-control" name="web_url" placeholder="Enter URL"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-buttons text-right">
                                          <button class="btn btn-primary btn-sm btn-prev pull-left"><< Edit Card Details</button>
                                          <button class="btn btn-primary btn-sm btn-save">I'm Finish with this Card</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCardModal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">Are you sure you want to delete this card?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-xs light" data-dismiss="modal">Keep</button>
                        <button type="button" class="btn btn-primary btn-xs">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($vizzy && $vizzy_status != 'Published' && $vizzy_status != 'Pending Approval' )
        <div class="modal fade" id="deleteVizzyModal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">Are you sure you want to delete this Vizzy Episode?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-xs light" data-dismiss="modal">Keep</button>
                        <form id="deleteForm" action="{{ route('vizzy-delete', $vizzy->id) }}" method="POST"> @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-primary btn-xs">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Media Manager -->
        <vizzy-image-field inline-template>
            <div class="media-library-container">
                <div v-if="inputName">
                  @include('MediaManager::extras.modal',[
                    'select_button' => true,
                    'restrict' => [
                      'path' => 'uploads/' . Auth::user()->id,
                      'uploadSize' => 1
                  ]])
                </div>
                <media-modal item="cover" :name="inputName"></media-modal>
                <input id="media_manager_output" class="d-none" type="text" name="media_manager_cover" :value="cover" ref="input"/>
                <button id="media_manager" class="d-none" @click="toggleModalFor('cover')"></button>
            </div>
        </vizzy-image-field>
        <!-- End Media Manager -->


      </div>

@endsection

@push('css')
<link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}"  rel="stylesheet">
<link href="{{ asset('css/media.css') }}"  rel="stylesheet">
<link href="{{ asset('assets/vendor/MediaManager/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/curator.css') }}"  rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/camanjs/4.1.2/caman.full.min.js"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/curator/amplitude.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/curator/jquery-ui.slider.min.js') }}" type="text/javascript"></script>
@if($episode)
<script type="text/javascript">var audio = '{{$audio_url}}';var s3_url='{{$s3_url}}';</script>
@else
<script type="text/javascript">var audio = '';var s3_url='{{$s3_url}}';</script>
@endif
<script src="{{ asset('js/curator/curator.min.js') }}" type="text/javascript"></script>
@endpush