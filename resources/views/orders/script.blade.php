<script src="{{ asset('custom/js/jquery.min.js') }} "></script>
<script src="{{ asset('custom/js/html2pdf.bundle.min.js') }}"></script>

<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{ asset('custom/libs/select2/dist/js/select2.min.js')}}"></script>
<script src="{{ asset('custom/libs/moment/min/moment.min.js')}}"></script>
<script src="{{ asset('custom/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js')}}"></script>
<script src="{{ asset('custom/libs/fullcalendar/dist/fullcalendar.min.js')}}"></script>
<script src="{{ asset('custom/libs/flatpickr/dist/flatpickr.min.js')}}"></script>
<script src="{{ asset('custom/libs/quill/dist/quill.min.js')}}"></script>
<script src="{{ asset('custom/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{ asset('custom/libs/autosize/dist/autosize.min.js')}}"></script>

<script>
    function closeScript() {
        setTimeout(function () {
            window.open(window.location, '_self').close();
        }, 1000);
    }

    $( document ).ready(function() {
        var element = document.getElementById('boxes');
        var opt = {
            margin:       0.5,
            filename:     'Order',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 4, dpi: 72, letterRendering: true },
            jsPDF:        { unit: 'in', format: 'letter' },
            pagebreak:    { avoid: ['tr','td']}
        };
        html2pdf().set(opt).from(element).save().then(closeScript);
    });

</script>
