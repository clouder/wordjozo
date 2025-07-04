<?php

use App\Livewire\Books\Show;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Show::class)
        ->assertStatus(200);
});
