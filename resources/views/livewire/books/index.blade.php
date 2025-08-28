<section class="w-full">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        
        <!-- Continue Reading Section -->
        @if($continueReading)
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 p-6 shadow-2xl">
                <!-- Background pattern -->
                <div class="absolute inset-0 opacity-20">
                    <x-placeholder-pattern class="size-full stroke-white/30" />
                </div>
                
                <!-- Content -->
                <div class="relative z-10">
                    @if(isset($continueReading['is_completed']) && $continueReading['is_completed'])
                        <!-- Bible completed state -->
                        <div class="text-center">
                            <div class="mb-4">
                                <flux:icon.trophy class="mx-auto size-16 text-yellow-300" />
                            </div>
                            <flux:heading size="xl" class="mb-2 text-white">
                                {{ $continueReading['message'] }}
                            </flux:heading>
                            <p class="text-blue-100">
                                {{ __('What an incredible achievement! You\'ve studied every book of the Bible.') }}
                            </p>
                        </div>
                    @else
                        <!-- Continue reading state -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                @if(isset($continueReading['is_new_user']) && $continueReading['is_new_user'])
                                    <!-- New user messaging -->
                                    <div class="mb-2 flex items-center gap-2">
                                        <flux:icon.book-open class="size-5 text-blue-200" />
                                        <span class="text-sm font-medium text-blue-200 uppercase tracking-wider">
                                            {{ __('Welcome to WordJozo') }}
                                        </span>
                                    </div>
                                    
                                    <flux:heading size="lg" class="mb-2 text-white">
                                        {{ $continueReading['message'] }}
                                    </flux:heading>
                                    
                                    <p class="text-blue-100">
                                        {{ __('Begin your study with') }}
                                        <span class="font-semibold">
                                            {{ $continueReading['book']->title }} {{ __('Chapter') }} {{ $continueReading['chapter'] }}
                                        </span>
                                    </p>
                                @else
                                    <!-- Returning user messaging -->
                                    <div class="mb-2 flex items-center gap-2">
                                        <flux:icon.book-open class="size-5 text-blue-200" />
                                        <span class="text-sm font-medium text-blue-200 uppercase tracking-wider">
                                            {{ __('Continue Your Journey') }}
                                        </span>
                                    </div>
                                    
                                    <flux:heading size="lg" class="mb-2 text-white">
                                        {{ $continueReading['message'] }}
                                    </flux:heading>
                                    
                                    <p class="text-blue-100">
                                        @if($continueReading['is_new_book'])
                                            {{ __('Ready to start a new book?') }}
                                        @else
                                            {{ __('Pick up where you left off') }}
                                        @endif
                                        <span class="font-semibold">
                                            {{ $continueReading['book']->title }} {{ __('Chapter') }} {{ $continueReading['chapter'] }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                            
                            <div class="ml-6">
                                <flux:button 
                                    wire:click="continueToChapter"
                                    variant="primary"
                                    class="bg-white text-blue-600 hover:bg-blue-50 border-none shadow-lg text-lg px-8 py-4"
                                    icon="play"
                                >
                                    @if(isset($continueReading['is_new_user']) && $continueReading['is_new_user'])
                                        {{ __('Start Reading') }}
                                    @elseif($continueReading['is_new_book'])
                                        {{ __('Begin') }}
                                    @else
                                        {{ __('Continue') }}
                                    @endif
                                </flux:button>
                            </div>
                        </div>
                        
                        @if($continueReading['is_new_book'] && (!isset($continueReading['is_new_user']) || !$continueReading['is_new_user']))
                            <!-- New book celebration (only for returning users) -->
                            <div class="mt-4 p-3 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20">
                                <div class="flex items-center gap-2 text-yellow-300">
                                    <flux:icon.sparkles class="size-4" />
                                    <span class="text-sm font-medium">
                                        {{ __('Congratulations! You\'ve completed') }} {{ \App\Models\Book::where('id', '<', $continueReading['book']->id)->orderBy('id', 'desc')->first()?->title ?? 'your previous book' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        <!-- Books Grid -->
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
