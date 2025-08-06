<?php

use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedTinyInteger('chapter_count');
            $table->timestamps();
        });

        Book::insert([
            ['title' => 'Genesis', 'chapter_count' => 50],
            ['title' => 'Exodus', 'chapter_count' => 40],
            ['title' => 'Leviticus', 'chapter_count' => 27],
            ['title' => 'Numbers', 'chapter_count' => 36],
            ['title' => 'Deuteronomy', 'chapter_count' => 34],
            ['title' => 'Joshua', 'chapter_count' => 24],
            ['title' => 'Judges', 'chapter_count' => 21],
            ['title' => 'Ruth', 'chapter_count' => 4],
            ['title' => '1 Samuel', 'chapter_count' => 31],
            ['title' => '2 Samuel', 'chapter_count' => 24],
            ['title' => '1 Kings', 'chapter_count' => 22],
            ['title' => '2 Kings', 'chapter_count' => 25],
            ['title' => '1 Chronicles', 'chapter_count' => 29],
            ['title' => '2 Chronicles', 'chapter_count' => 36],
            ['title' => 'Ezra', 'chapter_count' => 10],
            ['title' => 'Nehemiah', 'chapter_count' => 13],
            ['title' => 'Esther', 'chapter_count' => 10],
            ['title' => 'Job', 'chapter_count' => 42],
            ['title' => 'Psalms', 'chapter_count' => 150],
            ['title' => 'Proverbs', 'chapter_count' => 31],
            ['title' => 'Ecclesiastes', 'chapter_count' => 12],
            ['title' => 'Song of Solomon', 'chapter_count' => 8],
            ['title' => 'Isaiah', 'chapter_count' => 66],
            ['title' => 'Jeremiah', 'chapter_count' => 52],
            ['title' => 'Lamentations', 'chapter_count' => 5],
            ['title' => 'Ezekiel', 'chapter_count' => 48],
            ['title' => 'Daniel', 'chapter_count' => 12],
            ['title' => 'Hosea', 'chapter_count' => 14],
            ['title' => 'Joel', 'chapter_count' => 3],
            ['title' => 'Amos', 'chapter_count' => 9],
            ['title' => 'Obadiah', 'chapter_count' => 1],
            ['title' => 'Jonah', 'chapter_count' => 4],
            ['title' => 'Micah', 'chapter_count' => 7],
            ['title' => 'Nahum', 'chapter_count' => 3],
            ['title' => 'Habakkuk', 'chapter_count' => 3],
            ['title' => 'Zephaniah', 'chapter_count' => 3],
            ['title' => 'Haggai', 'chapter_count' => 2],
            ['title' => 'Zechariah', 'chapter_count' => 14],
            ['title' => 'Malachi', 'chapter_count' => 4],
            ['title' => 'Matthew', 'chapter_count' => 28],
            ['title' => 'Mark', 'chapter_count' => 16],
            ['title' => 'Luke', 'chapter_count' => 24],
            ['title' => 'John', 'chapter_count' => 21],
            ['title' => 'Acts', 'chapter_count' => 28],
            ['title' => 'Romans', 'chapter_count' => 16],
            ['title' => '1 Corinthians', 'chapter_count' => 16],
            ['title' => '2 Corinthians', 'chapter_count' => 13],
            ['title' => 'Galatians', 'chapter_count' => 6],
            ['title' => 'Ephesians', 'chapter_count' => 6],
            ['title' => 'Philippians', 'chapter_count' => 4],
            ['title' => 'Colossians', 'chapter_count' => 4],
            ['title' => '1 Thessalonians', 'chapter_count' => 5],
            ['title' => '2 Thessalonians', 'chapter_count' => 3],
            ['title' => '1 Timothy', 'chapter_count' => 6],
            ['title' => '2 Timothy', 'chapter_count' => 4],
            ['title' => 'Titus', 'chapter_count' => 3],
            ['title' => 'Philemon', 'chapter_count' => 1],
            ['title' => 'Hebrews', 'chapter_count' => 13],
            ['title' => 'James', 'chapter_count' => 5],
            ['title' => '1 Peter', 'chapter_count' => 5],
            ['title' => '2 Peter', 'chapter_count' => 3],
            ['title' => '1 John', 'chapter_count' => 5],
            ['title' => '2 John', 'chapter_count' => 1],
            ['title' => '3 John', 'chapter_count' => 1],
            ['title' => 'Jude', 'chapter_count' => 1],
            ['title' => 'Revelation', 'chapter_count' => 22],
        ]);
    }
};
