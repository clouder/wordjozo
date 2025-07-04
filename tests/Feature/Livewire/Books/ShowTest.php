<?php

use App\Livewire\Books\Show;
use App\Models\Book;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    $book = Book::first();
    Livewire::test(Show::class, ['book' => $book])
        ->assertStatus(200);
});
