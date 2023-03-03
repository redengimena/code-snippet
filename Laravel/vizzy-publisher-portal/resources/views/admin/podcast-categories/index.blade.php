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
                <li class="breadcrumb-item active">Podcast Categories</li>
            </ol>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card profile-card">
                        <div class="card-header flex-wrap border-0 pb-0">
                            <h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Podcast Categories</h3>
                            <a href="{{ route('admin.podcast-categories.report') }}" class="btn btn-primary btn-rounded mb-2 mr-3">Mappings report</a>
                            <a href="{{ route('admin.podcast-categories.create') }}" class="btn btn-primary btn-rounded mb-2">Add New</a>
                        </div>
                        <div class="card-body">     
                            <div class="table-responsive">
                                <table id="category" class="table display table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                        <tr>
                                            <td>
                                                {{ $category->name }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.podcast-categories.edit', $category->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-pencil"></i></a>
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
// $('#category').dataTable();
</script>
@endpush