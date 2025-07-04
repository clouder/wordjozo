<?php

use App\Livewire\Books\Chapters\Show;
use App\Models\Book;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(Show::class, ['book' => Book::first()])
        ->assertStatus(200);
});
