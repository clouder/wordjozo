<?php

use App\Livewire\Books\Index;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});
