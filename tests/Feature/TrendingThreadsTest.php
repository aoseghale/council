<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Trending
     */
    protected $trending;

    protected function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();

//        Redis::del('testing_trending_threads');
        $this->trending->reset();
    }

    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
//        $this->assertEmpty(Redis::zrevrange('testing_trending_threads', 0, -1));
        $this->assertEmpty($this->trending->get());

        $thread = create('App\Thread');

        $this->call('GET', $thread->path());

//        $trending = Redis::zrevrange('testing_trending_threads', 0, -1);
        $trending = $this->trending->get();

        $this->assertCount(1, $trending);

//        $this->assertEquals($thread->title, json_decode($trending[0])->title);
        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
