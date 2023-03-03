{{-- Extends layout --}}
@extends('layout.default')



{{-- Content --}}
@section('content')
            <!-- row -->
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-lg-12">
						<div class="row">
							<div class="col-xl-4">
                <div class="card bg-info">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M30.25 5.75H28.5V2.25C28.5 1.78587 28.3156 1.34075 27.9874 1.01256C27.6593 0.684374 27.2141 0.5 26.75 0.5C26.2859 0.5 25.8407 0.684374 25.5126 1.01256C25.1844 1.34075 25 1.78587 25 2.25V5.75H11V2.25C11 1.78587 10.8156 1.34075 10.4874 1.01256C10.1592 0.684374 9.71413 0.5 9.25 0.5C8.78587 0.5 8.34075 0.684374 8.01256 1.01256C7.68437 1.34075 7.5 1.78587 7.5 2.25V5.75H5.75C4.35761 5.75 3.02226 6.30312 2.03769 7.28769C1.05312 8.27226 0.5 9.60761 0.5 11V12.75H35.5V11C35.5 9.60761 34.9469 8.27226 33.9623 7.28769C32.9777 6.30312 31.6424 5.75 30.25 5.75Z" fill="white"></path>
                          <path d="M0.5 30.25C0.5 31.6424 1.05312 32.9777 2.03769 33.9623C3.02226 34.9469 4.35761 35.5 5.75 35.5H30.25C31.6424 35.5 32.9777 34.9469 33.9623 33.9623C34.9469 32.9777 35.5 31.6424 35.5 30.25V16.25H0.5V30.25Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Podcasts Claimed</p>
                        <span class="fs-48 text-white font-w600">{{$data['podcast_claimed']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4">
                <div class="card bg-info">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M30.25 5.75H28.5V2.25C28.5 1.78587 28.3156 1.34075 27.9874 1.01256C27.6593 0.684374 27.2141 0.5 26.75 0.5C26.2859 0.5 25.8407 0.684374 25.5126 1.01256C25.1844 1.34075 25 1.78587 25 2.25V5.75H11V2.25C11 1.78587 10.8156 1.34075 10.4874 1.01256C10.1592 0.684374 9.71413 0.5 9.25 0.5C8.78587 0.5 8.34075 0.684374 8.01256 1.01256C7.68437 1.34075 7.5 1.78587 7.5 2.25V5.75H5.75C4.35761 5.75 3.02226 6.30312 2.03769 7.28769C1.05312 8.27226 0.5 9.60761 0.5 11V12.75H35.5V11C35.5 9.60761 34.9469 8.27226 33.9623 7.28769C32.9777 6.30312 31.6424 5.75 30.25 5.75Z" fill="white"></path>
                          <path d="M0.5 30.25C0.5 31.6424 1.05312 32.9777 2.03769 33.9623C3.02226 34.9469 4.35761 35.5 5.75 35.5H30.25C31.6424 35.5 32.9777 34.9469 33.9623 33.9623C34.9469 32.9777 35.5 31.6424 35.5 30.25V16.25H0.5V30.25Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Total Episodes</p>
                        <span class="fs-48 text-white font-w600">{{$data['total_episodes']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-4">
                <div class="card bg-success">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M30.25 5.75H28.5V2.25C28.5 1.78587 28.3156 1.34075 27.9874 1.01256C27.6593 0.684374 27.2141 0.5 26.75 0.5C26.2859 0.5 25.8407 0.684374 25.5126 1.01256C25.1844 1.34075 25 1.78587 25 2.25V5.75H11V2.25C11 1.78587 10.8156 1.34075 10.4874 1.01256C10.1592 0.684374 9.71413 0.5 9.25 0.5C8.78587 0.5 8.34075 0.684374 8.01256 1.01256C7.68437 1.34075 7.5 1.78587 7.5 2.25V5.75H5.75C4.35761 5.75 3.02226 6.30312 2.03769 7.28769C1.05312 8.27226 0.5 9.60761 0.5 11V12.75H35.5V11C35.5 9.60761 34.9469 8.27226 33.9623 7.28769C32.9777 6.30312 31.6424 5.75 30.25 5.75Z" fill="white"></path>
                          <path d="M0.5 30.25C0.5 31.6424 1.05312 32.9777 2.03769 33.9623C3.02226 34.9469 4.35761 35.5 5.75 35.5H30.25C31.6424 35.5 32.9777 34.9469 33.9623 33.9623C34.9469 32.9777 35.5 31.6424 35.5 30.25V16.25H0.5V30.25Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Vizzy Curated</p>
                        <span class="fs-48 text-white font-w600">{{$data['total_vizzies']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- <div class="col-xl-6">
                <div class="card bg-success">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M30.25 5.75H28.5V2.25C28.5 1.78587 28.3156 1.34075 27.9874 1.01256C27.6593 0.684374 27.2141 0.5 26.75 0.5C26.2859 0.5 25.8407 0.684374 25.5126 1.01256C25.1844 1.34075 25 1.78587 25 2.25V5.75H11V2.25C11 1.78587 10.8156 1.34075 10.4874 1.01256C10.1592 0.684374 9.71413 0.5 9.25 0.5C8.78587 0.5 8.34075 0.684374 8.01256 1.01256C7.68437 1.34075 7.5 1.78587 7.5 2.25V5.75H5.75C4.35761 5.75 3.02226 6.30312 2.03769 7.28769C1.05312 8.27226 0.5 9.60761 0.5 11V12.75H35.5V11C35.5 9.60761 34.9469 8.27226 33.9623 7.28769C32.9777 6.30312 31.6424 5.75 30.25 5.75Z" fill="white"></path>
                          <path d="M0.5 30.25C0.5 31.6424 1.05312 32.9777 2.03769 33.9623C3.02226 34.9469 4.35761 35.5 5.75 35.5H30.25C31.6424 35.5 32.9777 34.9469 33.9623 33.9623C34.9469 32.9777 35.5 31.6424 35.5 30.25V16.25H0.5V30.25Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Vizzy Interactions</p>
                        <span class="fs-48 text-white font-w600">{{$data['vizzy_interactions']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
						</div>
					</div>
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
						<h4 class="fs-20 text-black mb-sm-4 mt-sm-0 mt-2  mb-2">Latest Vizzies</h4>
					</div>
				</div>

				<div class="row">
					@foreach ($data['latest_vizzies'] as $vizzy)
					<div class="col-xl-4 col-lg-6">
						<div class="card shadow_1">
							<div class="card-body">
								<div class="media mb-2">
                  <img class="mr-3" width="60" height="60" src="{{$vizzy->image }}" />
									<div class="media-body">
                    <a href="{{route('curator', ['podcast'=> $vizzy->podcast, 'guid' => $vizzy->episode_guid])}}">
                      <h6 class="fs-16 text-black font-w600">{!! $vizzy->podcast->title !!}</h6>
                      <span class="text-primary font-w500 d-block mb-3">{!! $vizzy->title !!}</span>
                    </a>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
						<h4 class="fs-20 text-black mb-sm-4 mt-sm-0 mt-2  mb-2">Latest Podcasts Claimed</h4>
					</div>
				</div>

				<div class="row">
					@foreach ($data['latest_podcasts'] as $podcast)
					<div class="col-xl-4 col-lg-6">
						<div class="card shadow_1">
							<div class="card-body">
                <a href="{{route('episodes', $podcast)}}">
								<div class="media mb-2">
									<div class="media-body">
										<p class="mb-1">{{$podcast->user->company}}</p>
										<h4 class="fs-20 text-black">{!!$podcast->title!!}</h4>
									</div>
									<img class="ml-3" width="60" height="60" src="{{$podcast->image}}" />
								</div>
								<span class="text-primary font-w500 d-block mb-3">{!!$podcast->categoriesName!!}</span>
								<p class="fs-14">{{Str::limit(strip_tags($podcast->description), 200, $end='...')}}</p>
								<!-- <div class="d-flex align-items-center mt-4"> -->
									<!-- <a href="javascript:void(0);" class="btn btn-primary light btn-rounded mr-auto">REMOTE</a> -->
									<!-- <span class="text-black font-w500">London, England</span> -->
								<!-- </div> -->
                </a>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
@endsection

@push('css')
@endpush

@push('footer-scripts')
@endpush