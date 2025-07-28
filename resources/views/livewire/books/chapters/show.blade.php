<section class="w-full" x-data="{
    summaryVisible: true,
    showSummary: false,
    scrollPosition: { x: 0, y: 0 },
    init() {
        window.addEventListener('popstate', () => {
            if (this.showSummary) {
                this.showSummary = false;
                this.$nextTick(() => {
                    // Ensure the scroll position is restored after the DOM updates
                    window.scrollTo(this.scrollPosition.x, this.scrollPosition.y);
                });
            }
        });
        const observer = new IntersectionObserver(([entry]) => {
            this.summaryVisible = entry.isIntersecting;
        }, {
            threshold: 0.9
        });
        observer.observe(document.getElementById('sumsum'));
    },
    toggleSummary() {
        this.showSummary = !this.showSummary;
        if (this.showSummary) {
            history.pushState({ dummy: true }, '', location.href);
            this.scrollPosition = { x: window.scrollX, y: window.scrollY };
            // send focus to #sumsum > textarea
            const textarea = document.querySelector('#floating-sum textarea');
            this.$nextTick(() => {
                if (textarea) {
                    textarea.focus();
                }
            });
        } else {
            history.back();
        }
    }
}">
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
        x-show="showSummary"
        class="fixed top-0 left-0 flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-6 bg-zinc-900/10 bg-clip-padding backdrop-filter backdrop-blur bg-opacity-10 backdrop-saturate-100 backdrop-contrast-100"
    >
            <div class="grid auto-rows-min gap-4">
                <flux:textarea wire:model.live.debounce.500ms="summary" label="Summary" rows="auto" class="w-full" />
            </div>
    </div>
    <div id="sumsum" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4">
            <flux:textarea wire:model.live.debounce.500ms="summary" label="Summary" rows="auto" class="w-full" />
        </div>
    </div>
    <div
        x-show="!summaryVisible || showSummary"
        class="text-lg leading-relaxed text-gray-900 dark:text-gray-100 fixed bottom-0 z-10 mb-4"
    >
        <flux:button
            @click="toggleSummary()"
        >
            <span x-text="showSummary ? 'Hide Summary' : 'Show Summary'"></span>
        </flux:button>
    </div>
    <div
        x-data="bereanBible()"
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
        },
        init() {
            const book = {
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

            const chapter = '{{ $chapter_number }}';
            fetch(`https://bible.helloao.org/api/BSB/${book}/${chapter}.json`)
                .then(response => response.json())
                .then(data => {
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
        }
    };
}
</script>
