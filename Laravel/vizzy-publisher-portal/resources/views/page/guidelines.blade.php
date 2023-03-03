{{-- Extends layout --}}
@extends('layout.fullwidth')



{{-- Content --}}
@section('content')
		<div class="col-md-12">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
												<div class="text-center mb-3">
														<a href="/"><img src="{{ asset('images/logo-full.png') }}"  alt=""></a>
												</div>
												<h4 class="text-center mb-4 text-white">Please read our Podcaster guidelines. View/Scroll to the bottom of the document to sign.</h4>

												<div id="guidelines" class="text-left overflow-auto bg-white mb-3 p-2" style="height: 200px;">
													<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vulputate neque et quam dapibus, eget hendrerit turpis luctus. In nec aliquet eros. Cras aliquet rutrum leo ut pellentesque. Nam vel faucibus ipsum. Integer a massa vel arcu sagittis eleifend. Etiam hendrerit tellus id eleifend fermentum. Fusce imperdiet eu tortor ut condimentum.</p>
													<p>Nulla facilisi. Proin pretium ex sit amet neque consequat congue. Pellentesque eros augue, tempor id consequat sed, mollis sit amet mauris. Nullam feugiat ultrices mi a placerat. Suspendisse lectus sapien, pretium a tincidunt eget, tristique egestas quam. Donec vitae accumsan nisl, ut elementum turpis. Pellentesque libero ante, aliquam a ornare ac, placerat vel sem. Morbi fringilla eros id purus faucibus, eget tincidunt mauris egestas. Integer dapibus nunc arcu, ut semper libero pulvinar et.</p>
													<p>Aliquam justo mauris, malesuada in dignissim nec, aliquet vitae eros. Pellentesque venenatis metus at libero dictum, sit amet facilisis velit vestibulum. Sed vitae sodales justo, sed venenatis urna. Suspendisse rutrum vehicula nisl sit amet lacinia. Pellentesque nec magna viverra, sagittis diam sed, egestas ipsum. Nulla facilisi. Aliquam vel nibh lorem. Vivamus id odio elementum, mattis elit ullamcorper, ullamcorper lectus. Duis vulputate feugiat sapien, ac imperdiet ipsum venenatis sed. Aenean purus velit, lacinia vel lectus sit amet, cursus posuere sem. Vestibulum cursus lorem vitae turpis congue maximus. Nam aliquam enim in ipsum elementum luctus. Mauris cursus a mauris non semper. Nullam egestas cursus risus, ac sagittis ipsum feugiat vitae. Vestibulum ultrices est at turpis commodo luctus. Etiam arcu enim, pharetra at condimentum nec, mollis in diam.</p>
													<p>Sed a condimentum eros. Nulla ullamcorper orci vitae luctus aliquam. Phasellus sed placerat turpis. Morbi viverra ipsum eget aliquet convallis. Vivamus eget diam faucibus, cursus sem ut, laoreet nunc. Duis commodo tortor a arcu finibus tristique. Etiam ut convallis arcu, at molestie leo. Duis scelerisque euismod erat non lobortis.</p>
													<p>Sed pulvinar tincidunt rhoncus. Phasellus nec varius sapien. In sollicitudin nibh leo, et egestas dui tincidunt nec. Aliquam dapibus ornare nibh nec ullamcorper. Integer ultrices mi augue. Maecenas ut velit id leo dapibus gravida. Nullam euismod aliquet enim, a mattis dolor feugiat vel. Integer posuere eu elit quis dapibus. Sed ante metus, tempor in consequat at, aliquam eu nibh. Fusce purus tellus, dictum non eleifend vitae, auctor nec leo. Nunc luctus metus id consequat condimentum. </p>
												</div>

												<p class="text-white">By accepting the terms below, I acknowledge that I am the legal owner / representative of any Podcasts that I claim in my Vizzy account and that I am authorised to curate content using the Vizzy platform.</p>

                        
												<form action="{{ route('guidelines') }}" method="POST">
													@csrf
													<button type="submit" class="btn btn-primary btn-block" disabled>{{ __('Accept') }}</button>
												</form>
                    </div>
                </div>
            </div>
        </div>
    </div>					
@endsection

@push('css')
@endpush

@push('footer-scripts')
<script src="{{ asset('js/page/guidelines.js') }}" type="text/javascript"></script>
@endpush