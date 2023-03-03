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
						<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Publishers</a></li>
                        <li class="breadcrumb-item active">Edit Publisher</li>
					</ol>
				</div>
				<div class="row">
					<div class="col-xl-12 col-xxl-12 col-lg-12">
						<div class="row">
							<div class="col-xl-12">
								<div class="card custom-card">
									<div class="card-header flex-wrap border-0 pb-0">
										<h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Edit Publisher</h3>
									</div>
									<div class="card-body">

                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firstname">First Name</label>
                                <input id="firstname" name="firstname" type="text" class="form-control" value="{{ $user->firstname }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="lastname">Last Name</label>
                                <input id="lastname" name="lastname" type="text" class="form-control" value="{{ $user->lastname }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company">Company/Publisher Name</label>
                                <input id="company" name="company" type="text" class="form-control" value="{{ $user->company }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone">Phone</label>
                                <input id="phone" name="phone" type="text" class="form-control" value="{{ $user->phone }}" required>
                            </div>
                        </div>
                        <div class="form-group mb-5">
                            <div class="custom-control custom-checkbox mb-3 checkbox-primary">
                                <input type="checkbox" class="custom-control-input" @if ($user->hasRole('admin')) checked="" @endif id="is_admin" name="is_admin">
                                <label class="custom-control-label" for="is_admin">Set as Admin</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="#" data-toggle="modal" data-target="#userDeleteModal" class="btn btn-outline-primary pull-right">DELETE</a>
                        </div>
                    </form>



                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<div class="modal fade" id="userDeleteModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete user '{{$user->firstname}}'? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                <form action="{{route('admin.users.destroy', $user->id)}}" method="POST" enctype="multipart/form-data">
                @method('DELETE')
                @csrf
                <button class="btn btn-primary">DELETE</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
@endpush

@push('footer-scripts')
@endpush