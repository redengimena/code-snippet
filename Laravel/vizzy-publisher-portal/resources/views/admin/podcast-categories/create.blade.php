{{-- Extends layout --}}
@extends('layout.default')

@section('title')
Administration
@endsection

@section('content')
<!-- row -->
<div class="container-fluid" id="app" v-cloak>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">Administration</li>
            <li class="breadcrumb-item"><a href="{{ route('admin.podcast-categories.index') }}">Podcast Categories</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
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
                                <media-modal old="{{ old('cover') }}" item="cover" :name="inputName"></media-modal>
                            </div>
                            <form action="{{route('admin.podcast-categories.store')}}" method="POST">
                            @csrf
                            <div class="card-header flex-wrap border-0 pb-0">
                                <h3 class="fs-24 text-black font-w600 mr-auto mb-2 pr-3">Add new Podcast Category</h3>
                                <a href="{{ route('admin.podcast-categories.index') }}" class="btn btn-dark light btn-rounded mr-3 mb-2">Cancel</a>
                                <button class="btn btn-primary btn-rounded mb-2" type="submit">Save</button>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Sub Categories</label>
                                            <select id="mapping" name="mapping[]" class="form-control select2" multiple>
                                              @if (old('mapping'))
                                                @foreach(old('mapping') as $mapping)
                                                <option selected="selected">{{ $mapping }}</option>
                                                @endforeach
                                              @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-center mb-4">Image</div>
                                            <div class="text-center category-image">
                                                <div class="category-image-wrapper">
                                                    <img v-if="cover" :src="cover" class="mb-4">
                                                </div>
                                                <br/>
                                                <input type="hidden" name="image" :value="cover"/>
                                                <button class="btn btn-primary btn-sm btn-rounded" @click="toggleModalFor('cover')" type="button">Select Image</button>
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
</div>
@endsection


@push('css')
<link href="{{ asset('css/media.css') }}"  rel="stylesheet">
<link href="{{ asset('assets/vendor/MediaManager/style.css') }}" rel="stylesheet" />
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('footer-scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/camanjs/4.1.2/caman.full.min.js"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
$('.select2').select2({
    tags: true
});
</script>
@endpush