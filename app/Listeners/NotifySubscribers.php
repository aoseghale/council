<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;

class NotifySubscribers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
//        $event->reply->thread->notifySubscribers($event->reply);

        // DO NOT notifySubscribers CODE directly here
        $thread = $event->reply->thread;

        $thread->subscriptions
            ->where('user_id', '!=', $event->reply->user_id)
//            ->filter(function ($sub) use ($reply) {
//                return $sub->user_id != $reply->user_id;
//            })
//            ->each(function ($sub) use ($reply) {
//                $sub->notify($reply));
//            });
            ->each
            ->notify($event->reply);

    }
}
