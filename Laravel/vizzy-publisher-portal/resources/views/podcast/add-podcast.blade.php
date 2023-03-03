{{-- Extends layout --}}
@extends('layout.default')


@section('title')
My Podcasts
@endsection

{{-- Content --}}
@section('content')

			<div class="container-fluid">
				<div class="page-titles">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="/">Home</a></li>
						<li class="breadcrumb-item"><a href="{{ route('podcasts') }}">My Shows</a></li>
						<li class="breadcrumb-item active">Add Show</li>
					</ol>
				</div>
				<!-- row -->
				<div class="row">
					<div class="col-xl-12 col-xxl-12">
						<div class="card">
							<div class="card-body">
								<div id="smartwizard" class="form-wizard order-create">
									<ul class="nav nav-wizard">
										@if (!Auth::user()->company)
										<li><a class="nav-link" href="#wizard_company">
											<span>1</span>
										</a></li>
										<li><a class="nav-link" href="#wizard_search">
											<span>2</span>
										</a></li>
										<li><a class="nav-link" href="#wizard_verify">
											<span>3</span>
										</a></li>
										<li><a class="nav-link" href="#wizard_confirm">
											<span>4</span>
										</a></li>
										@else
										<li><a class="nav-link" href="#wizard_search">
											<span>1</span>
										</a></li>
										<li><a class="nav-link" href="#wizard_verify">
											<span>2</span>
										</a></li>
										<li><a class="nav-link" href="#wizard_confirm">
											<span>3</span>
										</a></li>
										@endif
									</ul>
									<div class="tab-content">
										@if (!Auth::user()->company)
										<div id="wizard_company" class="tab-pane" role="tabpanel">
											<form id="company" action="{{ route('confirm-details') }}" method="POST">
											<div class="row">
												<div class="col-lg-12 mb-2">
													<h4 class="card-title">Confirm your Podcaster account details</h4>
													<hr />
												</div>

												<div class="col-lg-6 mb-2">
													<div class="form-group">
														<label class="text-label">First Name*</label>
														<input type="text" name="firstname" class="form-control" value="{{ Auth::user()->firstname }}" required>
													</div>
												</div>
												<div class="col-lg-6 mb-2">
													<div class="form-group">
														<label class="text-label">Last Name*</label>
														<input type="text" name="lastname" class="form-control" value="{{ Auth::user()->lastname }}" required>
													</div>
												</div>
												<div class="col-lg-6 mb-2">
													<div class="form-group">
														<label class="text-label">Company/Publisher Name*</label>
														<input type="text" name="company" class="form-control" value="{{ Auth::user()->company }}" required>
														<div></div>
													</div>
												</div>
												<div class="col-lg-6 mb-2">
													<div class="form-group">
														<label class="text-label">Phone Number*</label>
														<input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}" required>
													</div>
												</div>

											</div>
											<div class="toolbar toolbar-bottom" role="toolbar" style="text-align: right;">
												<button class="btn btn-primary" type="button">Next</button>
											</div>
											</form>
										</div>
										@endif
										<div id="wizard_search" class="tab-pane" role="tabpanel">
											<div class="row">
												<div class="col-lg-12 mb-2">
													<h4 class="card-title">Claim your show</h4>
													<hr />
												</div>
												<div class="col-lg-5 mb-2">
													<div class="form-group">
														<label class="text-label">Search for your Podcast by name</label>
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
														<label class="text-label">Enter your Podcast feed url</label>
														<div class="input-group">
															<input type="text" id="feed_url" name="feed_url" class="form-control" />
															<div class="input-group-append">
																	<button id="search_url" class="btn btn-primary" type="button">Search</button>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-12 mt-4">
													<div id="feed_result" class="">

													</div>
												</div>
											</div>
											<div class="toolbar toolbar-bottom" role="toolbar">
											</div>
										</div>
										<div id="wizard_verify" class="tab-pane" role="tabpanel">
											<div class="row">
												<div class="col-lg-12 mb-2">
													<h4 class="card-title">Confirm you own the rights to manage this Podcast</h4>
													<hr />
												</div>
												<div id="podcast_selected" class="col-lg-12 mb-2"></div>
												<div class="col-lg-12 mb-2 no-owner-email d-none">
													<p>Podcast owner email address cannot be found in the feed. Please make sure owner email exists in the feed for verification purpose.</p>
													<button class="btn btn-primary sw-btn-prev">Back</button>
												</div>
												<div class="col-lg-12 mb-2 yes-owner-email d-none">
													<p>To verify that you have the rights to claim and manage this Podcast, a verification email will be sent to the email address that is in the Podcast RSS Feed. You will need access to this email account to validate ownership and claim this Podcast.</p>
													<button id="get_code" class="btn btn-primary">Send Verification Code</button>
												</div>
												<div class="code-form col-sm-12 mb-2 d-none">
													<p>Please check your inbox (or Junk / Spam folder) for the email containing the verification code.</p>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<input class="form-control fs-30" type="text" name="code" id="code" placeholder="Enter your verification code">
																<button id="submit_code" class="btn btn-primary mt-3">Next</button>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="toolbar toolbar-bottom" role="toolbar">
											</div>
										</div>
										<div id="wizard_confirm" class="tab-pane" role="tabpanel">
											<div class="row">
												<div class="col-lg-12 mb-2">
													<h4 class="card-title">Great, you've verified your Podcast!</h4>
													<hr />
												</div>
												<div class="col-lg-12 col-sm-12 col-12">
													<div id="confirm" class=""></div>
												</div>
											</div>
											<div class="toolbar toolbar-bottom text-right" role="toolbar">
												<a href="{{route('podcasts')}}" class="btn btn-primary">Finish</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

@endsection




@push('css')
<link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}"  rel="stylesheet">
<link href="{{ asset('vendor/jquery-smartwizard/dist/css/smart_wizard.min.css') }}"  rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ asset('js/plugins-init/jquery.validate-init.js') }}" type="text/javascript"></script> -->
<script type="text/javascript">
var user_podcasts = {!! Auth::user()->podcasturls !!};
</script>
<script src="{{ asset('js/podcast/add-podcast.js') }}" type="text/javascript"></script>
@endpush