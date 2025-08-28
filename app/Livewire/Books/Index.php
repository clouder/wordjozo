<?php

namespace App\Livewire\Books;

use Livewire\Component;

class Index extends Component
{
    public $books;

    public $continueReading = null;

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

        // Find where the user should continue reading
        $this->continueReading = $this->findNextChapter($userId);

        return view('livewire.books.index', ['books' => $this->books, 'continueReading' => $this->continueReading]);
    }

    protected function findNextChapter($userId): ?array
    {
        // Find the most recently updated chapter summary
        $lastChapter = \App\Models\Chapter::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (! $lastChapter) {
            // No chapters yet, start with Genesis chapter 1
            $firstBook = \App\Models\Book::first();

            return [
                'book' => $firstBook,
                'chapter' => 1,
                'is_new_book' => true,
                'is_new_user' => true,
                'message' => 'Start your Bible journey!',
            ];
        }

        $currentBook = \App\Models\Book::find($lastChapter->book_id);
        $nextChapter = $lastChapter->number + 1;

        // Check if there's a next chapter in the current book
        if ($nextChapter <= $currentBook->chapter_count) {
            return [
                'book' => $currentBook,
                'chapter' => $nextChapter,
                'is_new_book' => false,
                'message' => 'Continue with '.$currentBook->title,
            ];
        }

        // Find the next book
        $nextBook = \App\Models\Book::where('id', '>', $currentBook->id)->first();

        if (! $nextBook) {
            // User has completed the entire Bible!
            return [
                'book' => $currentBook,
                'chapter' => $lastChapter->number,
                'is_completed' => true,
                'message' => 'Congratulations! You\'ve completed the Bible!',
            ];
        }

        return [
            'book' => $nextBook,
            'chapter' => 1,
            'is_new_book' => true,
            'is_new_user' => false,
            'message' => 'Begin '.$nextBook->title,
        ];
    }

    public function continueToChapter(): void
    {
        if ($this->continueReading && ! isset($this->continueReading['is_completed'])) {
            $this->redirect(route('books.chapters.show', [
                'book' => $this->continueReading['book']->id,
                'chapter' => $this->continueReading['chapter'],
            ]));
        }
    }
}
