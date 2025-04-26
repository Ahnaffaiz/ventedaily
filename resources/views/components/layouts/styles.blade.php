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

    /* Enhanced preloader styling */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99999; /* Extremely high z-index */
        background-color: #ffffff;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.5s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark #preloader {
        background-color: #1e293b;
    }

    /* Hide all content during page load */
    body.page-loading .wrapper,
    body.page-loading main,
    body.page-loading [x-data],
    body.page-loading .modal,
    body.page-loading .fc-dropdown,
    body.page-loading [wire\:model] {
        visibility: hidden !important;
        opacity: 0 !important;
    }
</style>


<!-- Theme Config Js -->
<script src="{{ asset('assets/js/config.js') }}"></script>
@stack('styles')
