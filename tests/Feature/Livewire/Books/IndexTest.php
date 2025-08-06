<?php

use App\Livewire\Books\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});
