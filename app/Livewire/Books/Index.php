<?php

namespace App\Livewire\Books;

use Livewire\Component;

class Index extends Component
{
    public $books;

    public function render()
    {
        $this->books = \App\Models\Book::all();

        return view('livewire.books.index', ['books' => $this->books]);
    }
}
