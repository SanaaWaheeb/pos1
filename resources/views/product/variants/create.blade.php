
<form method="POST" action="{{ route('get.product.variants.possibilities') }}" class="needs-validation" novalidate>
    @csrf
    <div class="form-group">

        <label for="variant_name">{{ __('Variant Name') }}</label><x-required></x-required>
        <input class="form-control" name="variant_name" type="text" id="variant_name" placeholder="{{ __('Variant Name, i.e Size, Color etc') }}" required>
    </div>
    <div class="form-group">
        <label for="variant_options">{{ __('Variant Options') }}</label><x-required></x-required>
        <input class="form-control" name="variant_options" type="text" id="variant_options" placeholder="{{ __('Variant Options separated by|pipe symbol, i.e Black|Blue|Red') }}" required>
    </div>
    <div class="form-group col-12 py-0 mb-0 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Add Variants')}}" class="btn btn-primary add-variants ms-2">
    </div>
</form>
