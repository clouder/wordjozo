<?php

use App\Livewire\Books\Chapters\Show;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(Show::class, ['book' => Book::first()])
        ->assertStatus(200);
});
