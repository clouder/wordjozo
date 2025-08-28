<section class="w-full">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @foreach($books as $book)
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    
                    <!-- Progress indicator -->
                    @if($book->is_started)
                        @if($book->is_completed)
                            <!-- Completed book indicator -->
                            <div class="absolute top-3 right-3 z-10">
                                <flux:badge color="green" class="shadow-lg">
                                    <flux:icon.check class="size-3" />
                                    {{ __('Complete') }}
                                </flux:badge>
                            </div>
                            <!-- Completed book overlay -->
                            <div class="absolute inset-0 bg-green-500/10 dark:bg-green-400/10"></div>
                        @else
                            <!-- In progress book -->
                            <div class="absolute top-3 right-3 z-10">
                                <flux:badge color="blue" class="shadow-lg">
                                    {{ $book->progress_percentage }}%
                                </flux:badge>
                            </div>
                            <!-- Progress bar -->
                            <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-200 dark:bg-gray-700">
                                <div 
                                    class="h-full bg-blue-500 dark:bg-blue-400 transition-all duration-300"
                                    style="width: {{ $book->progress_percentage }}%"
                                ></div>
                            </div>
                            <!-- In progress overlay -->
                            <div class="absolute inset-0 bg-blue-500/5 dark:bg-blue-400/5"></div>
                        @endif
                    @endif
                    
                    <div class="absolute inset-0 flex items-center justify-center align-middle">
                        <div class="text-center">
                            <span class="text-3xl font-semibold text-gray-800 dark:text-gray-200">{{ $book->title }}</span>
                            @if($book->is_started && !$book->is_completed)
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $book->completed_chapters }} / {{ $book->chapter_count }} {{ __('chapters') }}
                                </div>
                            @endif
                        </div>
                        <flux:link wire:navigate href="{{ route('books.show', ['book' => $book->id]) }}" class="absolute w-full h-full"
                        ></flux:link>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
