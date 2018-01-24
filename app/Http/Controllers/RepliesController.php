<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use App\Http\Requests\CreatePostRequest;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * @param int $channelId
     * @param Thread $thread
     * @param CreatePostRequest $request
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread, CreatePostRequest $request)
    {
        if ($thread->locked) {
            return response('Thread is locked', 422);
        }

        // NOW INCLUDED IN CreatePostForm
//        if (Gate::denies('create', new Reply)) {
//            return response(
//                'You are posting too frequently. Please take a break. :)', 429
//            );
//        }

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        // USING event ThreadReceivedNewReply inside Thread.addReply
//        // Inspect the body of the reply for username mentions
//        preg_match_all('/\@([^\s\.]+)/', $reply->body, $matches);
//        $names = $matches[1];
//
//        // And then for each mentioned user, notify them.
//        foreach ($names as $name) {
//            $user = User::whereName($name)->first();
//
//            if ($user) {
//                $user->notify(new YouWereMentioned($reply));
//            }
//        }

        return $reply->load('owner');

//        if (request()->expectsJson()) {
//            return $reply->load('owner');
//        }
//        return $reply->load('owner');

        // return redirect($thread->path());
//        return back()
//            ->with('flash', 'Your reply has been left');
    }

    /**
     * Update an existing reply.
     *
     * @param Reply $reply
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        request()->validate(['body' => 'required|spamfree']);

        // $reply->update(['body' => request('body')]);
        $reply->update(request(['body']));
    }

    /**
     * @param Reply $reply
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
