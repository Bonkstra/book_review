<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when($title, fn ($query, $title) => $query->title($title));

        $books = match ($filter) { // MATCH IS LIKE A SWITCH CASE BUT YOU CAN RETURN VALUES
            'popular_last_week' => $books->popularLastWeek(),
            'popular_last_month' => $books->popularLastMonth(),
            'popular_all_time' => $books->popularAllTime(),
            'highest_rated_last_week' => $books->highestRatedLastWeek(),
            'highest_rated_last_moth' => $books->highestRatedLastMonth(),
            'highest_rated_all_time' => $books->highestRatedAllTime(),
            default => $books->popular()->minReviews(2)->HighestRated()->latest(),
        };

        $books = Cache::remember('books:' . $filter . ':' . $title, 3600, fn () => $books->get());

        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book = Cache::remember('book:' . $book->id, 3600, fn () => $book->load([
            'reviews' => fn ($query) => $query->latest(),
        ]));

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
