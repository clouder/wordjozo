<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Book;

class Show extends Component
{
    public Book $book;

    public function render()
    {
        return view('livewire.books.show');
    }
}
