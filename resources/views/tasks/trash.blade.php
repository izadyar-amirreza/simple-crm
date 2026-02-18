<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight">Tasks Trash</h2>
                <p class="text-white/60 text-sm mt-1">Deleted tasks (Soft Delete)</p>
            </div>

            <a href="{{ route('tasks.index') }}"
               class="inline-flex items-center rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm hover:bg-white/10">
                Back
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-white/70">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Title</th>
                        <th class="px-4 py-3 text-left font-medium">Type</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Deleted At</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-white/90">
                                {{ $task->title }}
                            </td>

                            <td class="px-4 py-3 text-white/70">
                                {{ $task->type ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-white/70">
                                <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-2 py-1 text-xs">
                                    {{ $task->status }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-white/60">
                                {{ optional($task->deleted_at)->format('Y-m-d H:i:s') }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @can('restore', $task)
                                        <form method="POST" action="{{ route('tasks.restore', $task->id) }}">
                                            @csrf
                                            <button type="submit"
                                                class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white/90 hover:bg-white/10">
                                                Restore
                                            </button>
                                        </form>
                                    @endcan

                                    @can('forceDelete', $task)
                                        <form method="POST" action="{{ route('tasks.forceDelete', $task->id) }}"
                                              onsubmit="return confirm('Delete forever? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-1.5 text-rose-100 hover:bg-rose-500/15">
                                                Delete forever
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-white/60">
                                Trash is empty.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</x-app-layout>
