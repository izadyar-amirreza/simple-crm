<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Edit Task
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">

        {{-- Errors --}}
        @if($errors->any())
            <div class="bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200 p-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded p-6">
            <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Title</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $task->title) }}"
                        required
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Notes</label>
                    <textarea
                        name="notes"
                        rows="4"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('notes', $task->notes) }}</textarea>
                </div>

                {{-- Type --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Type</label>
                    <select
                        name="type"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        @php $type = old('type', $task->type); @endphp
                        <option value="follow_up" @selected($type === 'follow_up')>Follow Up</option>
                        <option value="call"      @selected($type === 'call')>Call</option>
                        <option value="meeting"   @selected($type === 'meeting')>Meeting</option>
                        <option value="email"     @selected($type === 'email')>Email</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Status</label>
                    <select
                        name="status"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        @php $status = old('status', $task->status); @endphp
                        <option value="open"     @selected($status === 'open')>Open</option>
                        <option value="done"     @selected($status === 'done')>Done</option>
                        <option value="canceled" @selected($status === 'canceled')>Canceled</option>
                    </select>
                </div>

                {{-- Due Date + Due Time --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Due Date</label>
                        <input
                            type="date"
                            name="due_date"
                            value="{{ old('due_date', optional($task->due_at)->format('Y-m-d')) }}"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                   shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Due Time</label>
                        <input
                            type="time"
                            name="due_time"
                            value="{{ old('due_time', optional($task->due_at)->format('H:i')) }}"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                   shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                {{-- Related Lead --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Related Lead (optional)
                    </label>
                    <select
                        name="lead_id"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">-- None --</option>
                        @php $leadId = old('lead_id', $task->lead_id); @endphp
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" @selected((string)$leadId === (string)$lead->id)>
                                {{ $lead->name }} ({{ $lead->email ?? 'no email' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Related Customer --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Related Customer (optional)
                    </label>
                    <select
                        name="customer_id"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">-- None --</option>
                        @php $customerId = old('customer_id', $task->customer_id); @endphp
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string)$customerId === (string)$customer->id)>
                                {{ $customer->name }} ({{ $customer->email ?? 'no email' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Admin: Assign To --}}
                @if(auth()->user()->hasRole('admin'))
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Assign To</label>
                        <select
                            name="assigned_to"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                   shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            @php $assigned = old('assigned_to', $task->assigned_to); @endphp
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected((string)$assigned === (string)$u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                        Update Task
                    </button>

                    <a href="{{ route('tasks.show', $task) }}" class="text-gray-600 dark:text-gray-300 underline">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
