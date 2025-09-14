<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
    <style>
        body{
            min-height: 100vh;
        }
        .waiting-container {
            display: flex;
            padding: 10px;
            text-align: center;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .spinner {
            margin: 100px auto;
            width: 100px;
            height: 100px;
            border: 5px solid lightgray;
            border-top: 5px solid #8492a6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="waiting-container">
        <div class="spinner"></div>
        <p>Processing your payment... Please wait.</p>
    </div>

    <script>
        const order_id = "{{ $order_id }}";
        const slug = "{{ $slug }}";
        const paymentStatusBaseUrl = "{{ route('payment.status', ['slug' => $slug, 'order_id' => $order_id]) }}";

        async function checkOrderStatus() {
            try {
                $.ajax({
                    url: "{{ route('edfapay.check') }}",
                    type: 'GET',
                    headers: {
                        'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        order_id: order_id,
                        slug: slug
                    },
                    success: function (response) {
                        if (response.status !== 'pending') {
                            // Redirect to the final status page
                            window.location.href = paymentStatusBaseUrl;
                        } else {
                            // Poll again after 1 second
                            setTimeout(checkOrderStatus, 3000);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error checking order status:', xhr.responseText);
                        setTimeout(checkOrderStatus, 3000);
                    }
                });

            } catch (error) {
                console.error('Error checking order status:', error);
                setTimeout(checkOrderStatus, 3000);
            }
        }

        // Start polling
        checkOrderStatus();
    </script>
</body>
</html>
