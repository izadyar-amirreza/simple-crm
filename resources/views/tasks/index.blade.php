<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            My Tasks
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Success message --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search + Create --}}
        <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
            <form method="GET" class="flex gap-2 w-full">
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="Search tasks..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700
                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                           shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Search
                </button>
            </form>
            <a href="{{ route('tasks.trash') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded whitespace-nowrap">
                    Trash
            </a>
            <a href="{{ route('tasks.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded whitespace-nowrap">
                + New Task
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-900/50">
                    <tr class="text-left text-gray-700 dark:text-gray-200">
                        <th class="p-3">Title</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Due</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tasks as $task)
                        <tr class="text-gray-900 dark:text-gray-100">
                            <td class="p-3">
                                <div class="font-medium">{{ $task->title }}</div>
                                @if($task->notes)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                                        {{ $task->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="p-3">
                                <span class="text-sm px-2 py-1 rounded bg-gray-100 dark:bg-gray-900/60">
                                    {{ $task->type }}
                                </span>
                            </td>

                            <td class="p-3">
                                @php
                                    $badge = match($task->status) {
                                        'open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                                        'done' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                                        'canceled' => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                                        default => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                                    };
                                @endphp

                                <span class="text-sm px-2 py-1 rounded {{ $badge }}">
                                    {{ $task->status }}
                                </span>
                            </td>

                            <td class="p-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $task->due_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>

                            <td class="p-3 text-right">
                                <div class="flex justify-end items-center gap-3">

                                    {{-- Status buttons --}}
                                    @if($task->status === 'open')
                                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <button class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                                                Done
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="canceled">
                                            <button class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="open">
                                            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                Reopen
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                            onclick="return confirm('Delete this task?')"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 dark:text-gray-400">
                                No tasks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>

    </div>
</x-app-layout>
