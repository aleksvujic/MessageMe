<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use DB;
use Activity;

class MessagesController extends Controller
{
    public function getMessages(Request $request) {
        if ($request->isMethod('post')){
            $userId = $request->userId;
            $latestPerson = $request->otherId;

            // pridobi zadnja sporočila med tema dvema osebama (urejena po datumu)
            $latestConversation =
                Message::where(function($q) use($userId, $latestPerson) {
                    $q->where('to', $userId)->where('from', $latestPerson);
                })
                    ->orWhere(function($q) use($userId, $latestPerson) {
                        $q->where('to', $latestPerson)->where('from', $userId);
                    })
                    ->orderBy('created_at')
                    ->get();

            // posodobi še števec neprebranih sporočil na 0
            Message::where('from', $latestPerson)->where('to', $userId)->update(['opened' => true]);

            // poišči imena oseb, ki so se pogovarjale
            for ($i = 0; $i < count($latestConversation); $i++) {
                $latestConversation[$i]['userName'] = User::find($userId)->name;
                $latestConversation[$i]['otherName'] = User::find($latestPerson)->name;
            }

            return json_encode($latestConversation);
        }
    }

    public function sendMessage(Request $request) {
        if ($request->isMethod('post')) {
            if (strlen($request->message) === 0) {
                return;
            }

            $sender = $request->sender;
            $receiver = $request->receiver;
            $content = $request->message;

            $message = new Message;
            $message->from = $sender;
            $message->to = $receiver;
            $message->content = $content;
            $message->created_at = \Carbon\Carbon::now('Europe/Ljubljana');
            $message->opened = false;

            $message->save();
        }
    }

    public function latestMessages(Request $request) {
        if ($request->isMethod('post')) {
            $userId = $request->userId;

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
                $otherIDs = array();

                if ($from == $userId) {
                    $person = User::find($to)->name;
                    $otherID = $to;
                } else if ($to == $userId) {
                    $person = User::find($from)->name;
                    $otherID = $from;
                }

                $found = false;
                for ($j = 0; $j < count($pari); $j++) {
                    if (($pari[$j]['from'] === $from && $pari[$j]['to'] === $to) || ($pari[$j]['from'] === $to && $pari[$j]['to'] === $from)) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    array_push($otherIDs, $otherID);
                    array_push($pari, ['unread' => 0, 'id' => $id, 'from' => $from, 'to' => $to, 'content' => $content, 'person' => $person, 'time' => $time]);
                }
            }

            // za osebo z ID dobiš število neprebranih sporočil
            $newMessages = DB::table('messages')->selectRaw("`from`, COUNT(id) AS `numberOfUnread`")->where('to', '=', intval($userId))->where('opened', '=', false)->groupBy('from')->get();
            $newMessages = json_decode($newMessages, true);

            $sum = 0;

            for ($i = 0; $i < count($newMessages); $i++) {
                $from = $newMessages[$i]['from'];
                $numberOfUnread = intval($newMessages[$i]['numberOfUnread']);

                $sum += $numberOfUnread;

                for ($j = 0; $j < count($pari); $j++) {
                    if ($pari[$j]['from'] == $from) {
                        $pari[$j]['unread'] = $numberOfUnread;
                        break;
                    }
                }
            }

            return $pari;
        }
    }

    public function getID(Request $request) {
        if ($request->isMethod('post')) {
            $id = (User::where('name', $request->name)->select('id')->first())->id;
            return $id;
        }
    }

    public function search(Request $request) {
        if (empty($request->string)) {
            return null;
        }

        // poisci vse uporabnike, razen samega sebe
        $users = DB::table('users')->where('id', '!=', $request->userId)->get();
        $string = strtolower($request->string);
        $array = array();

        for ($i = 0; $i < count($users); $i++) {
            // ime splitaš po presledkih in pogledaš vsakega, če se ujema
            $name = explode(' ', strtolower($users[$i]->name));

            for ($j = 0; $j < count($name); $j++) {
                if (strpos($name[$j], $string) === 0) {
                    // ime uporabnika v tabeli se začne s tem, kar smo vpisali v iskanje
                    array_push($array, $users[$i]);
                    break;
                }
            }

            if (count($array) > 5) {
                return $array;
            }
        }

        return $array;
    }

    // TODO
    public function usersOnline() {
        // poisci nazadnje aktivne uporabnike
        $activities = Activity::users(10)->get()->toJson();

        return $activities;
    }

    public function numberOfNewMessages(Request $request) {
        if ($request->isMethod('post')) {
            $userId = $request->userId;

            // prestej vsa neprebrana sporočila
            $sum = DB::table('messages')->selectRaw('COUNT(*) AS sum')->where('to', $userId)->where('opened', false)->pluck('sum');
            return intval($sum[0]);
        }
    }
}