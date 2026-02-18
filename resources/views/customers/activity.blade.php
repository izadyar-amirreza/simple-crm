<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Customer Activity</h2>
                <p class="text-sm text-white/70 mt-1">
                    {{ $customer->name }} ({{ $customer->email }})
                </p>
            </div>

            @php
                $backUrl = url()->previous() ?: ($customer->trashed()
                    ? route('customers.trash')
                    : route('customers.index'));
            @endphp

            <a href="{{ $backUrl }}"
               class="px-4 py-2 rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow">
                <div class="p-5">

                    @php
                        $humanDay = function($day) {
                            $d = \Illuminate\Support\Carbon::parse($day);
                            if ($d->isToday()) return 'Today';
                            if ($d->isYesterday()) return 'Yesterday';
                            return $d->translatedFormat('Y-m-d (l)');
                        };
                    @endphp

                    <div class="space-y-6">
                        @forelse($grouped as $day => $usersGroup)
                            {{-- Day group --}}
                            <div x-data="{ open: true }">
                                <button type="button"
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-2xl border border-white/10 bg-black/20 hover:bg-black/30 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="text-white/90 font-semibold text-sm">{{ $humanDay($day) }}</span>
                                        <span class="text-xs text-white/50">
                                            ({{ collect($usersGroup)->flatten(1)->count() }} logs)
                                        </span>
                                    </div>

                                    <div class="text-white/70 text-sm">
                                        <span x-text="open ? '▾' : '▸'"></span>
                                    </div>
                                </button>

                                <div x-show="open" x-transition class="mt-4 space-y-5">
                                    @foreach($usersGroup as $userId => $items)
                                        {{-- User group --}}
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4" x-data="{ open: true }">
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-full flex items-center justify-between">
                                                <div class="text-xs text-white/60">
                                                    User:
                                                    <span class="text-white/80">
                                                        {{ $items->first()?->user?->name ?? 'System' }}
                                                    </span>
                                                    <span class="text-white/40">
                                                        ({{ $items->count() }} logs)
                                                    </span>
                                                </div>
                                                <div class="text-white/70 text-sm">
                                                    <span x-text="open ? '▾' : '▸'"></span>
                                                </div>
                                            </button>

                                            <div x-show="open" x-transition class="mt-3 space-y-3">
                                                @foreach($items as $log)
                                                    <x-activity-card :log="$log" />
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-white/60 py-8">No activity yet.</div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
