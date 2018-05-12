@extends('layouts.app')

<?php
$loggedIn = Auth::check();
$userId = Auth::id();
$name = Auth::user()->name;
$otherId = $latestPersonID;
?>

<style>
    #fileUpload {
        display: none;
    }

    form {
        margin-bottom: 0px !important;
    }

    a {
        cursor: pointer;
    }

    #suggestionBox p {
        padding-top: 18px;
        margin-bottom: 0 !important;
    }

    #messageTextarea {
        resize: none !important;
        overflow: hidden !important;
        padding: 7px !important;
        display: block !important;
        border-radius:5px !important;
    }

    #scrollDown {
        max-height: 25em;
        overflow-y: auto;
    }

    #recentMessages {
        max-height: 23em !important;
        overflow-y: auto;
    }

    .list-group-item a, .list-group-item a:hover {
        color: black;
    }

    .list-group-item-action {
        padding: .75rem 1.25rem !important;
    }

    .list-group-flush .list-group-item-action {
        padding: 3px !important;
    }

    @media screen and (max-width: 767px) {
        .sidenav {
            height: auto;
        }

        .row.content {
            height: auto;
        }

        .col-sm, .col-md, .col-lg {
            display: flex;
            flex-flow: column;
        }
        #one {
            order: 3;
        }

        #two {
            order: 1;
            padding-bottom: 10px;
        }

        #three {
            order: 2;
            padding-bottom: 10px;
        }
    }
</style>

@section('content')
    @if ($loggedIn)
        <div class="container-fluid">
            <div class="row content">

                <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="one">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Messages</strong></div>

                                <div class="card-body" id="recentMessagesBody">
                                    <div class="list-group" id="recentMessages" style="max-height: 25em; overflow-y: auto;">
                                        @if (count($recentMessages) > 0)
                                            @for ($i = 0; $i < count($recentMessages); $i++)
                                                @if ($i == 0)
                                                    <a class="list-group-item list-group-item-action" style="background-color: #ededef;">
                                                @else
                                                    <a class="list-group-item list-group-item-action">
                                                @endif

                                                @php
                                                if (strlen($recentMessages[$i]['content']) >= 50) {
                                                    $message = substr($recentMessages[$i]['content'], 0, 50) . '...';
                                                } else {
                                                    $message = $recentMessages[$i]['content'];
                                                }
                                                @endphp

                                                {{--<span class="badge badge-pill badge-primary">1</span>--}}
                                                {{$recentMessages[$i]['person']}} <small>{{$recentMessages[$i]['time']}}</small><br>
                                                <small>{{$message}}</small>
                                                </a>
                                            @endfor
                                        @else
                                            <p><strong>You don't have any messages!</strong></p><p>Use form in the middle to send new message.</p>
                                        @endif
                                    </div>
                                    <br>
                                    <input class="btn btn-primary btn-sm" id="newMessage" type="submit" value="Send New Message">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6" id="two">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header" id="personName"><strong>{{$latestPersonName}}</strong></div>

                                <div class="card-body" id="scrollDown">
                                    <?php
                                    $prev = -1;
                                    $first = true;
                                    ?>

                                    @if(count($latestConversation) > 0)
                                        @foreach ($latestConversation as $message)
                                            @if ($first)
                                                @if ($userId == $message->from)
                                                    <p class="text-right">
                                                        {{$message->content}}<br>
                                                        <small>{{$name}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                    </p>
                                                @else
                                                    <p class="text-left">
                                                        {{$message->content}}<br>
                                                        <small>{{$latestPersonName}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                    </p>
                                                @endif

                                                @php
                                                    $prev = $message->from;
                                                    $first = false;
                                                @endphp
                                            @else
                                                @if ($message->from == $prev)
                                                    @if ($message->from == $userId)
                                                        <p class="text-right">
                                                            {{$message->content}}<br>
                                                            <small>{{$name}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                        </p>
                                                    @else
                                                        <p class="text-left">
                                                            {{$message->content}}<br>
                                                            <small>{{$latestPersonName}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                        </p>
                                                    @endif
                                                @else
                                                    @if ($message->from == $userId)
                                                        <hr>
                                                        <p class="text-right">
                                                            {{$message->content}}<br>
                                                            <small>{{$name}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                        </p>
                                                    @else
                                                        <hr>
                                                        <p class="text-left">
                                                            {{$message->content}}<br>
                                                            <small>{{$latestPersonName}}, {{date('H:i', strtotime($message->created_at))}}</small>
                                                        </p>
                                                    @endif

                                                    @php
                                                        $prev = $message->from;
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <p>You don't have any messages!</p>
                                    @endif
                                </div>

                                <div class="card-footer">
                                    {!! Form::open(['id' => 'msgForm', 'url' => 'sendMessage']) !!}
                                    <div class="input-group">
                                        {{Form::textarea('message', '', ['rows' => 1, 'class' => 'form-control', 'placeholder' => 'Message...', 'id' => 'messageTextarea'])}}&nbsp;&nbsp;&nbsp;
                                        <span class="input-group-btn">
                                            <input type="file" id="fileUpload">
                                            <button type="button" class="btn btn-sm" id="get_file"><i class="material-icons">attachment</i></button>
                                            {{Form::submit('Send', ['class' => 'btn btn-primary'])}}
                                        </span>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="three">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Users Online</strong></div>

                                <div class="card-body" id="usersOnline" style="padding-bottom: 5px;">
                                    <ul class="list-group list-group-flush" id="usersOnlineList">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    @endif
@endsection

<script>
    window.onload = function() {
        // ajax setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // število novih sporočil v navbaru
        getNumberOfNewMessages();

        // pridobi trenutno prijavljene osebe
        onlineUsers();

        // pridobi zadnja sporocila
        latestMessages();

        // ko se odpre stran, mora biti div scrollan do dna
        var scrollDown = $('#scrollDown');
        var el = document.getElementById('scrollDown');
        el.scrollTop = el.scrollHeight;

        // posiljatelj zadnjega sporocila
        var prev = '{{$prev}}';
        var otherId = '{{$otherId}}';

        // funkcija za dodajanje sporočil v div
        function addMessages(data, mode) {
            var messagesInJson = data.length;
            var currentNumberOfMessages = $('#scrollDown').find('p').length;

            for (var i = currentNumberOfMessages; i < messagesInJson; i++) {
                var time = ((data[i].created_at).split(' ')[1]).split(':');
                var finalTime = time[0] + ":" + time[1];
                var append = "";
                currentNumberOfMessages = $('#scrollDown').find('p').length;

                // sprehod skozi sporocila, ki se niso dodana na stran
                if (currentNumberOfMessages === 0 && mode !== 1) {
                    // first
                    if (data[i].from === '{{$userId}}') {
                        // posiljatelj je uporabnik sam
                        append = "<p class='text-right'>" + data[i].content + "<br><small>" + data[i].userName + ", " + finalTime + "</small></p>";
                    } else {
                        // posiljatelj je druga oseba
                        append = "<p class='text-left'>" + data[i].content + "<br><small>" + data[i].otherName + ", " + finalTime + "</small></p>";
                    }
                } else {
                    console.log("currentNumberOfMessages = " + currentNumberOfMessages);
                    console.log("mode = " + mode);
                    // so ze sporocila
                    if (prev === data[i].from) {
                        // novo sporocilo je od ISTE osebe kot prej
                        if (prev === '{{$userId}}') {
                            // novo sporocilo je od prijavljenega uporabika
                            append = "<p class='text-right'>" + data[i].content + "<br><small>" + data[i].userName + ", " + finalTime + "</small></p>";
                        } else {
                            // novo sporocilo je od drugega uporabika
                            append = "<p class='text-left'>" + data[i].content + "<br><small>" + data[i].otherName + ", " + finalTime + "</small></p>";
                        }
                    } else {
                        // novo sporocilo je od DRUGE osebe kot prej
                        if (data[i].from === '{{$userId}}') {
                            // novo sporocilo je od prijavljenega uporabnika
                            append = "<hr><p class='text-right'>" + data[i].content + "<br><small>" + data[i].userName + ", " + finalTime + "</small></p>";
                        } else {
                            // novo sporocilo ni od prijavljenega uporabnika
                            append = "<hr><p class='text-left'>" + data[i].content + "<br><small>" + data[i].otherName + ", " + finalTime + "</small></p>";
                        }
                    }
                }
                prev = data[i].from;
                scrollDown.append(append);
            }
        }

        // funkcija za pridobivanje sporočil
        function getMessages() {
            $.post(
                'getMessages',
                {
                    userId: '{{$userId}}',
                    otherId: otherId
                },
                function(data) {
                    data = JSON.parse(data);

                    var currentNumberOfMessages = $('#scrollDown > p').length;
                    var messagesInJson = data.length;

                    if (messagesInJson > currentNumberOfMessages) {
                        addMessages(data, 0);
                        scrollDown.animate({scrollTop: scrollDown.prop("scrollHeight")}, 500);
                    }
                }
            );
        }

        // pridobi sporočila vsake 3s
        var getMessagesInterval = window.setInterval(getMessages, 3000);

        $(function() {
            $('#messageTextarea').on('keydown', function(e) {
                if (e.which === 13 && ! e.shiftKey) {
                    // ob pritisku na enter, ko si v textareai, pošlji sporočilo (shift + enter ne pošlje sporočila)
                    e.preventDefault();
                    sendMessage();
                    latestMessages();
                } else {
                    // samodejna razsiritev polja za vnos sporocila
                    var el = this;
                    setTimeout(function() {
                        el.style.cssText = 'height:auto; padding:0';
                        el.style.cssText = 'height:' + el.scrollHeight + 'px';
                    }, 0);
                }
            });
        });

        // posiljanje spročila ob kliku na gumb "Send"
        $('#msgForm').submit(function(e){
            e.preventDefault();
            sendMessage();
            latestMessages();
        });

        // splosna funkcija za posiljanje sporocila
        function sendMessage() {
            $.post(
                'sendMessage',
                {
                    sender: '{{$userId}}',
                    receiver: otherId,
                    message: $('#messageTextarea').val()
                },
                function(data) {
                    var textarea = $('#messageTextarea');
                    textarea.val('');
                    textarea.height('auto');

                    $('#recentMessages').animate({ scrollTop: 0 }, 'fast');

                    // sporočilo takoj dodaj v div, ne čakaj na naslednji setInverval
                    getMessages();
                }
            )
        }

        // poskrbi, da je gumb "Send New Message" kliknjen samo enkrat, ce uporabnik nima se nobenega sporocila
        var clicked = false;

        // get latest messages (sidebar)
        function latestMessages() {
            $.post(
                'latestMessages',
                {
                    userId: '{{$userId}}'
                },
                function(data) {
                    var recentMessages = $('#recentMessages');
                    var currentName = $('#personName').text();
                    recentMessages.empty();

                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var dodaj = "";

                            if (data[i].person === currentName) {
                                // trenutno odprto osebo pobarvamo s temnejšo barvo
                                dodaj += "<a class='list-group-item list-group-item-action' style='background-color: #ededef;'>";
                            } else {
                                // osebe, ki niso odprte, ne pobarvamo
                                dodaj += "<a class='list-group-item list-group-item-action'>";
                            }

                            var message = data[i].content;
                            if (message.length >= 50) {
                                // trim
                                message = message.substring(0, 50) + '...';
                            }

                            // ce je oseba poslala kaksno novo sporocilo, dodaj stevilo novih sporocil
                            if (data[i].unread != 0) {
                                dodaj += "<span class='badge badge-pill badge-primary'>" + data[i].unread + "</span>&nbsp";
                            }

                            // element dodamo na seznam
                            var append = dodaj + data[i].person + " <small>" + data[i].time + "</small><br><small>" + message + "</small></a>";
                            recentMessages.append(append);
                        }
                    } else {
                        append = "<p><strong>You don't have any messages!</strong></p><p>Use form in the middle to send new message.</p>";
                        recentMessages.append(append);
                        if (!clicked) {
                            $('#newMessage').click();
                            clicked = true;
                        }
                    }
                }
            );
        }

        // function gets called every 3s
        window.setInterval(latestMessages, 3000);

        // pridobi število novih sporočil za navbar
        function getNumberOfNewMessages() {
            $.ajax({
                type: 'POST',
                url: 'numberOfNewMessages',
                data: {
                    userId: '{{ $userId }}'
                },
                success: function (data) {
                    if (data > 0) {
                        $('#supNewMessages').show();
                        $('#newMessages').html(data);
                    } else {
                        $('#supNewMessages').hide();
                    }
                }
            });
        }

        // spremeni osebo, s katero trenutno klepetas
        $('#recentMessages').on('click', '.list-group-item', function(e) {
            // pridobi kliknjeno ime
            var name = $(e.target).text().trim();

            // odstrani vse nepotrebne znake
            name = name.replace(/(\r\n|\n|\r)/gm,"").split(/[0-9]/);

            // če je name[0] prazen, pomeni, da smo kliknili na nekoga z novimi sporočili
            if (!name[0]) {
                name = name[1];
            } else {
                name = name[0];
            }

            // iz imena odstraniš vse presledke
            name = name.trim();

            $.post(
                'getID',
                {
                    name: name
                },
                function(data1) {
                    otherId = data1;
                    $.post(
                        'getMessages',
                        {
                            userId: '{{$userId}}',
                            otherId: otherId
                        },
                        function(data2) {
                            data2 = JSON.parse(data2);

                            // izprazni trenuten div, kjer so sporocila
                            $('#scrollDown').empty();

                            // dodaj nova sporocila in ga scrollaj do dna
                            addMessages(data2, 0);
                            el.scrollTop = el.scrollHeight;

                            // zamenjaj ime osebe na vrhu okna
                            $('#personName').html('<strong>' + data2[0].otherName + '</strong>');

                            // posodobi še sidebar (zadnja sporočila)
                            latestMessages();

                            // posodobi število novih sporočil v navbaru
                            getNumberOfNewMessages();
                        }
                    );
                }
            );
        });

        $('#newMessage').click(function() {
            // začasno ustavi osveževanje sporočil
            clearInterval(getMessagesInterval);

            // izprazni pogovorno okno
            $('#scrollDown').empty();

            // v meniju na levi nobena oseba ne sme biti izbrana
            latestMessages();

            // namesto imena osebe dodaj text input
            var input = "<input type='text' id='searchPerson' placeholder='Name of the person...' class='form-control'>";
            $('#personName').html(input);

            // ob pisanju v ta text input kliči to funkcijo
            $('#searchPerson').keyup(function(e){
                var isWordCharacter = e.key.length === 1;
                var isBackSpace = e.keyCode === 8;

                if ($('#searchPerson').val().length !== 0 && (isWordCharacter || isBackSpace)) {
                    $.ajax({
                        type: 'POST',
                        url: 'search',
                        data: {
                            string: $('#searchPerson').val(),
                            userId: '{{ $userId }}'
                        },
                        beforeSend: function(){
                            var searchPerson = $('#searchPerson');
                            searchPerson.css({'background-image': 'url({{ asset('img/loader.gif') }})',
                                'background-position': 'right top',
                                'background-repeat': 'no-repeat',
                                'background-size': 'contain'});

                            // če suggestionBox div še ne obstaja (še ni bilo iskanj), ga dodaj
                            if ($('#suggestionBox').length === 0) {
                                searchPerson.after("<div id='suggestionBox'></div>");
                            }
                        },
                        success: function (data) {
                            // loader gif odstrani po pol sekunde, saj drugace skoraj ni viden
                            setTimeout(function() {
                                $('#searchPerson').removeAttr('style');
                            }, 800);

                            // priprava unordered lista, kjer bodo rezultati iskanja
                            var suggestionBox = $('#suggestionBox');
                            suggestionBox.html("<ul class='list-group' id='searchResults'></ul>");

                            // dodaj rezulate v unordered list
                            if (data.length === 0) {
                                $('#searchResults').append('<p>No users found!</p>');
                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    $('#searchResults').append("<a id=" + data[i].id + " class='list-group-item list-group-item-action'>" + data[i].name + "</a>");
                                }
                            }

                            // ob kliku na rezultat pokliči to funkcijo
                            $('#searchResults').find('a').on('click', function() {
                                // pridobi kliknjeno ime in ID te osebe
                                var name = $(this).text();
                                otherId = jQuery(this).attr('id');

                                // odstrani polje za iskanje in predloge
                                $('#suggestionBox').remove();
                                $('#searchPerson').remove();

                                // pridobi sporočila med tema dvema osebaba
                                getMessages();

                                // ponovno nastavi interval za osveževanje sporočil
                                getMessagesInterval = setInterval(getMessages, 3000);

                                // zamenjaj ime osebe s katero klepetaš
                                $('#personName').html('<strong>' + name + '</strong>');

                                // pošlji sporočilo
                                sendMessage();
                            });
                        }
                    });
                } else if ($('#searchPerson').val().length === 0) {
                    // če izbrišeš cel input field, odstrani tudi suggestionBox
                    $('#suggestionBox').remove();
                }
            });
        });

        function onlineUsers() {
            $.ajax({
                type: 'POST',
                url: 'usersOnline',
                success: function (data) {
                    data = JSON.parse(data);

                    if (data.length === 1 && data[0].user.id == '{{$userId}}') {
                        // prijavljen si samo ti
                        $('#usersOnlineList').html('<p>No users online!</p>');
                    } else {
                        // prijavljeni so tudi drugi uporabniki
                        $('#usersOnline').css('padding-top', '0px');
                        var usersOnlineList = $('#usersOnlineList');
                        usersOnlineList.empty();
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].user.id != '{{$userId}}') {
                                usersOnlineList.append("<a class='list-group-item list-group-item-action' id=" + data[i].user.id + "><span class='glyphicon glyphicon-one-fine-dot'></span> " + data[i].user.name + "</a>");
                            }
                        }
                    }
                }
            });
        }

        window.setInterval(onlineUsers, 10000);

        $('#usersOnlineList').on('click', '.list-group-item', function() {
            // pridobi kliknjeno ime in ID te osebe
            var name = $(this).text().trim();
            otherId = jQuery(this).attr('id');

            // izprazni trenuten div, kjer so sporocila
            $('#scrollDown').empty();

            // pridobi sporočila med tema dvema osebaba
            getMessages();

            // zamenjaj ime osebe s katero klepetaš
            $('#personName').html('<strong>' + name + '</strong>');

            // posodobi še sidebar (zadnja sporočila)
            latestMessages();
        });

        document.getElementById('get_file').onclick = function() {
            document.getElementById('fileUpload').click();
        };

        document.getElementById('fileUpload').onchange = function() {
            console.log('image selected!');
            sendMessage();
            //document.getElementById('msgForm').submit();
        };
    }
</script>