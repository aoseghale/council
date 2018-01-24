<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Favorite;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * FavoritesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Reply $reply)
    {
//        Favorite::create([
//            'user_id' => auth()->id(),
//            'favorited_id' => $reply->id,
//            'favorited_type' => get_class($reply),
//        ]);

        // $reply->favorites()->create(['user_id' => auth()->id(),]);

        $reply->favorite();

        if (request()->expectsJson()) {
            return response(['status' => 'Favorite created']);
        }

        return back();
    }

    /**
     * @param Reply $reply
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        if (request()->expectsJson()) {
            return response(['status' => 'Favorite deleted']);
        }
    }
}
