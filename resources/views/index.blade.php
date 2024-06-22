<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="{{asset('style.css')}}">
  <script>

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    // var pusher = new Pusher('0f04730c30d2c485a972', {
    //   cluster: 'ap2'
    // });

    // var channel = pusher.subscribe('my-channel');
    // channel.bind('my-event', function(data) {
    //   alert(JSON.stringify(data));
    // });
  </script>
</head>
<body>
  <div class="chat">
    <div class="top">
        <img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="">
        <div>
            <p>Ross Edlin</p>
            <small>Oline</small>
        </div>
    </div>
    <div class="message">
        @include('receive', ['message' => "Hey! What's up"])
    </div>
    <div class="bottom">
        <form>
            <input type="text" name="message" id="message" placeholder="Enter Message......">
            <button type="submit"></button>
        </form>
    </div>
  </div>

  <script>
    const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'eu'})
    const channel = pusher.subscribe('public')
    
    // recevie message 
    channel.bind('chat', function(data) {
        $.post("/receive", {
            _token: '{{csrf_token()}}',
            message: data.message,
        })
        .done( function(res){
            $(".message > .message").last().after(res);
            (document).scrollTop($(document).height());
        })
    })


    // broadcast message
    $("form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: "/broadcast",
            method: "POST",
            headers : {
                'X-Socket-Id': pusher.connection.socket_id,
            }, 
            data : {
                _token : '{{csrf_token()}}',
                message : $("form #message").val(),
            }

        }).done(function(res){
            $(".message > .message").last().after(res);
            $("form #message").val('');
            $(document).scrollTop($(document).height());
        })
    })
  </script>
</body>