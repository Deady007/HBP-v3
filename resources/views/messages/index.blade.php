@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="card card-primary card-outline direct-chat direct-chat-primary">
        <div class="card-header">
            
            <h3 class="card-title"> {{ Auth::user()->name }}</h3>
            
            <div class="card-tools">
                <!-- <span data-toggle="tooltip" title="3 New Messages" class="badge badge-light">3</span> -->
                <!-- <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button> -->
                <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                    <i class="fas fa-comments"></i>
                </button>
                <!-- <button type="button" class="btn btn-tool" data-widget="remove"><i class="fas fa-times"></i>
                </button> -->
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages" id="chat-box">
                <p>Select a user to start chatting</p>
            </div>
            <!--/.direct-chat-messages-->
            <!-- Contacts are loaded here -->
            <div class="direct-chat-contacts">
                <ul class="contacts-list">
                    @foreach($users as $user)
                        <li>
                            <a href="#" class="user" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                <!-- <img class="contacts-list-img" src="/path/to/user/image.jpg" alt="User Image"> -->
                                <div class="contacts-list-info">
                                    <span class="contacts-list-name">
                                        {{ $user->name }}
                                        <small class="contacts-list-date float-right">Last seen</small>
                                    </span>
                                    <!-- <span class="contacts-list-msg">Click to chat</span> -->
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <!-- /.contacts-list -->
            </div>
            <!-- /.direct-chat-pane -->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <form action="#" method="post">
                <div class="input-group">
                    <input type="hidden" id="receiver_id">
                    <input type="text" id="message" name="message" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-append">
                        <button type="button" id="send-message" class="btn btn-primary">Send</button>
                    </span>
                </div>
            </form>
        </div>
        <!-- /.card-footer-->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let receiver_id;
    let receiver_name;
    let lastRightClickTime = 0;

    $(".user").click(function() {
        receiver_id = $(this).data('id');
        receiver_name = $(this).data('name');
        $("#receiver_id").val(receiver_id);
        $("#chat-box").html(`<p>Chatting with ${receiver_name}</p>`);
        loadMessages(receiver_id);
    });

    function loadMessages(receiver_id) {
        $.get(`/messages/${receiver_id}`, function(messages) {
            $("#chat-box").html(`<p>Chatting with ${receiver_name}</p>`);
            messages.forEach(msg => {
                let align = msg.sender_id == {{ Auth::id() }} ? 'right' : '';
                let bgColor = msg.sender_id == {{ Auth::id() }} ? 'bg-blue-100' : 'bg-gray-100';
                let time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                $("#chat-box").append(`
                    <div class="direct-chat-msg ${align}">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-${align ? 'right' : 'left'}">${msg.sender_id == {{ Auth::id() }} ? 'You' : receiver_name}</span>
                            <span class="direct-chat-timestamp float-${align ? 'left' : 'right'}">${time}</span>
                        </div>
                        
                        <div class="direct-chat-text ${bgColor}">
                            ${msg.message}
                        </div>
                        <button class="delete-message hidden bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </div>
                `);
            });

            $(".direct-chat-msg").on('contextmenu', function(e) {
                e.preventDefault();
                let currentTime = new Date().getTime();
                if (currentTime - lastRightClickTime < 500) {
                    $(this).find(".delete-message").toggleClass("hidden");
                }
                lastRightClickTime = currentTime;
            });

            $(".delete-message").click(function() {
                let messageId = $(this).closest(".direct-chat-msg").data("id");
                deleteMessage(messageId);
            });
        }).fail(function() {
            toastr.error('Failed to load messages.');
        });
    }

    $("#send-message").click(function() {
        sendMessage();
    });

    $("#message").keypress(function(e) {
        if (e.which == 13) {
            sendMessage();
            return false; // Prevent the default form submission
        }
    });

    function sendMessage() {
        let message = $("#message").val();
        if (message.trim() === '') return;

        $.post("/messages/send", {
            _token: "{{ csrf_token() }}",
            receiver_id: $("#receiver_id").val(),
            message: message
        }, function(msg) {
            $("#message").val('');
            loadMessages(receiver_id);
        }).fail(function() {
            toastr.error('Failed to send message.');
        });
    }

    function deleteMessage(messageId) {
        $.ajax({
            url: `/messages/${messageId}`,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(result) {
                loadMessages(receiver_id);
            },
            fail: function() {
                toastr.error('Failed to delete message.');
            }
        });
    }
});
</script>
@endsection