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

it('can navigate to next chapter in same book', function () {
    $book = Book::first(); // Genesis has 50 chapters

    $component = Livewire::test(Show::class, ['book' => $book]);

    // Manually set the state for testing
    $component->set('chapter_number', 1);
    $component->set('nextChapter', 2);
    $component->set('isLastChapterOfBook', false);

    $component->call('goToNextChapter')
        ->assertRedirect(route('books.chapters.show', ['book' => $book->id, 'chapter' => 2]));
});

it('can navigate to first chapter of next book when at last chapter', function () {
    $firstBook = Book::first(); // Genesis
    $secondBook = Book::skip(1)->first(); // Exodus

    $component = Livewire::test(Show::class, ['book' => $firstBook]);

    // Manually set the state for testing the last chapter scenario
    $component->set('chapter_number', $firstBook->chapter_count);
    $component->set('nextChapter', 1);
    $component->set('isLastChapterOfBook', true);
    $component->set('nextBook', $secondBook);

    $component->call('goToNextChapter')
        ->assertRedirect(route('books.chapters.show', ['book' => $secondBook->id, 'chapter' => 1]));
});

it('calculates next chapter correctly for mid-book chapter', function () {
    $book = Book::first();

    $component = new Show;
    $component->book = $book;
    $component->chapter_number = 10;
    $component->calculateNextChapter();

    expect($component->nextChapter)->toBe(11);
    expect($component->isLastChapterOfBook)->toBeFalse();
});

it('calculates next book correctly for last chapter of book', function () {
    $firstBook = Book::first();
    $secondBook = Book::skip(1)->first();

    $component = new Show;
    $component->book = $firstBook;
    $component->chapter_number = $firstBook->chapter_count;
    $component->calculateNextChapter();

    expect($component->nextChapter)->toBe(1);
    expect($component->isLastChapterOfBook)->toBeTrue();
    expect($component->nextBook->id)->toBe($secondBook->id);
});

it('handles last chapter of last book correctly', function () {
    $lastBook = Book::orderBy('id', 'desc')->first(); // Revelation

    $component = new Show;
    $component->book = $lastBook;
    $component->chapter_number = $lastBook->chapter_count; // Chapter 22 of Revelation
    $component->calculateNextChapter();

    expect($component->nextChapter)->toBeNull();
    expect($component->isLastChapterOfBook)->toBeTrue();
    expect($component->nextBook)->toBeNull();
});
