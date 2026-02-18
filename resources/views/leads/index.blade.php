<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Leads
                </h2>
                <p class="text-sm text-white/70">Manage your leads list.</p>
            </div>

            {{-- RIGHT ACTIONS (Trash + New Lead) --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('leads.trash') }}"
                   class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-800 bg-white hover:bg-slate-100 transition">
                    Trash
                </a>

                <a href="{{ route('leads.create') }}"
                   class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                    + New Lead
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-500/15 text-emerald-200 px-4 py-3 border border-emerald-500/25">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-500/15 text-red-200 px-4 py-3 border border-red-500/25">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur shadow">
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-white/90">
                            <thead class="text-xs uppercase text-white/70 border-b border-white/10">
                                <tr>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3 text-left">Email</th>
                                    <th class="px-4 py-3 text-left">Phone</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/10">
                                @forelse($leads as $lead)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $lead->name }}
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $lead->email ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $lead->phone ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs border border-white/10 bg-white/5">
                                                {{ $lead->status }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex justify-end gap-2">
                                                @if ($lead->status === 'new')
                                                    {{-- Edit --}}
                                                    @can('update', $lead)
                                                        <a href="{{ route('leads.edit', $lead) }}"
                                                           class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                  border border-white/15 hover:bg-white/10 transition">
                                                            Edit
                                                        </a>
                                                    @endcan

                                                    {{-- Convert --}}
                                                    @can('convert', $lead)
                                                        <form action="{{ route('leads.convert', $lead) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                           border border-emerald-400/40 text-emerald-200 hover:bg-emerald-500/15 transition">
                                                                Convert
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @else
                                                    {{-- Disabled Edit --}}
                                                    <span title="Converted leads cannot be edited"
                                                          class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                 border border-white/10 text-white/40 cursor-not-allowed">
                                                        Edit
                                                    </span>

                                                    {{-- Disabled Convert --}}
                                                    <span title="This lead is already converted"
                                                          class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                 border border-white/10 text-white/40 cursor-not-allowed">
                                                        Convert
                                                    </span>
                                                @endif

                                                {{-- Delete --}}
                                                @can('delete', $lead)
                                                    <form action="{{ route('leads.destroy', $lead) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Delete this lead?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                   border border-red-400/40 text-red-200 hover:bg-red-500/15 transition">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-white/60">
                                            No leads found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
