<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Create New Task
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

        <div class="bg-white dark:bg-gray-800 shadow rounded p-6">
            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Title</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="e.g. Call lead Ali"
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
                        placeholder="Optional details..."
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('notes') }}</textarea>
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
                        <option value="follow_up" @selected(old('type') === 'follow_up')>Follow Up</option>
                        <option value="call" @selected(old('type') === 'call')>Call</option>
                        <option value="meeting" @selected(old('type') === 'meeting')>Meeting</option>
                        <option value="email" @selected(old('type') === 'email')>Email</option>
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
                        <option value="open" @selected(old('status','open') === 'open')>Open</option>
                        <option value="done" @selected(old('status') === 'done')>Done</option>
                        <option value="canceled" @selected(old('status') === 'canceled')>Canceled</option>
                    </select>
                </div>

                {{-- Due Date + Due Time --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Due Date</label>
                        <input
                            type="date"
                            name="due_date"
                            value="{{ old('due_date') }}"
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
                            value="{{ old('due_time') }}"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                   shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Optional — اگر تاریخ را بدهی ولی ساعت را ندهی، ساعت 09:00 ثبت می‌شود.
                </p>

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
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" @selected(old('lead_id') == $lead->id)>
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
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }} ({{ $customer->email ?? 'no email' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    You can link a task to a Lead or a Customer (optional).
                </p>

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
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string)old('assigned_to') === (string)$user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Save Task
                    </button>

                    <a href="{{ route('tasks.index') }}" class="text-gray-600 dark:text-gray-300 underline">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
