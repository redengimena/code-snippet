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
                <li class="breadcrumb-item active">Vizzys</li>
            </ol>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12">
            <div class="row">
                <div class="col-xl-12">

                  <div class="datatable-vizzy-filter text-center d-none">
                    <a href="#" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 mb-2">ALL</a>
                    <a href="#draft" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 light mb-2">Draft</a>
                    <a href="#pending" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 light mb-2">Pending</a>
                    <a href="#rejected" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 light mb-2">Rejected</a>
                    <a href="#published" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 light mb-2">Published</a>
                    <a href="#unpublished" class="btn-status btn btn-primary btn-rounded btn-xs mr-2 light mb-2">Unpublished</a>
                  </div>
                  <input type="hidden" id="status" />
                  <div class="table-responsive">
                    {{$dataTable->table()}}
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
  $(function () {
    var table = $('#vizzys').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('admin.vizzys.index') }}",
          data: function (d) {
                d.status = $('#status').val(),
                d.search = $('input[type="search"]').val()
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'podcast_id', name: 'podcast_id'},
            {data: 'title', name: 'title'},
            {data: 'owner', name: 'owner'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'},
        ],
        order: [[ 5, "desc" ]],
        responsive: true,
        autoWidth: false,
    }).on( 'init.dt', function () {
        $('#vizzys_wrapper').prepend($('.datatable-vizzy-filter'));
        $('.datatable-vizzy-filter').removeClass('d-none');
    } );

    $('.btn-status').on('click', function(e) {
      e.preventDefault();
      $('.btn-status').addClass('light');
      $(this).removeClass('light');
      $('#status').val($(this).attr('href').replace('#',''));
      table.draw();
    });
  });
</script>
@endpush