@extends('layouts.app')

@section('content')
<!-- Add DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

<!-- Custom CSS -->
<style>
    body {
        font-family: 'Arial', sans-serif;
    }

    .content {
        background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
    }

    .container {
        max-width: 1200px;
    }

    .bg-white {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .text-gray-700 {
        color: #4a4a4a;
    }

    .text-gray-600 {
        color: #6b7280;
    }

    .rounded {
        border-radius: 8px;
    }

    .hover\:bg-green-600:hover {
        background-color: #2d6a4f;
    }

    .table th {
        background-color: #4caf50;
        color: white;
        font-weight: bold;
    }

    .table td {
        color: #333;
    }

    #patients-table_filter input {
        border: 2px solid #4caf50;
        border-radius: 5px;
        padding: 5px 10px;
    }

    #patients-table_wrapper .dt-buttons button {
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 5px;
        cursor: pointer;
    }

    #patients-table_wrapper .dt-buttons button:hover {
        background-color: #388e3c;
    }
</style>

<div class="content p-6 bg-gray-100 min-h-screen">
    <section class="mb-6">
        <div class="container mx-auto">
            <div class="flex justify-between items-center bg-white p-4 rounded shadow">
                <h1 class="text-2xl font-bold text-gray-700">My Patients</h1>
                <a href="{{ route('admin.patient.create') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Add Patient</a>
            </div>
        </div>
    </section>

    <section class="mt-6">
        <div class="container mx-auto">
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-600 mb-4">Patients List</h2>
                <table id="patients-table" class="min-w-full bg-white border border-gray-300 table">
                    <thead>
                        <tr>
                            <th class="py-4 px-6 text-left text-sm font-bold border-b border-gray-300 bg-green-500 text-white">Full Name</th>
                            <th class="py-4 px-6 text-left text-sm font-bold border-b border-gray-300 bg-green-500 text-white">Email</th>
                            <th class="py-4 px-6 text-left text-sm font-bold border-b border-gray-300 bg-green-500 text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this section -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <style>
        #patients-table tbody tr:hover {
            background-color: #f1f5f9; /* Light gray background on hover */
            transition: background-color 0.3s ease;
        }

        #patients-table tbody tr:hover td {
            color: #2d6a4f; /* Vibrant green text on hover */
            font-weight: bold;
        }

        #patients-table th {
            background-color: #14B8A6;
            color: white;
            font-weight: bold;
        }

        #patients-table td {
            color: #333;
            padding: 10px;
        }
    </style>
</div>

<!-- Add jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#patients-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.patient.getData') }}',
            columns: [
                { data: 'full_name', name: 'full_name' },
                { data: 'email', name: 'email' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            "paging": true,     
            "searching": true,
            "ordering": true,
            "destroy": true,
            "dom": '<"top"lfB>rt<"bottom"ip><"clear">', // Add export buttons to "top"
            "buttons": [
                {
                    extend: 'collection',
                    text: 'Export Options',
                    className: 'btn btn-success', // Custom class for styling
                    buttons: [
                        {
                            extend: 'copy',
                            text: 'Copy to Clipboard',
                            className: 'btn btn-light'
                        },
                        {
                            extend: 'csv',
                            text: 'Export as CSV',
                            className: 'btn btn-light'
                        },
                        {
                            extend: 'excel',
                            text: 'Export as Excel',
                            className: 'btn btn-light'
                        },
                        {
                            extend: 'pdf',
                            text: 'Export as PDF',
                            className: 'btn btn-light'
                        },
                        {
                            extend: 'print',
                            text: 'Print Table',
                            className: 'btn btn-light'
                        }
                    ]
                }
            ],
            "renderer": "semanticUI"
        }).buttons().container().appendTo('#patients-table_wrapper .col-md-6:eq(0)');

        $('#patients-table_filter input')
            .attr('placeholder', 'Search Patients...')
            .css({
                'color': 'black',
                'font-weight': 'bold'
            });
    });
</script>
@endsection
