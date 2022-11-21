<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Dashboard</title>
    <style>
        .chatUser {
            width: 400px;
            height: 100%;
            position: fixed;
            background: #ffdd34;
            border-radius: 12px;
        }

        .chatbox {
            width: 400px;
            height: 400px;
            background: #000;
            color: white;
            overflow-y: scroll;
            float: right;
            display: none;
            flex-direction: column-reverse;
        }

        .chatbox::-webkit-scrollbar {
            width: 7px;
        }

        .chatbox::-webkit-scrollbar-track {
            color: rgba(58, 23, 23, 0.3);
            border-radius: 10px;
        }

        .chatbox::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: rgb(123, 145, 63);
        }

        ​ .messages {
            position: relative;
            border: 1px solid grey;
        }

        .messages .sender {
            width: 80%;
            float: right;
            background: grey;
            margin: 10px 0px;
            border-radius: 25px;
            height: auto;
        }

        .messages .receiver {
            width: 80%;
            float: left;
            background: rgb(123, 145, 63);
            margin: 10px 0px;
            border-radius: 25px;
            height: auto;
        }

        .messageBox input[type=text] {
            width: 67%;
            padding: 10px;
        }

        .messageBox input[name=send] {
            width: 25%;
            padding: 10px;
        }

        .messageBox {
            margin-bottom: auto;
            display: flex;
            flex-direction: row;
            position: absolute;
            bottom: 55px;
            width: 408px;
        }

        .chatUser ul {
            padding: 0;
        }

        .chatUser ul li {
            list-style-type: none;
        }

        .chatIcon {
            cursor: pointer;
            background: #e0e0e0;
            padding: 20px;
        }
    </style>
</head>

<body>
    <h2>welcome {{ auth()->user()->name }}</h2>
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <input type="submit" name="logout" value="logout">
    </form>

    <div class="chatUser">
        <ul>
            @foreach ($user as $use)
                <li>
                    <div class="chatIcon" onclick="getChatBox({{ $use->id }})">
                        <span>{{ $use->name }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="chatbox" id="messages">
        <div class="messages">

        </div>
        <form method="post" class="messageBox">
            @csrf
            <input type="text" name="message" />
            <input type='hidden' name='receiver_id' id='receiver_id' value=''>
            <input type="submit" name="send" value="send" />
        </form>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(() => {
                var check = checkChatStatus();
                if (check == true) {
                    getRefreshChatBox();
                }
            }, 5000);
        });

        function checkChatStatus() {
            var check = $.trim($(".messages").html()) == '' ? false : true;
            return check;
        }

        function getRefreshChatBox() {
            var arr = [];
            var idd = $("#receiver_id").val();
            $('.messages>span p').each(function() {
                arr.push($(this).attr('id'));
            });
            var formdata = {
                "_token": "{{ csrf_token() }}",
                receiver_id: idd,
                sender_id: {{ auth()->user()->id }},
                message_id: {
                    arr
                },
            };
            if(arr.length>0)
            {$.ajax({
                type: "get",
                url: "{{ route('getRefreshMessage', '') }}",
                data: formdata,
                dataType: "json",
                success: function(data) {
                        data.forEach(element => {
                            if (element.user_messages.receiver_id == {{ auth()->user()->id }} && element
                                .user_messages.sender_id == idd) {
                                $(".messages").append("<span class='receiver'><p id='" + element.id + "'>" +
                                    element.message + "</p></span>");
                            }
                        });
                }
            });
        }
        }

        function getChatBox(id) {
            $(".messages").empty();
            var chatbox = $('#messages');
            $("#receiver_id").val("");
            chatbox.css('display', 'flex');
            $.ajax({
                type: "get",
                url: "{{ route('getMessage', '') }}/" + id,
                dataType: "json",
                success: function(data) {
                    var input = id;
                    data.forEach(element => {
                        if (element.user_messages.sender_id == {{ auth()->user()->id }} && element
                            .user_messages.receiver_id == id) {
                            $(".messages").append("<span class='sender'><p id='" + element.id + "'>" +
                                element.message + "</p></span>");
                        }
                        if (element.user_messages.receiver_id == {{ auth()->user()->id }} && element
                            .user_messages.sender_id == id) {
                            $(".messages").append("<span class='receiver'><p id='" + element.id + "'>" +
                                element.message + "</p></span>");
                        }
                    });
                    $("#receiver_id").val(input);
                }
            });
        }
        $(".messageBox").submit(function(e) {
            e.preventDefault();
            var receive = $("#receiver_id").val();
            var formdata = {
                "_token": "{{ csrf_token() }}",
                receiver_id: receive,
                message: $("input[name='message']").val(),
            };
            $.ajax({
                type: "POST",
                url: "{{ route('sendmessage') }}",
                data: formdata,
                dataType: "json",
            }).done(function(data) {
                $("input[name='message']").val("");
                getChatBox(receive);
            });
        });
    </script>
</body>

</html>
