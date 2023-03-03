{{-- Extends layout --}}
@extends('layout.default')

@section('title')
Export Usage Data
@endsection

{{-- Content --}}
@section('content')
<div class="container-fluid">
    <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Administration</li>
                <li class="breadcrumb-item active">Export usage data</li>
            </ol>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card profile-card">
                        <div class="card-header flex-wrap border-0 pb-0">
                            <h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Export Usage Data</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="category" class="table display table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th scope="col">Database Table</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                App - User
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.export.appusers') }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-download"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                App - Actvity Logs
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.export.appactivitylogs') }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-download"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                App - Played
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.export.appplayed') }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-download"></i></a>
                                            </td>
                                        </tr>
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
@endpush

@push('footer-scripts')
@endpush