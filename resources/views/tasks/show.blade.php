<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Task Details</h2>
                <p class="text-sm text-white/70 mt-1">#{{ $task->id }}</p>
            </div>

            <a href="{{ route('tasks.index') }}"
               class="px-4 py-2 rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow p-6 text-white/90 space-y-3">
                <div><span class="text-white/60">Title:</span> {{ $task->title }}</div>
                <div><span class="text-white/60">Notes:</span> {{ $task->notes ?? '-' }}</div>
                <div><span class="text-white/60">Type:</span> {{ $task->type }}</div>
                <div><span class="text-white/60">Status:</span> {{ $task->status }}</div>
                <div>
                    <span class="text-white/60">Due:</span>
                    {{ $task->due_at ? $task->due_at->format('Y-m-d H:i') : '-' }}
                </div>

                <div>
                    <span class="text-white/60">Assigned To:</span>
                    {{ $task->assigned_to ?? '-' }}
                </div>

                <div>
                    <span class="text-white/60">Lead:</span>
                    {{ $task->lead_id ?? '-' }}
                </div>

                <div>
                    <span class="text-white/60">Customer:</span>
                    {{ $task->customer_id ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
