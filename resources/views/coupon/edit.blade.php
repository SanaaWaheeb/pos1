<form method="post" action="{{ route('coupons.update', $coupon->id) }}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if(!empty($settings['chatgpt_key']))
        <div class="d-flex justify-content-end">
            <a href="#" class="btn btn-primary btn-sm" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['coupan']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate with AI') }}
            </a>
        </div>
    @endif
    <div class="row">
        <div class="form-group col-md-12">
            <label for="name" class="form-label">{{ __('Name') }}</label><x-required></x-required>
            <input type="text" name="name" class="form-control" required value="{{ $coupon->name }}" placeholder="{{ __('Enter Name') }}">
        </div>

        <div class="form-group col-md-6">
            <label for="discount" class="form-label">{{ __('Discount') }}</label><x-required></x-required>
            <input type="number" name="discount" class="form-control" required step="0.01" min="0"
                value="{{ $coupon->discount }}" placeholder="{{ __('Enter Discount') }}">
            <span class="small">{{ __('Note: Discount in Percentage') }}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="limit" class="form-label">{{ __('Limit') }}</label><x-required></x-required>
            <input type="number" name="limit" class="form-control" required min="0" value="{{ $coupon->limit }}" placeholder="{{ __('Enter Limit') }}">
        </div>
        <div class="form-group col-md-12" id="auto">
            <label for="code" class="form-label">{{ __('Code') }}</label><x-required></x-required>
            <div class="input-group">

                <input class="form-control" name="code" type="text" id="auto-code" value="{{ $coupon->code }}" placeholder="{{ __('Enter Code') }}">
                <button type="button" class="btn btn-outline-secondary" id="code-generate"><i class="fa fa-history pr-1"></i>
                    {{ __('Generate') }}</button>

            </div>
        </div>
        <div class="form-group col-12 d-flex py-0 mb-0 justify-content-end form-label">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary"
                data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
        </div>
    </div>
</form>
