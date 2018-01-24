<?php

namespace App;

use Laravel\Scout\Searchable;
use App\Events\ThreadReceivedNewReply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    /**
     * The relationships to always eager-load.
     *
     * @var array
     */
    protected $with = ['creator', 'channel'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['isSubscribedTo', 'visits'];

    protected $casts = [
        'locked' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            // $thread->replies()->delete();
            // the below forces the trigger of the static deleting for Reply defined in RecordsActivity
            $thread->replies->each->delete();

            Reputation::reduce($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);

            Reputation::award($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });
    }

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    /**
     * A thread belongs to a creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread belongs to a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Add a reply to the thread.
     *
     * @param array $reply
     * @return Reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        // REMOVED since now using event ThreadReceivedNewReply
//        $this->notifySubscribers($reply);

        // Prepare notifications for all subscribers.
//        foreach ($this->subscriptions as $subscription) {
//            if ($subscription->user_id != $reply->user_id) {
//                $subscription->user->notify(new ThreadWasUpdated($this, $reply));
//            }
//        }

        return $reply;
    }

    // NOT NECESSARY SINCE NOW USING events
//    public function notifySubscribers($reply)
//    {
//        $this->subscriptions
//            ->where('user_id', '!=', $reply->user_id)
    ////            ->filter(function ($sub) use ($reply) {
    ////                return $sub->user_id != $reply->user_id;
    ////            })
    ////            ->each(function ($sub) use ($reply) {
    ////                $sub->notify($reply));
    ////            });
//            ->each
//            ->notify($reply);
//    }

    /**
     * Apply all relevant thread filters.
     *
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * Unsubscribe a user from the current thread.
     *
     * @param null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * A thread can have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Determine if the current user is subscribed to the thread.
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * Determine if the thread has been updated since the user last read it.
     *
     * @param User $user
     * @return bool
     */
    public function hasUpdatesFor($user = null)
    {
        $user = $user ?: auth()->user();

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function visits()
    {
        return new Visits($this);
    }

    public function getVisitsAttribute()
    {
        return (int) $this->visits()->count();
    }

    /**
     * Set the proper slug attribute.
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        $original = $slug;
        $count = 2;

        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-".$count++;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * Mark the given reply as the best answer.
     *
     * @param Reply $reply
     */
    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);

        Reputation::award($reply->owner, Reputation::BEST_REPLY_AWARDED);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
