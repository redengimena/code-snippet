{{-- Extends layout --}}
@extends('layout.default')

@section('title')
MyProfile
@endsection

{{-- Content --}}
@section('content')
            <!-- row -->
			<div class="container-fluid" id="app" v-cloak >
				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-lg-12">
						<div class="row">
							<div class="col-xl-12">
								<vizzy-image-field inline-template>
								<div>
									<div class="media-library-container">
										<div v-if="inputName">
											@include('MediaManager::extras.modal',[
												'select_button' => true,
												'restrict' => [
													'path' => 'uploads/' . Auth::user()->id,
											]])
										</div>
										<media-modal old="{{ old('cover', Auth::user()->image) }}" item="cover" :name="inputName"></media-modal>
									</div>
									<form action="{{route('profile')}}" method="POST">
									@csrf
										<div class="card profile-card">
											<div class="card-header flex-wrap border-0 pb-0">
												<h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Edit Profile</h3>
												<a href="/" class="btn btn-dark light btn-rounded mr-3 mb-2">Cancel</a>
												<button class="btn btn-primary btn-rounded mb-2" type="submit">Save Changes</button>
											</div>

											<div class="card-body">
												<div class="row mb-5">
													<div class="col-md-9">
														<div class="title mb-4"><span class="fs-18 text-black font-w600">Generals</span></div>
														<div class="row">
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>First Name</label>
																	<input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" placeholder="Enter name" value="{{Auth::user()->firstname}}">
																	@error('firstname')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																	@enderror
																</div>
															</div>
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>Last Name</label>
																	<input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" placeholder="Last name" value="{{Auth::user()->lastname}}">
																	@error('lastname')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																	@enderror
																</div>
															</div>
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>Password</label>
																	<input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
																	@error('password')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																	@enderror
																</div>
															</div>
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>Re-Type Password</label>
																	<input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Enter password">
																	@error('password')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																	@enderror
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="title mb-4"><span class="fs-18 text-black font-w600">Avatar</span></div>
															<div class="text-center profile-avatar">
                                <div class="profile-image">
																<img v-if="cover" :src="cover" class="avator-lg rounded-circle mb-4">
                                </div>
                                <br/>
																<input type="hidden" name="image" :value="cover"/>
																<button class="btn btn-primary btn-sm btn-rounded" @click="toggleModalFor('cover')" type="button">Select Image</button>
															</div>
														</div>
													</div>


													<div class="mb-5">
														<div class="title mb-4"><span class="fs-18 text-black font-w600">CONTACT</span></div>
														<div class="row">
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>Publisher Name</label>
																	<div class="mb-3">
																		<input type="text" class="form-control @error('company') is-invalid @enderror" name="company" placeholder="Publisher/Company Name" value="{{Auth::user()->company}}">
																		@error('company')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																		@enderror
																	</div>
																</div>
															</div>
															<div class="col-xl-6 col-sm-6">
																<div class="form-group">
																	<label>Mobile Phone</label>
																	<div class="input-group input-icon mb-3">
																		<div class="input-group-prepend">
																			<span class="input-group-text" id="basic-addon1"><i class="fa fa-phone" aria-hidden="true"></i></span>
																		</div>
																		<input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Phone no." value="{{Auth::user()->phone}}">
																		@error('phone')
																			<span class="invalid-feedback animated fadeInUp" role="alert">
																					<strong>{{ $message }}</strong>
																			</span>
																		@enderror
																	</div>
																</div>
															</div>
														</div>
													</div>

											</div>
										</div>
									</form>
								</div>
								</vizzy-image-field>
							</div>
						</div>
					</div>
				</div>
			</div>

@endsection

@push('css')
<link href="{{ asset('css/media.css') }}"  rel="stylesheet">
<link href="{{ asset('assets/vendor/MediaManager/style.css') }}" rel="stylesheet" />
@endpush

@push('footer-scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/camanjs/4.1.2/caman.full.min.js"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endpush