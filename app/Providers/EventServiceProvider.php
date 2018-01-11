<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ThreadReceivedNewReply' => [
            'App\Listeners\NotifyMentionedUsers',
            'App\Listeners\NotifySubscribers',
        ],
//        'App\Events\Event' => [
//            'App\Listeners\EventListener',
//        ],

        // Not necessary, since its only one listener -- DIRECT should suffice
//        Registered::class => [
//            'App\Listeners\SendEmailConfirmationRequest'
//        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
