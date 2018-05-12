@extends('layouts.app')

<?php
$loggedIn = Auth::check();
$user = Auth::user();
?>

<style>
    p {
        margin: 0px !important;
    }

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
    <div class="container-fluid">
        <div class="row content">

            <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="one">
                <div class="row justify-content-center">
                    <div class="container-fluid">
                        <div class="card card-default">
                            <div class="card-header"><strong>Settings</strong></div>

                            <div class="card-body">
                                <ul class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action">Settings</a>
                                    <a href="#" class="list-group-item list-group-item-action">Saved Posts</a>
                                    <a href="#" class="list-group-item list-group-item-action">Friends</a>
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
                            <div class="card-header"><strong>Settings</strong></div>

                            <div class="card-body">
                                <p>This div will contain content based on the choice in the sidebar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-3 col-lg-3 sidenav" id="three">
                <div class="row justify-content-center">
                    <div class="container-fluid">
                        <div class="card card-default">
                            <div class="card-header"><strong>Statistics</strong></div>

                            <div class="card-body">
                                <p>Some basic information about the user (number of friends, number of times your profile has been viewed).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
