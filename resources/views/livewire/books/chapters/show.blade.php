<section class="w-full">
    <div class="mb-4">
        <flux:heading size="xl" class="text-center">
            <flux:link wire:navigate variant="ghost" href="{{ route('books.show', ['book' => $book->id]) }}">
                {{ $book->title }}
            </flux:link>
        </flux:heading>
        <flux:heading size="lg" class="text-center">{{ __('Chapter') }} {{ $chapter_number }}</flux:heading>
    </div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4">
            <flux:textarea wire:model.live.debounce.500ms="summary" label="Summary" rows="auto" class="w-full" />
        </div>
    </div>
</section>
