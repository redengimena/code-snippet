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
						<li class="breadcrumb-item active">Podcasters</li>
					</ol>
				</div>
				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-lg-12">
						<div class="row">
							<div class="col-xl-12">
								<div class="card profile-card">
									<div class="card-header flex-wrap border-0 pb-0">
										<h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Podcasters</h3>
									</div>
									<div class="card-body">
                      <div class="table-responsive">
                          <table id="publisher" class="display table-responsive-md">
                              <thead>
                                  <tr>
                                      <th scope="col">First Name</th>
                                      <th scope="col">Last Name</th>
                                      <th scope="col">Email</th>
                                      <th scope="col">No. Podcast</th>
                                      <th scope="col">Role</th>
                                      <th scope="col">Created</th>
                                      <th scope="col">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($users as $user)
                                  <tr>
                                      <td>{{ $user->firstname }}</td>
                                      <td>{{ $user->lastname }}</td>
                                      <td>{{ $user->email }}</td>
                                      <td>{{ $user->podcasts->count() }}</td>
                                      <td>
                                          @foreach ($user->roles as $role)
                                          <a href="javascript:void(0);" class="badge badge-rounded badge-outline-primary font-size-11 m-1">{{ $role->name }}</a>
                                          @endforeach
                                      </td>
                                      <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                      <td>
                                          <div class="d-flex">
                                              <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-pencil"></i></a>
                                              @canImpersonate($guard = null)
                                              @canBeImpersonated($user, $guard = null)
                                                  <a href="{{ route('impersonate', $user->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1" title="Login as user"><i class="fa fa-sign-in "></i></a>
                                              @endCanBeImpersonated
                                              @endCanImpersonate
                                          </div>

                                          <!-- @ perms('impersonation')
                                          <a href="{ { route('admin.login-as', $user->id) } }" title="Login as user" target="_blank"><span class="fe fe-log-in fs-4"></span></a>
                                          @ endperms -->
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
				</div>
			</div>
@endsection


@push('css')
<link href="{{ asset('/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$('#publisher').dataTable({"aaSorting": []});
</script>
@endpush