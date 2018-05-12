@extends('layouts.app')

<?php
$loggedIn = Auth::check();
$user = Auth::user();
?>

<style>
    .list-group-item a, .list-group-item a:hover {
        color: black;
    }

    @media screen and (max-width: 767px) {
        .sidenav {
            height: auto;
        }
        .row.content {
            height: auto;
        }

        .col-sm, .col-md, .col-lg {
            display: flex; flex-flow: column;
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
                                <div class="card-header"><strong>Navigation</strong></div>

                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item"><a href="/">Home</a></li>
                                        <li class="list-group-item"><a href="messages">Messages</a></li>
                                        <li class="list-group-item"><a href="#section3">People</a></li>
                                        <li class="list-group-item"><a href="#section3">Photos</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6" id="two">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Recent Posts</strong></div>

                                <div class="card-body">
                                    {{--<img src="img/image.jpg" class="img-fluid">--}}
                                    <p>Body content</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="three">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Dashboard</strong></div>

                                <div class="card-body">
                                    You are logged in as <strong>{{$user->name}}</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <div class="row content">

                <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="one">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Navigation</strong></div>

                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item"><a href="#section1">Home</a></li>
                                        <li class="list-group-item"><a href="#section2">Messages</a></li>
                                        <li class="list-group-item"><a href="#section3">People</a></li>
                                        <li class="list-group-item"><a href="#section3">Photos</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6" id="two">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Recent Posts</strong></div>

                                <div class="card-body">
                                    <strong>You are not logged in!</strong><hr>
                                    Here will be recent posts.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="three">
                    <div class="row justify-content-center">
                        <div class="container-fluid">
                            <div class="card card-default">
                                <div class="card-header"><strong>Dashboard</strong></div>

                                <div class="card-body">
                                    You are not logged in!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection