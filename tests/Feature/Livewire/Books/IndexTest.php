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

it('suggests starting with Genesis chapter 1 for new users', function () {
    $user = \App\Models\User::factory()->create();

    $component = Livewire::actingAs($user)->test(Index::class);
    $continueReading = $component->get('continueReading');

    expect($continueReading['book']->title)->toBe('Genesis');
    expect($continueReading['chapter'])->toBe(1);
    expect($continueReading['is_new_book'])->toBeTrue();
    expect($continueReading['is_new_user'])->toBeTrue();
    expect($continueReading['message'])->toBe('Start your Bible journey!');
});

it('suggests next chapter in same book based on latest activity', function () {
    $user = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first(); // Genesis

    // User has completed chapters 1, 2, 3, with chapter 3 being most recent
    \App\Models\Chapter::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'number' => 1,
        'updated_at' => now()->subHours(3),
    ]);

    \App\Models\Chapter::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'number' => 2,
        'updated_at' => now()->subHours(2),
    ]);

    $latestChapter = \App\Models\Chapter::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'number' => 3,
        'updated_at' => now()->subHour(),
    ]);

    $component = Livewire::actingAs($user)->test(Index::class);
    $continueReading = $component->get('continueReading');

    expect($continueReading['book']->id)->toBe($book->id);
    expect($continueReading['chapter'])->toBe(4); // Next chapter after 3
    expect($continueReading['is_new_book'])->toBeFalse();
    expect($continueReading['message'])->toBe('Continue with Genesis');
});

it('suggests first chapter of next book when current book is completed', function () {
    $user = \App\Models\User::factory()->create();
    $firstBook = \App\Models\Book::first(); // Genesis
    $secondBook = \App\Models\Book::skip(1)->first(); // Exodus

    // User completes all chapters of Genesis, with the last chapter being most recent
    for ($i = 1; $i <= $firstBook->chapter_count; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user->id,
            'book_id' => $firstBook->id,
            'number' => $i,
            'updated_at' => now()->subHours($firstBook->chapter_count - $i),
        ]);
    }

    $component = Livewire::actingAs($user)->test(Index::class);
    $continueReading = $component->get('continueReading');

    expect($continueReading['book']->id)->toBe($secondBook->id);
    expect($continueReading['chapter'])->toBe(1);
    expect($continueReading['is_new_book'])->toBeTrue();
    expect($continueReading['is_new_user'])->toBeFalse();
    expect($continueReading['message'])->toBe('Begin Exodus');
});

it('can navigate to continue reading chapter', function () {
    $user = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first();

    // User has completed chapter 1
    \App\Models\Chapter::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'number' => 1,
    ]);

    $component = Livewire::actingAs($user)->test(Index::class);

    $component->call('continueToChapter')
        ->assertRedirect(route('books.chapters.show', ['book' => $book->id, 'chapter' => 2]));
});

it('shows continue reading section in the UI for users with progress', function () {
    $user = \App\Models\User::factory()->create();
    $book = \App\Models\Book::first();

    // User has completed chapter 1
    \App\Models\Chapter::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'number' => 1,
    ]);

    $response = $this->actingAs($user)->get('/books');

    $response->assertStatus(200);
    $response->assertSee('Continue Your Journey');
    $response->assertSee('Continue with Genesis');
    $response->assertSee('Chapter 2');
});

it('shows welcome message for new users', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get('/books');

    $response->assertStatus(200);
    $response->assertSee('Welcome to WordJozo');
    $response->assertSee('Start your Bible journey!');
    $response->assertSee('Begin your study with');
    $response->assertSee('Genesis');
    $response->assertSee('Chapter 1');
    $response->assertSee('Start Reading');
    $response->assertDontSee('Continue Your Journey');
    $response->assertDontSee('Congratulations!');
});

it('shows book completion celebration for returning users starting new book', function () {
    $user = \App\Models\User::factory()->create();
    $firstBook = \App\Models\Book::first(); // Genesis

    // User completes all chapters of Genesis
    for ($i = 1; $i <= $firstBook->chapter_count; $i++) {
        \App\Models\Chapter::factory()->create([
            'user_id' => $user->id,
            'book_id' => $firstBook->id,
            'number' => $i,
            'updated_at' => now()->subHours($firstBook->chapter_count - $i),
        ]);
    }

    $response = $this->actingAs($user)->get('/books');

    $response->assertStatus(200);
    $response->assertSee('Continue Your Journey');
    $response->assertSee('Begin Exodus');
    $response->assertSee('Ready to start a new book?');
    $response->assertSee('Congratulations!');
    $response->assertSee('completed Genesis');
    $response->assertSee('Begin');
});
