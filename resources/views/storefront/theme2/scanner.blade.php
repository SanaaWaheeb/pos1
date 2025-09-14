@extends('storefront.layout.theme2')
@section('page-title') 
    {{ __('Scan and Add to Cart') }} 
@endsection 

@php 
    $cart = session()->get($store->slug); 
    $imgpath = \App\Models\Utility::get_file('uploads/is_cover_image/'); 
    $successAudioPath = asset('storage/uploads/audios/successScan.mp3');
    $failurAudioPath = asset('storage/uploads/audios/wrongScan.mp3');
@endphp 

{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> --}}

@section('content') 

<div class="wrapper" style="flex: 1; display: flex; flex-direction: column; justify-content: center"> 
    <section class="scanner-section py-5"> 
        <div class="container"> 
            <div class="row justify-content-center"> 
                <div class="col-lg-12 text-center mb-4"> 
                    <h2 class="fw-bold">{{ __('Scan Your Product') }}</h2> 
                    <p class="text-muted">{{ __('Use your camera to scan a barcode and add the product to your cart') }}</p> 
                </div> 
            </div> 
            <div class="row justify-content-center" style="padding: 1rem;"> 
                <div class="col-lg-6 col-md-8 col-12"> 
                    <div id="scanner-container" style="position: relative; width: 100%; height: 280px; border: 2px solid #ccc; border-radius: 10px; overflow: hidden;">
                        <video id="scanner-preview" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;"></video>
                        {{-- <video-capture auto-play facing-mode="environment"></video-capture> --}}
                        <!-- Overlay with smaller rectangular border -->
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                            <div style="
                                position: absolute;
                                top: 50%; left: 50%;
                                transform: translate(-50%, -50%);
                                width: 80%; 
                                height: 60%; 
                                pointer-events: none;">
                                
                                <div style="position: absolute; top: 0; left: 0; width: 30px; height: 30px; border: 4px solid #D62329; border-right: none; border-bottom: none;"></div>
                                <div style="position: absolute; top: 0; right: 0; width: 30px; height: 30px; border: 4px solid #D62329; border-left: none; border-bottom: none;"></div>                            
                                <div style="position: absolute; bottom: 0; left: 0; width: 30px; height: 30px; border: 4px solid #D62329; border-right: none; border-top: none;"></div>
                                <div style="position: absolute; bottom: 0; right: 0; width: 30px; height: 30px; border: 4px solid #D62329; border-left: none; border-top: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <p id="scan-result" class="mt-3 text-success"> 
                        {{ __('Scanning... Please wait') }} 
                    </p> 
                </div> 
            </div> 

            <!-- Add "Can't Scan Barcode?" button -->
            <div class="row justify-content-center mt-4">
                <button id="manualBarcodeButton" class="checkout-btn btn btn-primary btn-lg">{{ __('Can\'t Scan Barcode?') }}</button>
            </div>
        </div> 
    </section> 
</div> 

<!-- Modal for Manual Barcode Entry -->
<div class="modal" id="manualBarcodeModal" tabindex="-1" aria-labelledby="manualBarcodeModalLabel" style="background-color: rgba(0, 0, 0, 0.5)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualBarcodeModalLabel">{{ __('Enter Barcode Manually') }}</h5>
                <!-- Font Awesome Close Button -->
                <button type="button" class="btn-close custom-close-button" data-bs-dismiss="modal" aria-label="Close" style="border: none; background-color: transparent">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted">{{ __('If you are having issues scanning, you can enter the product\'s barcode number here.') }}</p>
                <!-- Display the image as a preview -->
                <img src="{{ asset('assets/images/font_size.jpg') }}" alt="Barcode Guide" class="img-fluid mb-3" style="max-width: 100%; height: auto;">
                <!-- Input for entering barcode manually -->
                <input type="text" id="manualBarcodeInput" class="form-control mb-3" placeholder="{{ __('Product barcode:') }}">
                <!-- Error message container -->
                <p id="barcodeError" style="display: none; color: red;">{{ __('Please enter a barcode.') }}</p>
            </div>
            <!-- Enforced Centering for Submit Button -->
            <div class="justify-content-center" style="text-align: center;">
                <button type="button" id="submitManualBarcode" class="btn checkout-btn">{{ __('Submit') }}</button>
            </div>
            <br>
        </div>
    </div>
</div>

@endsection 
 
@push('script-page')
<script type="module" src="{{ asset('js/scanReader.js') }}"></script>

<script>
    function playAudio(audioURL) {
        if (!audioURL) {
            console.warn("Audio URL is missing.");
            return;
        }

        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        fetch(audioURL)
            .then((response) => response.arrayBuffer())
            .then((buffer) => audioContext.decodeAudioData(buffer))
            .then((decodedData) => {
                const source = audioContext.createBufferSource();
                source.buffer = decodedData;
                source.connect(audioContext.destination);
                source.start(0);
            })
            .catch((error) => {
                console.error("Error playing audio:", error);
            });
    }
</script>

<script type="module">
    document.addEventListener('DOMContentLoaded', async function () {
        const scannerContainer = document.getElementById('scanner-container');
        const scanResult = document.getElementById('scan-result');
        const videoPreview = document.getElementById('scanner-preview');
        const manualBarcodeButton = document.getElementById('manualBarcodeButton');
        const manualBarcodeModal = new bootstrap.Modal(document.getElementById('manualBarcodeModal'));
        const submitManualBarcode = document.getElementById('submitManualBarcode');
        const manualBarcodeInput = document.getElementById('manualBarcodeInput');
        const barcodeError = document.getElementById('barcodeError');
        let isProcessing = true;

        const translations = {
            scanning: @json(__('Scanning... Please wait')),
            barcodeDetected: @json(__('Barcode detected')),
            cameraError: @json(__('Error initializing scanner. Please check your camera settings')),
            accessDenied: @json(__('Camera access denied. Please allow camera permissions to use the scanner')),
        };

        /**
         * Initialize the video stream.
         */
        async function initializeVideoStream() {
            try {
                const constraints = {
                    video: {
                        facingMode: 'environment',
                    },
                };
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                videoPreview.srcObject = stream;
                videoPreview.play();
            } catch (error) {
                console.error("Error accessing video stream:", error.message);
                alert(translations.cameraError);
            }
        }

        /**
         * Perform barcode scanning and handle the detected barcode.
         */
        async function scanBarcode(barcodeReader) {
            // if (!isProcessing) return;
            // isProcessing = false;

            try {
                const barcode = await barcodeReader.detect(videoPreview);

                if (barcode?.rawValue) {
                    const slug = "{{ $store->slug }}";
                    const baseURL = "{{ url('/') }}";
                    const url = `${baseURL}/user-cart-item/${slug}/scanner?barcode=${barcode.rawValue}`;

                    // Fetch data for detected barcode
                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then((response) => response.json())
                        .then((result) => {
                            let audioURL;
                            if (result.status === 'success') {
                                audioURL = "{{ $successAudioPath }}";
                                const cartItemsCountElements = document.getElementById('cart-item-count');
                                if (cartItemsCountElements) {
                                    cartItemsCountElements.textContent = `(${result.cart_items})`;
                                }
                                show_toastr('success', result.message, "success");
                            } else {
                                audioURL = "{{ $failurAudioPath }}";
                                show_toastr('Error', result.error, 'error');
                            }

                            // Play sound
                            if (audioURL) playAudio(audioURL);
                        })
                        .catch((error) => console.error("Error fetching barcode data:", error));
                }
                scanResult.textContent = `${translations.barcodeDetected}: ${barcode.rawValue}`;
                
            } catch (error) {
                console.warn("Barcode not detected. Continuing scan.");
            }

            // Reset for the next scan after 3 seconds
            setTimeout(() => {
                // isProcessing = true;
                scanResult.textContent = translations.scanning;
                scanBarcode(barcodeReader);
            }, 1000);
        }

        /**
         * Initialize the barcode reader and start scanning.
         */
        async function initializeBarcodeReader() {
            const { barcodeReaderError } = await scanReader.setup();
            if (barcodeReaderError) {
                alert(translations.cameraError);
                console.error(barcodeReaderError.message);
                return;
            }

            const supportedFormats = await scanReader.getSupportedFormats();
            const barcodeReader = await scanReader.create(supportedFormats);

            // Start scanning
            scanBarcode(barcodeReader);
        }

        // Initialize video and barcode reader
        await initializeVideoStream();
        await initializeBarcodeReader();
        
        // Show modal when "Can't Scan Barcode?" button is clicked
        manualBarcodeButton.addEventListener('click', function () {
            manualBarcodeModal.show();
        });

        // Handle manual barcode submission
        submitManualBarcode.addEventListener('click', function () {
            console.log("clicked");
            let audioURL;
            const barcode = manualBarcodeInput.value.trim();

            // Show error if input is empty
            if (!barcode) {
                barcodeError.style.display = 'block'; // Display the error message
                return;
            }

            // Hide error if input is valid
            barcodeError.style.display = 'none';

            const slug = "{{ $store->slug }}";
            const baseURL = "{{ url('/') }}";
            const url = `${baseURL}/user-cart-item/${slug}/scanner?barcode=${barcode}`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.status === 'success') {
                        audioURL = "{{ $successAudioPath }}";
                        const cartItemsCountElements = document.getElementById('cart-item-count');
                        if (cartItemsCountElements) {
                            cartItemsCountElements.textContent = `(${result.cart_items})`;
                        }
                        show_toastr('success', result.message, "success");
                        manualBarcodeModal.hide();
                    } else {
                        audioURL = "{{ $failurAudioPath }}";
                        show_toastr('Error', result.error, 'error');
                    }

                    // Play Sound
                    if (audioURL) playAudio(audioURL);
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while processing the barcode.");
                });
        });
    });
</script>



{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script> --}}

{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const scannerContainer = document.getElementById('scanner-container');
    const scanResult = document.getElementById('scan-result');
    const manualBarcodeButton = document.getElementById('manualBarcodeButton');
    const manualBarcodeModal = new bootstrap.Modal(document.getElementById('manualBarcodeModal'));
    const submitManualBarcode = document.getElementById('submitManualBarcode');
    const manualBarcodeInput = document.getElementById('manualBarcodeInput');
    const barcodeError = document.getElementById('barcodeError');
    let isProcessing = true; // Prevent multiple detections of the same barcode

    const translations = {
        scanning: @json(__('Scanning... Please wait')),
        barcodeDetected: @json(__('Barcode detected')),
        cameraError: @json(__('Error initializing scanner. Please check your camera settings')),
        accessDenied: @json(__('Camera access denied. Please allow camera permissions to use the scanner')),
    };

    // Initialize AudioContext
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    let audioUnlocked = false;

    // Unlock audio playback with a silent interaction
    function unlockAudio() {
        if (audioUnlocked) return;
        const buffer = audioContext.createBuffer(1, audioContext.sampleRate * 0.1, audioContext.sampleRate);
        const source = audioContext.createBufferSource();
        source.buffer = buffer;
        source.connect(audioContext.destination);
        source.start(0);
        audioUnlocked = true;
    }

    // Add event listener to unlock audio on first user interaction
    document.body.addEventListener('click', unlockAudio, { once: true });

    // Function to play audio
    function playAudio(audioURL) {
        fetch(audioURL)
            .then(response => response.arrayBuffer())
            .then(buffer => audioContext.decodeAudioData(buffer))
            .then(decodedData => {
                const source = audioContext.createBufferSource();
                source.buffer = decodedData;
                source.connect(audioContext.destination);
                source.start(0);
            })
            .catch(error => console.error("Audio playback failed:", error));
    }

    // Initialize Quagga barcode scanner
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(function (stream) {
            const video = document.getElementById('scanner-preview');
            video.srcObject = stream;
            video.setAttribute("playsinline", true); // Required for iOS

            Quagga.init(
                {
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: scannerContainer,
                    },
                    decoder: {
                        readers: ["ean_8_reader","upc_reader", "ean_reader","code_39_reader","code_93_reader","code_128_reader"],
                    },
                },
                function (err) {
                    if (err) {
                        console.error(err);
                        alert(translations.cameraError);
                        return;
                    }
                    Quagga.start();
                    scanResult.style.display = 'block';
                    scanResult.textContent = translations.scanning;
                }
            );

            Quagga.onDetected(function (data) {
                let audioURL;
                if (!isProcessing) return;

                isProcessing = false;
                const barcode = data.codeResult.code;

                scanResult.textContent = `${translations.barcodeDetected} ${barcode}`;

                const slug = "{{ $store->slug }}";
                const baseURL = "{{ url('/') }}";
                const url = `${baseURL}/user-cart-item/${slug}/scanner?barcode=${barcode}`;

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.status === 'success') {
                            audioURL = "{{ $successAudioPath }}";
                            const cartItemsCountElements = document.getElementById('cart-item-count');
                            if (cartItemsCountElements) {
                                cartItemsCountElements.textContent = `(${result.cart_items})`;
                            }
                            show_toastr('success', result.message, "success");
                            console.log(result.cart);
                        } else {
                            audioURL = "{{ $failurAudioPath }}";
                            show_toastr('Error', result.error, 'error');
                        }

                        // Play Sound Effect
                        if (audioURL) {
                            playAudio(audioURL);
                        } else {
                            console.error("Audio path is empty");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    })
                    .finally(() => {
                        setTimeout(() => {
                            isProcessing = true;
                            scanResult.textContent = translations.scanning;
                        }, 3000);
                    });
            });
        })
        .catch(function (error) {
            alert(translations.accessDenied);
        });

    // Show modal when "Can't Scan Barcode?" button is clicked
    manualBarcodeButton.addEventListener('click', function () {
        manualBarcodeModal.show();
    });

    // Handle manual barcode submission
    submitManualBarcode.addEventListener('click', function () {
        let audioURL;
        const barcode = manualBarcodeInput.value.trim();

        // Show error if input is empty
        if (!barcode) {
            barcodeError.style.display = 'block'; // Display the error message
            return;
        }

        // Hide error if input is valid
        barcodeError.style.display = 'none';

        const slug = "{{ $store->slug }}";
        const baseURL = "{{ url('/') }}";
        const url = `${baseURL}/user-cart-item/${slug}/scanner?barcode=${barcode}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.json())
            .then((result) => {
                if (result.status === 'success') {
                    audioURL = "{{ $successAudioPath }}";
                    const cartItemsCountElements = document.getElementById('cart-item-count');
                    if (cartItemsCountElements) {
                        cartItemsCountElements.textContent = `(${result.cart_items})`;
                    }
                    show_toastr('success', result.message, "success");
                    manualBarcodeModal.hide();
                } else {
                    audioURL = "{{ $failurAudioPath }}";
                    show_toastr('Error', result.error, 'error');
                }

                // Play Sound Effect
                if (audioURL) {
                    playAudio(audioURL);
                } else {
                    console.error("Audio path is empty");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while processing the barcode.");
            });
    });
});

</script> --}}
@endpush
