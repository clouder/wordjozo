<?php

use App\Livewire\Books\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

it('shows no progress indicators for unstarted books', function () {
    $user = \App\Models\User::factory()->create();

    $component = Livewire::actingAs($user)->test(Index::class);

    $books = $component->get('books');

    // All books should have no progress
    foreach ($books as $book) {
        expect($book->is_started)->toBeFalse();
        expect($book->completed_chapters)->toBe(0);
        expect($book->progress_percentage)->toBe(0);
    }
});

it('shows progress indicator for books in progress', function () {
    $user = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first(); // Genesis - 50 chapters

    // Create 10 completed chapters (20% progress)
    for ($i = 1; $i <= 10; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'number' => $i,
        ]);
    }

    $component = Livewire::actingAs($user)->test(Index::class);

    $books = $component->get('books');
    $testBook = $books->firstWhere('id', $book->id);

    expect($testBook->completed_chapters)->toBe(10);
    expect($testBook->progress_percentage)->toBe(20);
    expect($testBook->is_started)->toBeTrue();
    expect($testBook->is_completed)->toBeFalse();
});

it('shows complete indicator for finished books', function () {
    $user = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first(); // Genesis - 50 chapters

    // Create all 50 chapters as completed
    for ($i = 1; $i <= $book->chapter_count; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'number' => $i,
        ]);
    }

    $component = Livewire::actingAs($user)->test(Index::class);

    $books = $component->get('books');
    $testBook = $books->firstWhere('id', $book->id);

    expect($testBook->completed_chapters)->toBe(50);
    expect($testBook->progress_percentage)->toBe(100);
    expect($testBook->is_started)->toBeTrue();
    expect($testBook->is_completed)->toBeTrue();
});

it('calculates progress correctly for different users', function () {
    $user1 = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first();

    // User 1 completes 10 chapters
    for ($i = 1; $i <= 10; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user1->id,
            'book_id' => $book->id,
            'number' => $i,
        ]);
    }

    // User 2 completes 25 chapters
    for ($i = 1; $i <= 25; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book->id,
            'number' => $i,
        ]);
    }

    // Test user 1's progress
    $component1 = Livewire::actingAs($user1)->test(Index::class);
    $books1 = $component1->get('books');
    $testBook1 = $books1->firstWhere('id', $book->id);

    expect($testBook1->completed_chapters)->toBe(10);
    expect($testBook1->progress_percentage)->toBe(20);

    // Test user 2's progress
    $component2 = Livewire::actingAs($user2)->test(Index::class);
    $books2 = $component2->get('books');
    $testBook2 = $books2->firstWhere('id', $book->id);

    expect($testBook2->completed_chapters)->toBe(25);
    expect($testBook2->progress_percentage)->toBe(50);
});
