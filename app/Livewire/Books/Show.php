<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Book;
use Illuminate\Support\Arr;

class Show extends Component
{
    public Book $book;
    public $summarized;
    public $chapters;

    public function mount()
    {
        $this->chapters = range(1, $this->book->chapter_count);

        $this->summarized =  $this->book->chapters()
            ->where('user_id', auth()->id())
            ->pluck('number');

        $this->chapters = Arr::map($this->chapters, function ($chapter) {
            return [
                'number' => $chapter,
                'summarized' => $this->summarized->contains($chapter),
            ];
        });
    }

    public function render()
    {
        return view('livewire.books.show');
    }
}
