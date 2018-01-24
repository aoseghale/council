<?php

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * Filter the query by a given username.
     *
     * @param $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     * @return $this
     */
    protected function popular()
    {
        // clear existing orders
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Filter the query to display unanswered threads.
     * @return mixed
     */
    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}
