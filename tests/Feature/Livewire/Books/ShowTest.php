<?php

use App\Livewire\Books\Show;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    $book = Book::first();
    Livewire::test(Show::class, ['book' => $book])
        ->assertStatus(200);
});
