<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\User;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        // Inspect the body of the reply for username mentions
////        preg_match_all('/\@([^\s\.]+)/', $event->reply->body, $matches);
////        $names = $matches[1];
//        $mentionedUsers = $event->reply->mentionedUsers();
//
//        // And then for each mentioned user, notify them.
//        foreach ($mentionedUsers as $name) {
//            if ($user = User::whereName($name)->first()) {
//                $user->notify(new YouWereMentioned($event->reply));
//            }
//        }

        // REFACTOR using collect
//        collect($event->reply->mentionedUsers())
//            ->map(function ($name) {
//                return User::whereName($name)->first();
//            })
//            ->filter() // filter strips out null values
//            ->each(function ($user) use ($event) {
//                $user->notify(new YouWereMentioned($event->reply));
//            });

        // REFACTOR
        User::whereIn('name', $event->reply->mentionedUsers())
            ->get()
            ->each(function ($user) use ($event) {
                $user->notify(new YouWereMentioned($event->reply));
            });
    }
}
