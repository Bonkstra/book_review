<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['review', 'rating'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    protected static function booted()
    {
        // IF A REVIEW IS UPDATED OR DELETED, DELETE THE BOOK CACHE
        static::updated(fn (Review $review) => Cache::forget('book:' . $review->book->id));
        static::deleted(fn (Review $review) => Cache::forget('book:' . $review->book->id));
    }
}
