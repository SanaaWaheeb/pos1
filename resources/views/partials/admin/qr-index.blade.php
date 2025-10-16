@extends('layouts.admin')

@section('page-title', __('QR Payments'))
@section('content')
<div class="container-fluid">
  <div class="row g-3">
    {{-- <div class="col-12 d-flex align-items-center justify-content-between">
      <h4 class="mb-0">{{ __('QR Stores') }}</h4>
      <a href="{{ route('dashboard') }}" class="btn btn-light-primary">
        <i class="ti ti-layout-dashboard"></i> {{ __('Visit Dashboard') }}
      </a>
    </div> --}}
{{-- Create New QR – first card --}}
<div class="col-xl-3 col-lg-4 col-md-6">
  <a href="#!"
     class="text-decoration-none"
     data-size="lg"
     data-url="{{ route('store-resource.create', ['type' => 'qr']) }}"
     data-ajax-popup="true"
     data-title="{{ __('Create New QR') }}">
    <div class="card h-100 border-0 shadow-sm new-qr-tile">
      <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
        <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
             style="width:72px;height:72px;background: #F2F2F2;">
          <i class="ti ti-circle-plus" style="font-size:34px;color:primary;"></i>
        </div>
        <div class="fw-semibold" style="font-size:1.05rem;">{{ __('Create New QR') }}</div>
        <div class="text-muted small">{{ __('Add a new QR payment') }}</div>
      </div>
    </div>
  </a>
</div>

    @forelse ($stores as $store)
      @php
        $slug      = $store->slug;
        $mobile    = $store->mobile ?? '-';
        $storeLink = url("{$slug}/self-payment");
      @endphp

      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card h-100">
          <div class="card-body d-flex flex-column align-items-center text-center">
            {{-- QR (Option A: server-side SVG) --}}
            @if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
              {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(1)->generate($storeLink) !!}
            @else
              {{-- Option B: client-side placeholder --}}
              <div class="qr-js" data-link="{{ $storeLink }}" style="width:180px;height:180px;"></div>
            @endif

            <div class="mt-3 w-100">
  <div class="fw-semibold">{{ $store->name ?? $slug }}</div>
  {{-- <div class="text-muted small">{{ $slug }}</div> --}}

  {{-- Editable mobile (inline) --}}
  <form action="{{ route('stores.updateMobile', $store->id) }}" method="POST" class="mt-2 d-flex gap-2 align-items-center">
      @csrf
      @method('PATCH')

      <div class="input-group input-group-sm" style="max-width: 230px;">
          <span class="input-group-text">
              <i class="ti ti-phone"></i>
          </span>
          <input
              type="tel"
              name="mobile"
              class="form-control"
              placeholder="{{ __('+966560000000') }}"
              value="{{ old('mobile', $store->mobile) }}"
          >
      </div>

      <button type="submit" class="btn btn-primary btn-sm">
          {{ __('Save') }}
      </button>
  </form>

  @error('mobile')
    <div class="small text-danger mt-1">{{ $message }}</div>
  @enderror
</div>


            {{-- <div class="d-flex gap-2 mt-3">
              <a href="{{ route('stores.visit', $store->id) }}" class="btn btn-primary">
                <i class="ti ti-layout-dashboard"></i> {{ __('Visit Dashboard') }}
              </a>

             
              <button type="button"
                      class="btn btn-light-success copy-link"
                      data-link="{{ $storeLink }}">
                <i class="ti ti-copy"></i> {{ __('Copy') }}
              </button>

              <button type="button"
                      class="btn btn-light-primary print-link"
                      data-link="{{ $storeLink }}"
                      data-store-title="{{ $store->name ?? $slug }}">
                <i class="ti ti-printer"></i> {{ __('Print') }}
              </button>
            </div> --}}

            {{-- fix size of buttons --}}
            <div class="qr-actions d-flex flex-wrap gap-2 mt-3 w-100">
  {{-- Use the existing change_store route to switch, then your app redirects to dashboard --}}
  <a href="{{ route('change_store', $store->id) }}"
     class="btn btn-primary btn-sm flex-fill d-inline-flex align-items-center justify-content-center gap-1">
    <i class="ti ti-layout-dashboard"></i>
    <span class="text-truncate">{{ __('Visit Dashboard') }}</span>
  </a>

  <button type="button"
          class="btn btn-light-primary btn-sm flex-fill d-inline-flex align-items-center justify-content-center gap-1 copy-link"
          data-link="{{ $storeLink }}">
    <i class="ti ti-copy"></i>
    <span class="text-truncate">{{ __('Copy') }}</span>
  </button>
  <button type="button"
          class="btn btn-light-primary btn-sm flex-fill d-inline-flex align-items-center justify-content-center gap-1 print-link"
          data-link="{{ $storeLink }}"
          data-store-title="{{ $store->name ?? $slug }}">
    <i class="ti ti-printer"></i>
    <span class="text-truncate">{{ __('Print') }}</span>
  </button>
</div>



          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info mb-0">{{ __('No QR stores found.') }}</div>
      </div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
{{-- Option B only: qrcodejs for client-side render --}}
@if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.qr-js').forEach(function(el){
    new QRCode(el, { text: el.dataset.link, width: 180, height: 180 });
  });
});
</script>
@endif
{{-- fUNCTION --}}
@endpush
<script>
(function () {
  // ---- absolute URLs for assets (Blade expands these before JS runs) ----
  const ASSETS = {
    logo : @json(url(Storage::url('uploads/theme5/ava.png'))),
    mada : @json(url(Storage::url('uploads/theme5/mada.png'))),
    apple: @json(url(Storage::url('uploads/theme5/apple_pay.png'))),
    mc   : @json(url(Storage::url('uploads/theme5/master_card.png'))),
    visa : @json(url(Storage::url('uploads/theme5/visa.png'))),
    step1: @json(url(Storage::url('uploads/theme5/amount.png'))),
    step2: @json(url(Storage::url('uploads/theme5/payment.png'))),
    step3: @json(url(Storage::url('uploads/theme5/confirm.png')))
  };

  // ---- A4 printable HTML ----
  function buildPrintHtml({ title, link }) {
    return `<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8"/>
<title>${title}</title>
<style>
  /* Exact A4 and keep background colors in print */
  @page { size: A4; margin: 0; }
  *      { box-sizing: border-box; }
  html, body {
    width: 210mm; height: 297mm; margin: 0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    color-adjust: exact;
    font-family: "Noto Kufi Arabic", Arial, sans-serif;
  }

  .poster{
    position: relative;
    width: 210mm; height: 297mm;
    margin: 0 auto;
    background: #fff;
    overflow: hidden;
  }

  /* header */
  .header{
    position: relative; height: 48mm;
  }
  .header .black{
    position:absolute; inset:0 0 20mm 0; /* 40mm high */
    height:40mm; background:#000;
  }
  // .header .skewL, .header .skewR{
  //   position:absolute; top:14mm; width:120mm; height:22mm; background:#d9d9d9;
  // }
  .header .skewL{ left:-10mm; transform: rotate(9deg); }
  .header .skewR{ right:-10mm; transform: rotate(-9deg); }
  .header .logo{
    position:absolute; top:6mm; left:0; right:0;
    display:flex; justify-content:center; align-items:center; height:28mm;
  }
  .header .logo img{ height:25mm; }

  /* payment row */
  .pay{
    text-align:center; padding-top: 2mm;
  }
  .pay .title{
    font-weight:700; color:#242424; font-size:23pt; line-height:1.4; margin-bottom:4mm;
  }
  .brands{ display:inline-flex; align-items:center; gap:8mm; }
  .brands img{ height:10mm; }

  /* QR area */
  .qr{
    margin: 10mm auto 4mm auto;
    width: 55mm;   /* FINAL QR SIZE */
    height:100mm;
    display:flex; align-items:center; justify-content:center;
  }
  /* qrcodejs injects a <img> or <canvas> → clamp it here */
  .qr img, .qr canvas{ width:111mm !important; height:111mm !important; }

  .qr-captions{
    display:flex; justify-content:center; gap:10mm; text-align:center;
    font-weight:700; color:#242424; font-size:17pt; line-height:25px;
  }

  /* footer */
  .footer{
    position:absolute; left:0; right:0; bottom:0;
    height:75mm; background:#000; color:#fff; padding:10mm;
  }
  .footer .head{
    display:flex; justify-content:space-between; align-items:baseline;
    font-weight:700;
  }
  .footer .head .en, .footer .head .ar{font-size:18pt;   }

  .steps{
    margin-top:8mm; display:flex; justify-content:space-between; gap:8mm; text-align:center;
  }
  .step{ flex:1; }
  .num{
    width:8mm; height:8mm; border-radius:50%; background:#fff; color:#000;
    display:inline-flex; align-items:center; justify-content:center;
    font-weight:700; font-size:10pt; margin-bottom:3mm;
  }
  .icon{ margin-bottom:3mm; }
  .step .ar{ font-weight:700; font-size:18pt;  line-height:13px; margin-bottom:1mm; }
  .step .en{ font-weight:600; font-size:18pt;  line-height:30px; opacity:.95; }

  .ltr{ direction:ltr; display:inline-block; }
</style>
</head>
<body>
  <div class="poster">
    <div class="header">
      <div class="black"></div>
      <div class="skewL"></div>
      <div class="skewR"></div>
      <div class="logo"><img src="${ASSETS.logo}" alt="AVA"></div>
    </div>

    <div class="pay">
      <div class="title"><span>للدفع بالوسائل</span> <span class="ltr">TO PAY USING</span></div>
      <div class="brands">
        <img src="${ASSETS.mada}"  alt="mada">
        <img src="${ASSETS.apple}" alt="Apple Pay">
        <img src="${ASSETS.mc}"    alt="Mastercard">
        <img src="${ASSETS.visa}"  alt="VISA">
      </div>
    </div>

    <div class="qr" id="qrBox"></div>
<br/>
<br/>
    <div class="qr-captions">
      <div>امسح الباركود <br>بكاميرا الجوال</div>
      <div>SCAN QR USING <br>PHONE CAMERA</div>
    </div>

    <div class="footer">
      <div class="head">
        <div class="en">NEXT STEPS</div>
        <div class="ar">الخطوات التالية</div>
      </div>

      <div class="steps">
        <div class="step">
          <div class="num">1</div>
          <img class="icon" src="${ASSETS.step1}" alt="">
          <div class="ar">ادخل المبلغ</div>
          <div class="en">Enter the amount</div>
        </div>
        <div class="step">
          <div class="num">2</div>
          <img class="icon" src="${ASSETS.step2}" alt="">
          <div class="ar">قم بالدفع</div>
          <div class="en">Make the payment</div>
        </div>
        <div class="step">
          <div class="num">3</div>
          <img class="icon" src="${ASSETS.step3}" alt="">
          <div class="ar">تأكيد الدفع</div>
          <div class="en">Confirmation</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"><\/script>
  <script>
    // Render QR (any size), then it's clamped to 55mm by CSS above
    new QRCode(document.getElementById('qrBox'), {
      text: ${JSON.stringify(link)},
      width: 900, height: 900, correctLevel: QRCode.CorrectLevel.M
    });
    // Print after the QR & images settle
    window.onload = function(){ setTimeout(function(){ window.print(); }, 500); };
  <\/script>
</body>
</html>`;
  }

  // ---- Bind PRINT on each card ----
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.print-link');
    if (!btn) return;

    e.preventDefault();
    const link  = btn.getAttribute('data-link') || '';
    const title = btn.getAttribute('data-store-title') || 'QR';
    if (!link) return alert('Missing store link');

    const w = window.open('', '_blank', 'width=1024,height=1366'); // big preview
    w.document.open();
    w.document.write(buildPrintHtml({ title, link }));
    w.document.close();
  });

  // ---- Copy (unchanged) ----
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.copy-link');
    if (!btn) return;
    const link = btn.getAttribute('data-link') || '';
    if (!link) return;
    const done = () => (typeof show_toastr === 'function') && show_toastr('Success', '{{ __("Link copied") }}', 'success');
    (navigator.clipboard?.writeText(link) || Promise.reject()).then(done).catch(function(){
      const tmp = document.createElement('input'); document.body.appendChild(tmp);
      tmp.value = link; tmp.select(); document.execCommand('copy'); tmp.remove(); done();
    });
  });
})();
</script>
