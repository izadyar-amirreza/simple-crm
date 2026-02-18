<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Customer Details
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}"
                   class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    Edit
                </a>
                <a href="{{ route('customers.index') }}"
                   class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded shadow p-6 space-y-3">

                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="text-lg font-medium">{{ $customer->name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-lg font-medium">{{ $customer->email ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="text-lg font-medium">{{ $customer->phone ?? '-' }}</p>
                </div>
            </div>

            {{-- Tasks --}}
            <div class="bg-white dark:bg-gray-800 rounded shadow p-6 space-y-4 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Tasks for this Customer
                    </h3>

                    <a href="{{ route('tasks.create', ['customer_id' => $customer->id]) }}"
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
                                        <div class="font-medium text-white">{{ $task->title }}</div>

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
                        No tasks linked to this customer.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
