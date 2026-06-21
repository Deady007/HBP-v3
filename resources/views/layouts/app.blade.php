@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@stop

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="manifest" href="/manifest.json">
<style>
    /* Global UI improvements */
    .card { border-radius: 10px !important; box-shadow: 0 2px 8px rgba(0,0,0,.08) !important; }
    .card-header { border-radius: 10px 10px 0 0 !important; }
    .btn { border-radius: 6px !important; }
    .form-control { border-radius: 6px !important; }
    .table th { background-color: #14B8A6 !important; color: #fff !important; font-size: .85rem; letter-spacing: .03em; }
    .table-hover tbody tr:hover { background-color: #f0fdfa !important; }
    .badge { border-radius: 4px !important; font-size: .78rem; }
    /* Status badges */
    .status-approved   { background:#d1fae5; color:#065f46; }
    .status-pending    { background:#fef9c3; color:#78350f; }
    .status-rejected   { background:#fee2e2; color:#991b1b; }
    .status-emergency  { background:#fce7f3; color:#9d174d; }
    /* Toastr positioning */
    #toast-container { top: 70px !important; right: 15px !important; }
    /* Mobile tweaks */
    @media (max-width: 576px) {
        .btn { padding: .3rem .6rem; font-size: .82rem; }
        .card-body { padding: .75rem; }
        .table td, .table th { font-size: .78rem; padding: 6px 8px; }
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2();
        flatpickr('.datepicker', { dateFormat: 'Y-m-d' });

        // Global Toastr config
        toastr.options = {
            positionClass: 'toast-top-right',
            timeOut: 4000,
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
        };

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    });
</script>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js');
    }
</script>

@stack('scripts')
@stop