<?php

namespace App\Livewire\Books\Chapters;

use App\Models\Book;
use App\Models\Chapter;
use Livewire\Component;

class Show extends Component
{
    public Book $book;

    public $chapter;

    public $summary;

    public $chapter_number;

    public $nextChapter = null;

    public $nextBook = null;

    public $isLastChapterOfBook = false;

    public function mount(Book $book)
    {
        $this->book = $book;

        $this->chapter_number = request()->route('chapter');
        if ($this->chapter_number > $this->book->chapter_count) {
            abort(404, 'Chapter not found');
        }

        $this->chapter = Chapter::query()
            ->where('user_id', auth()->id())
            ->where('book_id', $this->book->id)
            ->where('number', $this->chapter_number)
            ->first();

        if ($this->chapter) {
            $this->summary = $this->chapter->summary;
        }

        $this->calculateNextChapter();
    }

    public function updatedChapterNumber(): void
    {
        $this->calculateNextChapter();
    }

    public function calculateNextChapter(): void
    {
        if ($this->chapter_number < $this->book->chapter_count) {
            // There's a next chapter in this book
            $this->nextChapter = $this->chapter_number + 1;
            $this->isLastChapterOfBook = false;
        } else {
            // This is the last chapter, find the next book
            $this->isLastChapterOfBook = true;
            $this->nextBook = Book::where('id', '>', $this->book->id)->first();

            if ($this->nextBook) {
                $this->nextChapter = 1;
            }
        }
    }

    public function goToNextChapter(): void
    {
        if ($this->nextChapter) {
            if ($this->isLastChapterOfBook && $this->nextBook) {
                // Go to first chapter of next book
                $this->redirect(route('books.chapters.show', [
                    'book' => $this->nextBook->id,
                    'chapter' => 1,
                ]));
            } else {
                // Go to next chapter in current book
                $this->redirect(route('books.chapters.show', [
                    'book' => $this->book->id,
                    'chapter' => $this->nextChapter,
                ]));
            }
        }
    }

    public function updatedSummary($value)
    {
        $value = trim($value);

        if (! $value) {
            // If the summary is empty, delete the chapter entry
            Chapter::query()
                ->where('user_id', auth()->id())
                ->where('book_id', $this->book->id)
                ->where('number', $this->chapter_number)
                ->delete();

            $this->chapter = new Chapter;

            return;
        }

        $this->chapter = Chapter::query()
            ->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'book_id' => $this->book->id,
                    'number' => $this->chapter_number,
                ],
                ['summary' => $value]
            );
    }

    public function render()
    {
        return view('livewire.books.chapters.show-audio');
    }
}
