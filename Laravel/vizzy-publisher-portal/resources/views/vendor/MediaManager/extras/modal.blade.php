{{-- manager --}}
<div class="modal mm-animated fadeIn is-active modal-manager__Inmodal">
    <div class="modal-background" @click.stop="hideInputModal()"></div>
    <div class="modal-content mm-animated fadeInDown">
        <div class="box">
            <div>Max file upload size: 1 Mb</div>
            @include('MediaManager::_manager', ['modal' => true])
            @if (isset($select_button))
            <div class="text-center mt-3">
                <button class="btn btn-primary" @click.stop="hideInputModal()">Select</button>
            </div>
            @endif
        </div>
    </div>
    <button class="modal-close is-large is-hidden-touch" @click.stop="hideInputModal()"></button>
</div>
