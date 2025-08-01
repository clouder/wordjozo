<section class="w-full">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @foreach($books as $book)
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    <div class="absolute inset-0 flex items-center justify-center align-middle">
                        <span class="text-3xl font-semibold text-gray-800 dark:text-gray-200">{{ $book->title }}</span>
                        <flux:link wire:navigate href="{{ route('books.show', ['book' => $book->id]) }}" class="absolute w-full h-full"
                        ></flux:link>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
