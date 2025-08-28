<?php

namespace App\Livewire\Books;

use Livewire\Component;

class Index extends Component
{
    public $books;

    public function render()
    {
        $userId = auth()->id();

        $this->books = \App\Models\Book::all()->map(function ($book) use ($userId) {
            // Get completed chapters count for this user
            $completedChapters = $book->chapters()
                ->where('user_id', $userId)
                ->count();

            // Calculate progress percentage
            $progressPercentage = $book->chapter_count > 0
                ? (int) round(($completedChapters / $book->chapter_count) * 100)
                : 0;

            // Add progress data to the book object
            $book->completed_chapters = $completedChapters;
            $book->progress_percentage = $progressPercentage;
            $book->is_started = $completedChapters > 0;
            $book->is_completed = $completedChapters >= $book->chapter_count;

            return $book;
        });

        return view('livewire.books.index', ['books' => $this->books]);
    }
}
