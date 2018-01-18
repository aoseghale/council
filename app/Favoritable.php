<?php
/**
 * Created by PhpStorm.
 * User: okhae
 * Date: 7/22/17
 * Time: 8:24 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * A reply has favorites
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * Favorite the current reply.
     *
     * @return Model
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];

        if (!$this->favorites()->where($attributes)->exists()) {
            Reputation::award(auth()->user(), Reputation::REPLY_FAVORITED);

            return $this->favorites()->create($attributes);
        }
    }

    /**
     * Unfavorite the current reply.
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

//        $this->favorites()->where($attributes)->get()->each(function ($favorite) {
//            $favorite->delete();
//        });
        // Same as above
        $this->favorites()->where($attributes)->get()->each->delete();

        Reputation::reduce(auth()->user(), Reputation::REPLY_FAVORITED);
    }

    public function isFavorited()
    {
        return !!$this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}