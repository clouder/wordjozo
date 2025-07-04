<section class="w-full">
    <div class="mb-4">
        <flux:heading size="xl" class="text-center">{{ $book->title }}</flux:heading>
        <flux:heading size="lg" class="text-center">{{ $book->chapter_count }} {{ __('Chapters') }}</flux:heading>
    </div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @foreach($chapters as $chapter)
                <div
                    @class([
                        "relative aspect-video overflow-hidden rounded-xl border",
                        $chapter['summarized'] ? 'border-green-500 dark:border-green-400' : 'border-neutral-200 dark:border-neutral-700',
                    ])
                >
                    <x-placeholder-pattern
                        @class([
                            'absolute inset-0 size-full',
                            $chapter['summarized'] ? 'stroke-green-500/20 dark:stroke-green-400/20' : 'stroke-gray-900/20 dark:stroke-neutral-100/20',
                        ])
                    />
                    <div class="absolute inset-0 flex items-center justify-center align-middle">
                        <span class="text-3xl font-semibold text-gray-800 dark:text-gray-200">{{ $chapter['number'] }}</span>
                        <flux:link wire:navigate href="{{ route('books.chapters.show', ['book' => $book->id, 'chapter' => $chapter['number']]) }}" class="absolute w-full h-full"></flux:link>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
