<?php

namespace App\Livewire\Books\Chapters;

use Livewire\Component;
use App\Models\Book;
use App\Models\Chapter;

class Show extends Component
{
    public Book $book;
    public $chapter;
    public $summary;
    public $chapter_number;

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
    }

    public function updatedSummary($value)
    {
        $value = trim($value);

        if (!$value) {
            // If the summary is empty, delete the chapter entry
            Chapter::query()
                ->where('user_id', auth()->id())
                ->where('book_id', $this->book->id)
                ->where('number', $this->chapter_number)
                ->delete();

            $this->chapter = new Chapter();

            return;
        }

        $this->chapter = Chapter::query()
            ->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'book_id' => $this->book->id,
                    'number' => $this->chapter_number
                ],
                ['summary' => $value]
            );
    }

    public function render()
    {
        return view('livewire.books.chapters.show');
    }
}
