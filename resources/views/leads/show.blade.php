<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Lead Details</h2>
                <p class="text-sm text-white/70 mt-1">{{ $lead->name }}</p>
            </div>

            <a href="{{ route('leads.index') }}"
               class="px-4 py-2 rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Lead info --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow p-6 text-white/90 space-y-3">
                <div>
                    <span class="text-white/60">Name:</span>
                    {{ $lead->name }}
                </div>

                <div>
                    <span class="text-white/60">Email:</span>
                    {{ $lead->email ?? '-' }}
                </div>

                <div>
                    <span class="text-white/60">Phone:</span>
                    {{ $lead->phone ?? '-' }}
                </div>

                <div>
                    <span class="text-white/60">Status:</span>
                    {{ $lead->status }}
                </div>
            </div>

            {{-- Tasks --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow p-6 text-white/90">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Tasks for this Lead</h3>

                    <a href="{{ route('tasks.create', ['lead_id' => $lead->id]) }}"
                       class="px-3 py-2 rounded-lg bg-emerald-600/90 text-white hover:bg-emerald-600 transition text-sm">
                        + Add Task
                    </a>
                </div>

                @if($tasks->count())
                    <div class="space-y-3">
                        @foreach($tasks as $task)
                            <div class="p-4 rounded-xl border border-white/10 bg-black/20">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-medium text-white">
                                            {{ $task->title }}
                                        </div>

                                        <div class="text-sm text-white/60">
                                            Status:
                                            <span class="text-white/80">{{ $task->status }}</span>

                                            @if($task->due_at)
                                                • Due: {{ $task->due_at->format('Y-m-d H:i') }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        @if($task->status === 'open')
                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="done">
                                                <button class="text-emerald-300 hover:underline text-sm">
                                                    Done
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="canceled">
                                                <button class="text-orange-300 hover:underline text-sm">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="open">
                                                <button class="text-sky-300 hover:underline text-sm">
                                                    Reopen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <p class="text-white/60">
                        No tasks linked to this lead.
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
