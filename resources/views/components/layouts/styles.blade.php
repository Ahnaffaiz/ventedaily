<!-- App css -->
<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">

<!-- Icons css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
<style>
    /* Disable input number arrows */
    input.no-arrow::-webkit-outer-spin-button,
    input.no-arrow::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input.no-arrow {
        -moz-appearance: textfield;
    }
</style>


<!-- Theme Config Js -->
<script src="{{ asset('assets/js/config.js') }}"></script>
@stack('styles')
