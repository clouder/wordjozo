<section class="w-full" x-data="bereanBible()">
    <div class="mb-4">
        <flux:heading size="xl" class="text-center">
            <flux:link wire:navigate variant="ghost" href="{{ route('books.show', ['book' => $book->id]) }}">
                {{ $book->title }}
            </flux:link>
        </flux:heading>
        <flux:heading size="lg" class="text-center">{{ __('Chapter') }} {{ $chapter_number }}</flux:heading>
    </div>
    <div
        id="floating-sum"
        x-show="state.showSummary"
        class="fixed top-0 left-0 flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-6 bg-zinc-900/10 bg-clip-padding backdrop-filter backdrop-blur bg-opacity-10 backdrop-saturate-100 backdrop-contrast-100"
    >
            <div class="grid auto-rows-min gap-4 bg-opacity-100 bg-zinc-900 mt-10">
                <flux:textarea wire:model.live.debounce.500ms="summary" rows="auto" class="w-full" />
            </div>
    </div>
    <template x-if="state.audioLinks && state.audioLink">
        <div class="p-6 rounded-xl mb-6 ring dark:ring-zinc-900/90 shadow-sm dark:shadow-zinc-100/30 ring-zinc-100/10 shadow-zinc-900/30">
            <audio
                class="w-full"
                controls
                x-bind:src="state.audioLink"
                x-show="state.audioLink"
            >
                Your browser does not support the audio element.
            </audio>
            <div class="mt-6 flex justify-center">
                <flux:button.group>
                    <flux:button @click="setAudio('gilbert')">Gilbert</flux:button>
                    <flux:button @click="setAudio('hays')">Hays</flux:button>
                    <flux:button @click="setAudio('souer')">Souer</flux:button>
                </flux:button.group>
            </div>
        </div>
    </template>
    <div id="sumsum" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4">
            <flux:textarea wire:model.live.debounce.500ms="summary" label="Summary" rows="auto" class="w-full" />
        </div>
    </div>
    <div
        x-show="!state.summaryVisible || state.showSummary"
        class="text-lg leading-relaxed text-gray-900 dark:text-gray-100 fixed bottom-0 z-10 mb-4"
    >
        <flux:button
            @click="toggleSummary()"
        >
            <span x-text="state.showSummary ? 'Hide Summary' : 'Show Summary'"></span>
        </flux:button>
    </div>
    <div
        class="whitespace-pre-line text-lg leading-relaxed text-gray-900 dark:text-gray-100"
    >
        <div x-text="state.chapter"></div>
        <div class="text-right mb-12">
            <flux:button
                icon="chevron-up"
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            >
                {{ __('Ascend') }}
            </flux:button>
        </div>
    </div>
</section>

<script>
function bereanBible() {
    return {
        state: {
            summaryVisible: true,
            showSummary: false,
            scrollPosition: { x: 0, y: 0 },
            audioLinks: null,
            audioLink: 'https://cdn.jsdelivr.net/gh/anars/blank-audio/1-second-of-silence.mp3',
        },
        init() {
            const book = this.getBook('{{ $book->title }}');
            this.getChapter(book);
            this.setupSummaryObserver();
        },
        setAudio(voice, autoPlay = true) {
            this.state.audioLink = this.state.audioLinks[voice];

            if (!autoPlay) return;

            this.$nextTick(() => {
                const audioElement = document.querySelector('audio');
                if (audioElement) {
                    audioElement.load();
                    audioElement.play();
                }
            });
        },
        toggleSummary() {
            this.state.showSummary = !this.state.showSummary;
            if (this.state.showSummary) {
                history.pushState({ dummy: true }, '', location.href);
                this.state.scrollPosition = { x: window.scrollX, y: window.scrollY };
                const textarea = document.querySelector('#floating-sum textarea');
                this.$nextTick(() => {
                    if (textarea) {
                        textarea.focus();
                    }
                });
            } else {
                history.back();
            }
        },
        getChapter(book) {
            const chapter = '{{ $chapter_number }}';
            fetch(`https://bible.helloao.org/api/BSB/${book}/${chapter}.json`)
                .then(response => response.json())
                .then(data => {
                    this.state.audioLinks = data.thisChapterAudioLinks;
                    this.setAudio('gilbert', false);
                    this.state.chapter = data.chapter.content.map(item => {
                        if (item.type === 'verse') {
                            return item.content.map(verseItem => {
                                if (typeof verseItem === 'string' || verseItem.text) {
                                    return verseItem.text ?? verseItem;
                                }
                            }).filter(Boolean).join(' ');
                        } else if (item.type === "line_break") {
                            return "\n\n";
                        }
                    }).filter(Boolean).join(' ');
                })
        },
        setupSummaryObserver() {
            window.addEventListener('popstate', () => {
                if (this.state.showSummary) {
                    this.state.showSummary = false;
                    this.$nextTick(() => {
                        // Ensure the scroll position is restored after the DOM updates
                        window.scrollTo(this.scrollPosition.x, this.scrollPosition.y);
                    });
                }
            });
            const observer = new IntersectionObserver(([entry]) => {
                this.state.summaryVisible = entry.isIntersecting;
            }, {
                threshold: 0.9
            });
            observer.observe(document.getElementById('sumsum'));
        },
        getBook() {
            return {
                "GENESIS": "GEN",
                "EXODUS": "EXO",
                "LEVITICUS": "LEV",
                "NUMBERS": "NUM",
                "DEUTERONOMY": "DEU",
                "JOSHUA": "JOS",
                "JUDGES": "JDG",
                "RUTH": "RUT",
                "1 SAMUEL": "1SA",
                "2 SAMUEL": "2SA",
                "1 KINGS": "1KI",
                "2 KINGS": "2KI",
                "1 CHRONICLES": "1CH",
                "2 CHRONICLES": "2CH",
                "EZRA": "EZR",
                "NEHEMIAH": "NEH",
                "ESTHER": "EST",
                "JOB": "JOB",
                "PSALMS": "PSA",
                "PROVERBS": "PRO",
                "ECCLESIASTES": "ECC",
                "SONG OF SOLOMON": "SNG",
                "ISAIAH": "ISA",
                "JEREMIAH": "JER",
                "LAMENTATIONS": "LAM",
                "EZEKIEL": "EZK",
                "DANIEL": "DAN",
                "HOSEA": "HOS",
                "JOEL": "JOL",
                "AMOS": "AMO",
                "OBADIAH": "OBA",
                "JONAH": "JON",
                "MICAH": "MIC",
                "NAHUM": "NAM",
                "HABAKKUK": "HAB",
                "ZEPHANIAH": "ZEP",
                "HAGGAI": "HAG",
                "ZECHARIAH": "ZEC",
                "MALACHI": "MAL",
                "MATTHEW": "MAT",
                "MARK": "MRK",
                "LUKE": "LUK",
                "JOHN": "JHN",
                "ACTS": "ACT",
                "ROMANS": "ROM",
                "1 CORINTHIANS": "1CO",
                "2 CORINTHIANS": "2CO",
                "GALATIANS": "GAL",
                "EPHESIANS": "EPH",
                "PHILIPPIANS": "PHP",
                "COLOSSIANS": "COL",
                "1 THESSALONIANS": "1TH",
                "2 THESSALONIANS": "2TH",
                "1 TIMOTHY": "1TI",
                "2 TIMOTHY": "2TI",
                "TITUS": "TIT",
                "PHILEMON": "PHM",
                "HEBREWS": "HEB",
                "JAMES": "JAS",
                "1 PETER": "1PE",
                "2 PETER": "2PE",
                "1 JOHN": "1JN",
                "2 JOHN": "2JN",
                "3 JOHN": "3JN",
                "JUDE": "JUD",
                "REVELATION": "REV"
            }['{{ $book->title }}'.toUpperCase()] ?? '{{ $book->title }}'.toUpperCase();

        }
    };
}
</script>
