<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Global Activity</h2>
                <p class="text-sm text-white/70 mt-1">All system activity logs.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="mb-5 rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow p-4">
                <form method="GET" action="{{ route('activity.index') }}" class="flex flex-wrap gap-3 items-center">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search action/meta..."
                        class="w-64 rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-white placeholder:text-white/40"
                    />

                    <select name="type" class="rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-white">
                        <option value="">All Types</option>
                        <option value="customer" @selected(request('type') === 'customer')>Customer</option>
                        <option value="lead" @selected(request('type') === 'lead')>Lead</option>
                        <option value="task" @selected(request('type') === 'task')>Task</option>
                    </select>

                    {{-- action select --}}
                    <select name="action" class="rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-white">
                        <option value="">All Actions</option>
                        @foreach(($actions ?? []) as $a)
                            <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>

                    {{-- user select --}}
                    <select name="user_id" class="rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-white">
                        <option value="">All Users</option>
                        @foreach(($users ?? []) as $u)
                            <option value="{{ $u->id }}" @selected((string)request('user_id') === (string)$u->id)>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>

                    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition">
                        Filter
                    </button>

                    @if(request()->query())
                        <a href="{{ route('activity.index') }}"
                           class="px-4 py-2 rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Logs --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow">
                <div class="p-5">

                    @php
                        $humanDay = function($day) {
                            $d = \Illuminate\Support\Carbon::parse($day);
                            if ($d->isToday()) return 'Today';
                            if ($d->isYesterday()) return 'Yesterday';
                            return $d->translatedFormat('Y-m-d (l)');
                        };

                        // یک id امن برای alpine
                        $safeId = function($v) {
                            return preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$v);
                        };

                        // عنوان خوانا برای entity (با اسم اگر پیدا شد)
                        $entityTitle = function($entityKey) use ($customersMap, $leadsMap, $tasksMap) {
                            [$type, $id] = explode('|', $entityKey);
                            $short = class_basename($type);
                            $idInt = (int) $id;

                            if ($type === \App\Models\Customer::class) {
                                $name = $customersMap[$idInt]->name ?? null;
                                return $name ? "Customer: {$name} (#{$idInt})" : "Customer #{$idInt}";
                            }

                            if ($type === \App\Models\Lead::class) {
                                $name = $leadsMap[$idInt]->name ?? null;
                                return $name ? "Lead: {$name} (#{$idInt})" : "Lead #{$idInt}";
                            }

                            if ($type === \App\Models\Task::class) {
                                $title = $tasksMap[$idInt]->title ?? null;
                                return $title ? "Task: {$title} (#{$idInt})" : "Task #{$idInt}";
                            }

                            return "{$short} #{$id}";
                        };
                    @endphp

                    {{-- Controls --}}
                    <div
                        x-data="{
                            expandAll() {
                                document.querySelectorAll('[data-collapse]').forEach(el => el.__x && (el.__x.$data.open = true));
                            },
                            collapseAll() {
                                document.querySelectorAll('[data-collapse]').forEach(el => el.__x && (el.__x.$data.open = false));
                            }
                        }"
                        class="flex items-center justify-end gap-2 mb-4"
                    >
                        <button type="button"
                                @click="expandAll()"
                                class="px-3 py-1.5 text-xs rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                            Expand all
                        </button>
                        <button type="button"
                                @click="collapseAll()"
                                class="px-3 py-1.5 text-xs rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                            Collapse all
                        </button>
                    </div>

                    <div class="space-y-8">
                        @forelse($grouped as $day => $entities)
                            @php $dayKey = $safeId($day); @endphp

                            {{-- Day group --}}
                            <div x-data="{ open: true }" data-collapse>
                                <button type="button"
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-2xl border border-white/10 bg-black/20 hover:bg-black/30 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="text-white/90 font-semibold text-sm">{{ $humanDay($day) }}</span>
                                        <span class="text-xs text-white/50">
                                            ({{ collect($entities)->flatten(2)->count() }} logs)
                                        </span>
                                    </div>

                                    <div class="text-white/70 text-sm">
                                        <span x-text="open ? '▾' : '▸'"></span>
                                    </div>
                                </button>

                                <div x-show="open" x-transition class="mt-4 space-y-6">
                                    @foreach($entities as $entityKey => $usersGroup)
                                        @php $entityDomId = $safeId($dayKey.'_'.$entityKey); @endphp

                                        {{-- Entity group --}}
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4"
                                             x-data="{ open: false }"
                                             data-collapse>
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-full flex items-center justify-between gap-3">
                                                <div class="text-white/90 font-semibold text-sm">
                                                    {{ $entityTitle($entityKey) }}
                                                    <span class="text-xs text-white/50 ml-2">
                                                        ({{ collect($usersGroup)->flatten(1)->count() }} logs)
                                                    </span>
                                                </div>
                                                <div class="text-white/70 text-sm">
                                                    <span x-text="open ? '▾' : '▸'"></span>
                                                </div>
                                            </button>

                                            <div x-show="open" x-transition class="mt-4 space-y-5">
                                                @foreach($usersGroup as $userId => $items)
                                                    @php $userDomId = $safeId($entityDomId.'_u_'.$userId); @endphp

                                                    {{-- User group --}}
                                                    <div class="rounded-2xl border border-white/10 bg-black/15 p-3"
                                                         x-data="{ open: true }"
                                                         data-collapse>
                                                        <button type="button"
                                                                @click="open = !open"
                                                                class="w-full flex items-center justify-between">
                                                            <div class="text-xs text-white/60">
                                                                User:
                                                                <span class="text-white/85">
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
                                                                <x-activity-card :log="$log" :customersMap="$customersMap" :leadsMap="$leadsMap" :tasksMap="$tasksMap" />
                                                            @endforeach
                                                        </div>
                                                    </div>
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

                    {{-- Pagination (فقط یک بار) --}}
                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
