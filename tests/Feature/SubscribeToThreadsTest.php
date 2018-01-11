<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        // Given we have a thread ...
        $thread = create('App\Thread');

        // And the user subscribes to the thread
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->fresh()->subscriptions);

//        $this->assertCount(0, auth()->user()->notifications);
//
//        // Then each time a new reply is left...
//        $thread->addReply([
//            'user_id' => auth()->id(),
//            'body' => 'Some reply here'
//        ]);
//
//        // A notification should be prepared for the user
//        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }
    
    /** @test */
    function a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();

        // Given we have a thread ...
        $thread = create('App\Thread');

        $thread->subscribe();

        // And the user deletes the subscription to the thread
        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
