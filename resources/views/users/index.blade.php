@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Users Management</h2>
        <a class="btn btn-success mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Create New User</a>
    </div>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideIn 2s;
        }

        #users-table_filter input {
            width: 600px !important;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            color: black !important;
            font-weight: bold;
        }
    </style>

    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession

    <div class="bg-white shadow-lg rounded-lg">
        <div class="bg-teal-500 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="text-lg font-semibold">Users List</h3>
            <div id="customSearchContainer"></div>
        </div>

        <div class="p-4">
            @if($users->count() > 0)
            <table id="users-table" class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">No</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Name</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Email</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Phone Number</th>
						<th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Roles</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700" width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $loop->iteration }}</td>
                        <td class="py-2 px-4">{{ $user->name }}</td>
                        <td class="py-2 px-4">{{ $user->email }}</td>
                        <td class="py-2 px-4">{{ $user->phone_number }}</td>
						<td class="py-2 px-4">
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <span class="badge bg-success">{{ $v }}</span>
                        @endforeach
                    @endif
                </td>
                        <td class="py-2 px-4">
                            <a class="btn btn-info btn-sm" href="{{ route('users.show', $user->id) }}"><i class="fas fa-list"></i> Show</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('users.edit', $user->id) }}"><i class="fas fa-pencil-alt"></i> Edit</a>
                            <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>No users available.</p>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#users-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "dom": '<"top"f>rt<"bottom"lp><"clear">'
        });

        $('#users-table_filter').detach().appendTo('#customSearchContainer');

        $('#users-table_filter label').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();

        $('#users-table_filter input')
            .attr('placeholder', 'Search Users...')
            .css({
                'color': 'black',
                'font-weight': 'bold'
            });

        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                if (!confirm('Are you sure you want to delete this user?')) return;
                fetch(`/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`user-row-${userId}`).remove();
                        } else {
                            toastr.error('Error deleting user. Please try again.');
                        }
                    });
            });
        });
    });
</script>
@endsection
