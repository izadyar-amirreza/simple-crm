<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Box --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Welcome back, <span class="font-semibold">{{ auth()->user()->name }}</span> 👋
                </div>
            </div>

            {{-- Tasks Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Overdue Tasks --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        ⏰ Overdue Tasks
                    </h3>

                    @if($overdueTasks->count())
                        <ul class="space-y-3">
                            @foreach($overdueTasks as $task)
                                <li class="flex justify-between items-center p-3 rounded bg-red-50 dark:bg-red-900/20">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $task->title }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            Due: {{ $task->due_at?->format('Y-m-d H:i') }}
                                        </div>
                                    </div>

                                    {{-- Done button --}}
                                    <form method="POST" action="{{ route('tasks.status', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button class="text-emerald-700 dark:text-emerald-300 hover:underline">
                                            Done
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">
                            No overdue tasks 🎉
                        </p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('tasks.index') }}"
                           class="text-sm text-blue-600 dark:text-blue-300 hover:underline">
                            View all tasks →
                        </a>
                    </div>
                </div>

                {{-- Today Tasks --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📌 Today Tasks
                    </h3>

                    @if($todayTasks->count())
                        <ul class="space-y-3">
                            @foreach($todayTasks as $task)
                                <li class="flex justify-between items-center p-3 rounded bg-slate-50 dark:bg-slate-900/40">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $task->title }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            Time: {{ $task->due_at?->format('H:i') }}
                                        </div>
                                    </div>

                                    <div class="flex gap-3">
                                        {{-- Done --}}
                                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <button class="text-emerald-700 dark:text-emerald-300 hover:underline">
                                                Done
                                            </button>
                                        </form>

                                        {{-- Cancel --}}
                                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="canceled">
                                            <button class="text-orange-700 dark:text-orange-300 hover:underline">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">
                            No tasks scheduled for today.
                        </p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('tasks.index') }}"
                           class="text-sm text-blue-600 dark:text-blue-300 hover:underline">
                            View all tasks →
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
