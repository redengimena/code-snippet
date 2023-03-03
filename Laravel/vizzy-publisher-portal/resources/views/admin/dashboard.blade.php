{{-- Extends layout --}}
@extends('layout.default')

@section('title')
Admin Dashboard
@endsection

{{-- Content --}}
@section('content')
            <!-- row -->
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-lg-12">
						<div class="row">
							<div class="col-xl-3">
                <div class="card bg-primary">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M15.1811 22.0083C15.065 21.9063 14.7968 21.6695 14.7015 21.5799C12.3755 19.3941 10.8517 15.9712 10.8517 12.1138C10.8517 5.37813 15.4868 0.0410156 21.001 0.0410156C26.5152 0.0410156 31.1503 5.37813 31.1503 12.1138C31.1503 15.9679 29.6292 19.3884 27.3094 21.5778C27.2118 21.6699 26.9384 21.9116 26.8238 22.0125L26.8139 22.1799C26.8789 23.1847 27.554 24.0553 28.5232 24.3626C35.7277 26.641 40.9507 32.0853 41.8276 38.538C41.9483 39.3988 41.6902 40.2696 41.1198 40.9254C40.5495 41.5813 39.723 41.9579 38.8541 41.9579C32.4956 41.9591 9.50672 41.9591 3.14818 41.9591C2.2787 41.9591 1.4518 41.5824 0.881242 40.9263C0.31068 40.2701 0.0523763 39.3989 0.172318 38.5437C1.05145 32.0851 6.27444 26.641 13.4777 24.3628C14.4504 24.0544 15.1263 23.1802 15.1885 22.1722L15.1811 22.0083Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Podcaster Accounts</p>
                        <span class="fs-48 text-white font-w600">{{$data['podcaster']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3">
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
              <div class="col-xl-3">
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
                        <p class="fs-18 text-white mb-2">Vizzys Started</p>
                        <span class="fs-48 text-white font-w600">{{$data['vizzy_started']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card bg-secondary">
                  <div class="card-body">
                    <div class="media align-items-center">
                      <span class="p-3 mr-3 border border-white rounded">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M30.25 5.75H28.5V2.25C28.5 1.78587 28.3156 1.34075 27.9874 1.01256C27.6593 0.684374 27.2141 0.5 26.75 0.5C26.2859 0.5 25.8407 0.684374 25.5126 1.01256C25.1844 1.34075 25 1.78587 25 2.25V5.75H11V2.25C11 1.78587 10.8156 1.34075 10.4874 1.01256C10.1592 0.684374 9.71413 0.5 9.25 0.5C8.78587 0.5 8.34075 0.684374 8.01256 1.01256C7.68437 1.34075 7.5 1.78587 7.5 2.25V5.75H5.75C4.35761 5.75 3.02226 6.30312 2.03769 7.28769C1.05312 8.27226 0.5 9.60761 0.5 11V12.75H35.5V11C35.5 9.60761 34.9469 8.27226 33.9623 7.28769C32.9777 6.30312 31.6424 5.75 30.25 5.75Z" fill="white"></path>
                          <path d="M0.5 30.25C0.5 31.6424 1.05312 32.9777 2.03769 33.9623C3.02226 34.9469 4.35761 35.5 5.75 35.5H30.25C31.6424 35.5 32.9777 34.9469 33.9623 33.9623C34.9469 32.9777 35.5 31.6424 35.5 30.25V16.25H0.5V30.25Z" fill="white"></path>
                        </svg>
                      </span>
                      <div class="media-body text-right">
                        <p class="fs-18 text-white mb-2">Vizzys Published</p>
                        <span class="fs-48 text-white font-w600">{{$data['vizzy_published']}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

						</div>
					</div>
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
            <h4 class="fs-20 text-black mb-sm-4 mt-sm-0 mt-2  mb-2">Vizzy App Usage Summary</h4>
					</div>
				</div>

        <div class="row">
          <div class="col-xl-4 col-lg-6">
            <div class="card shadow_1">
              <div class="card-header">
                <h4 class="card-title">Active Users</h4>
              </div>
							<div class="card-body">
                <canvas id="lineChart_activeUsers"></canvas>
              </div>
            </div>
          </div>

					<div class="col-xl-4 col-lg-6">
            <div class="card shadow_1">
              <div class="card-header">
                <h4 class="card-title">Total Plays</h4>
              </div>
							<div class="card-body">
                <canvas id="lineChart_totalPlays"></canvas>
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-6">
            <div class="card shadow_1">
              <div class="card-header">
                <h4 class="card-title">Vizzy Plays</h4>
              </div>
							<div class="card-body">
                <canvas id="lineChart_vizzyPlays"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
            <a href="#" class="btn btn-primary light btn-rounded mb-4">View More
              <svg class="ml-3" width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.5607 5.93941L18.2461 0.62482C17.9532 0.331898 17.5693 0.185461 17.1854 0.185461C16.8015 0.185461 16.4176 0.331898 16.1247 0.62482C15.539 1.21062 15.539 2.16035 16.1247 2.74615L18.8787 5.50005L1.5 5.50005C0.671578 5.50005 0 6.17163 0 7.00005C0 7.82848 0.671578 8.50005 1.5 8.50005L18.8787 8.50005L16.1247 11.254C15.539 11.8398 15.539 12.7895 16.1247 13.3753C16.7106 13.9611 17.6602 13.9611 18.2461 13.3753L23.5607 8.06069C24.1464 7.47495 24.1464 6.52516 23.5607 5.93941Z" fill="#620404"></path>
              </svg>
            </a>
					</div>
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
            <div class="d-sm-flex align-items-center mb-3 mt-sm-0 mt-2">
              <h4 class="fs-20 text-black mr-auto">Latest Vizzies</h4>
              <a href="{{ route('admin.vizzys.index') }}" class="btn btn-primary light btn-rounded">View More
                <svg class="ml-3" width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23.5607 5.93941L18.2461 0.62482C17.9532 0.331898 17.5693 0.185461 17.1854 0.185461C16.8015 0.185461 16.4176 0.331898 16.1247 0.62482C15.539 1.21062 15.539 2.16035 16.1247 2.74615L18.8787 5.50005L1.5 5.50005C0.671578 5.50005 0 6.17163 0 7.00005C0 7.82848 0.671578 8.50005 1.5 8.50005L18.8787 8.50005L16.1247 11.254C15.539 11.8398 15.539 12.7895 16.1247 13.3753C16.7106 13.9611 17.6602 13.9611 18.2461 13.3753L23.5607 8.06069C24.1464 7.47495 24.1464 6.52516 23.5607 5.93941Z" fill="#620404"></path>
                </svg>
              </a>
            </div>
					</div>
				</div>

				<div class="row">
					@foreach ($data['latest_vizzies'] as $vizzy)
					<div class="col-xl-4 col-lg-6">
            <a href="{{ route('curator', ['podcast' => $vizzy->podcast->id, 'guid' => $vizzy->episode_guid]) }}">
						<div class="card shadow_1">
							<div class="card-body">
                <div class="media mb-2">
                  <img class="mr-3" width="60" height="60" src="{{$vizzy->image }}" />
									<div class="media-body">
                    <h6 class="fs-16 text-black font-w600">{!! $vizzy->podcast->title !!}</h6>
										<span class="text-primary font-w500 d-block mb-3">{!! $vizzy->title !!}</span>
									</div>
								</div>
							</div>
						</div>
            </a>
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
								<div class="media mb-2">
									<div class="media-body">
										<p class="mb-1">{{$podcast->user->company}}</p>
										<h4 class="fs-20 text-black">{!!$podcast->title!!}</h4>
									</div>
									<img class="ml-3" width="60" height="60" src="{{$podcast->image}}" />
								</div>
								<span class="text-primary font-w500 d-block mb-3">{!!$podcast->categoriesName!!}</span>
								<p class="fs-14">{{Str::limit(strip_tags($podcast->description), 200, $end='...')}}</p>
								<div class="d-flex align-items-center mt-4">
									<a href="{{ route('episodes', $podcast->id) }}" class="btn btn-primary light btn-rounded mr-auto">More</a>
									<!-- <span class="text-black font-w500">London, England</span> -->
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
            <div class="d-sm-flex align-items-center mb-3 mt-sm-0 mt-2">
              <h4 class="fs-20 text-black mr-auto">Latest Podcasters</h4>
              <a href="{{ route('admin.users.index') }}" class="btn btn-primary light btn-rounded">View More
                <svg class="ml-3" width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23.5607 5.93941L18.2461 0.62482C17.9532 0.331898 17.5693 0.185461 17.1854 0.185461C16.8015 0.185461 16.4176 0.331898 16.1247 0.62482C15.539 1.21062 15.539 2.16035 16.1247 2.74615L18.8787 5.50005L1.5 5.50005C0.671578 5.50005 0 6.17163 0 7.00005C0 7.82848 0.671578 8.50005 1.5 8.50005L18.8787 8.50005L16.1247 11.254C15.539 11.8398 15.539 12.7895 16.1247 13.3753C16.7106 13.9611 17.6602 13.9611 18.2461 13.3753L23.5607 8.06069C24.1464 7.47495 24.1464 6.52516 23.5607 5.93941Z" fill="#620404"></path>
                </svg>
              </a>
            </div>
					</div>
				</div>

        <div class="row">
					<div class="col-xl-12 col-lg-12">
						<div class="card shadow_1">
							<div class="card-body">
                <div class="table-responsive">
                  <table class="table table-responsive-md">
                    <thead>
                      <tr>
                        <th>Podcaster</th>
                        <th>Contact Name</th>
                        <th>Date Added</th>
                        <th>Status</th>
                        <th>Podcast Claimed</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($data['latest_podcasters'] as $user)
                      <tr>
                        <td>{{ $user->company }}</td>
                        <td>{{ $user->fullname }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td><button class="btn btn-xs btn-rounded btn-outline-dark mr-3 ml-auto">{{ $user->status }}</button></td>
                        <td>{{ $user->podcasts->count() }}</td>
                        <td>
                          <div class="d-flex">
                              <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-pencil"></i></a>
                              @canImpersonate($guard = null)
                              @canBeImpersonated($user, $guard = null)
                                  <a href="{{ route('impersonate', $user->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1" title="Login as user"><i class="fa fa-sign-in "></i></a>
                              @endCanBeImpersonated
                              @endCanImpersonate
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
							</div>
						</div>
					</div>
				</div>

			</div>
@endsection

@push('css')
<!-- <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}"  rel="stylesheet"> -->
<!-- <link href="{{ asset('vendor/jqvmap/css/jqvmap.min.css') }}"  rel="stylesheet"> -->
<link href="{{ asset('vendor/chartist/css/chartist.min.css') }}"  rel="stylesheet">
<!-- <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}"  rel="stylesheet"> -->
<!-- <link href="https://cdn.lineicons.com/2.0/LineIcons.css"  rel="stylesheet"> -->
@endpush

@push('footer-scripts')
<!-- <script src="{{ asset('js/deznav-init.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" type="text/javascript"></script> -->
<script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ asset('vendor/peity/jquery.peity.min.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ asset('vendor/apexchart/apexchart.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ asset('vendor/nouislider/nouislider.min.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ asset('vendor/wnumb/wNumb.js') }}" type="text/javascript"></script> -->
<script type="text/javascript">
const activeUsers=JSON.parse('{!! $data['usage_stats']['activeUsers'] !!}');
const totalPlays=JSON.parse('{!! $data['usage_stats']['totalPlays'] !!}');
const vizzyPlays=JSON.parse('{!! $data['usage_stats']['vizzyPlays'] !!}');
</script>
<script src="{{ asset('js/admin/dashboard.min.js') }}" type="text/javascript"></script>
@endpush