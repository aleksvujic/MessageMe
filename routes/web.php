<?php

use App\User;
use App\Message;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Auth::routes();

Route::get('/dashboard', 'DashboardController@index');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/messages', function() {
        $userId = Auth::id();

        // pridobi ustrezna sporocila za trenutno prijavljeno osebo
        $result = Message::where('to', '=', $userId)->orWhere('from', '=', $userId)->orderBy('created_at', 'desc')->get();

        // pridobi samo zadnje sporocilo med dvema osebama
        $pari = array();

        for ($i = 0; $i < count($result); $i++) {
            $id = $result[$i]['id'];
            $from = intval($result[$i]['from']);
            $to = intval($result[$i]['to']);
            $content = $result[$i]['content'];
            $time = date('H:i', strtotime($result[$i]['created_at']));

            if ($from == $userId) {
                $person = User::find($to)->name;
            } else if ($to == $userId) {
                $person = User::find($from)->name;
            }

            $found = false;
            for ($j = 0; $j < count($pari); $j++) {
                if (($pari[$j]['from'] === $from && $pari[$j]['to'] === $to) || ($pari[$j]['from'] === $to && $pari[$j]['to'] === $from)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                array_push($pari, ['id' => $id, 'from' => $from, 'to' => $to, 'content' => $content, 'person' => $person, 'time' => $time]);
            }
        }

        $recentMessages = $pari;

        if (count($result) > 0) {
            if ($result[0]['from'] == $userId) {
                $latestPerson = $result[0]['to'];
            } else {
                $latestPerson = $result[0]['from'];
            }

            $latestPersonName = User::find($latestPerson)->name;

            $latestPersonID = User::find($latestPerson)->id;

            $latestConversation =
                Message::where(function($q) use($userId, $latestPerson) {
                    $q->where('to', $userId)->where('from', $latestPerson);
                })
                    ->orWhere(function($q) use($userId, $latestPerson) {
                        $q->where('to', $latestPerson)->where('from', $userId);
                    })
                    ->orderBy('created_at')
                    ->get();

            return view('messages', ['recentMessages' => $recentMessages, 'latestPersonName' => $latestPersonName, 'latestConversation' => $latestConversation, 'latestPersonID' => $latestPersonID]);
        }

        return view('messages', ['recentMessages' => NULL, 'latestPersonName' => NULL, 'latestConversation' => NULL, 'latestPersonID' => NULL]);
    });
});

Route::post('/getMessages', 'MessagesController@getMessages');

Route::post('/sendMessage', 'MessagesController@sendMessage');

Route::post('/latestMessages', 'MessagesController@latestMessages');

Route::post('/getID', 'MessagesController@getID');

Route::post('/search', 'MessagesController@search');

Route::post('/usersOnline', 'MessagesController@usersOnline');

Route::post('/numberOfNewMessages', 'MessagesController@numberOfNewMessages');