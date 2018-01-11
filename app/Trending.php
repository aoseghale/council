<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get()
    {
//        $trending = collect(Redis::zrevrange('trending_threads', 0, 4))->map(function ($thread) {
//            return json_decode($thread);
//        });

        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    protected function cacheKey()
    {
        return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
    }
}