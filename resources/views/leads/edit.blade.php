<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-slate-900 dark:text-gray-100">Edit Lead</h2>
            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Update lead details.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @include('leads._form', ['lead' => $lead, 'owners' => $owners])

                    <div class="flex items-center gap-3 pt-4">
                        <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700 transition">
                            Update
                        </button>

                        <a href="{{ route('leads.index') }}"
                           class="rounded-lg border border-slate-300 dark:border-gray-700 px-5 py-2.5
                                  text-slate-700 dark:text-gray-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
