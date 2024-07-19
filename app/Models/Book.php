<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Cache;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder | QueryBuilder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopeWithReviewsCount(Builder $query, $from = NULL, $to = NULL): Builder | QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn (Builder $sub_query) => $this->dateRangeFilter($sub_query, $from, $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = NULL, $to = NULL): Builder | QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn (Builder $sub_query) => $this->dateRangeFilter($sub_query, $from, $to)
        ], 'rating');
    }

    public function scopePopular(Builder $query, $from = NULL, $to = NULL): Builder | QueryBuilder
    {
        return $query->WithReviewsCount()->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = NULL, $to = NULL): Builder | QueryBuilder
    {
        return $query->WithAvgRating()->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder | QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    public function scopePopularLastWeek(Builder $query): Builder | QueryBuilder
    {
        return $query->popular(now()->subWeek(), now())->highestRated(now()->subWeek(), now())->minReviews(2);
    }

    public function scopePopularLastMonth(Builder $query): Builder | QueryBuilder
    {
        return $query->popular(now()->subMonth(), now())->highestRated(now()->subMonth(), now())->minReviews(2);
    }

    public function scopePopularAllTime(Builder $query): Builder | QueryBuilder
    {
        return $query->popular()->highestRated()->minReviews(2);
    }

    public function scopeHighestRatedLastWeek(Builder $query): Builder | QueryBuilder
    {
        return $query->highestRated(now()->subWeek(), now())->highestRated(now()->subWeek(), now())->popular(now()->subWeek(), now())->minReviews(2);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder | QueryBuilder
    {
        return $query->highestRated(now()->subMonth(), now())->highestRated(now()->subMonth(), now())->popular(now()->subMonth(), now())->minReviews(2);
    }

    public function scopeHighestRatedAllTime(Builder $query): Builder | QueryBuilder
    {
        return $query->highestRated()->highestRated()->popular()->minReviews(2);
    }

    private function dateRangeFilter(Builder $query, $from, $to)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    protected static function booted()
    {
        // IF A BOOK IS UPDATED OR DELETED, DELETE THE BOOK CACHE
        static::updated(fn (Book $book) => Cache::forget('book:' . $book->id));
        static::deleted(fn (Book $book) => Cache::forget('book:' . $book->id));
    }
}
