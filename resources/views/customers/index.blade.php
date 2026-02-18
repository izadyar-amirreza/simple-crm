<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white">Customers</h2>
                <p class="text-sm text-white/70">Manage your customers list.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('customers.trash') }}"
                   class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-800 bg-white hover:bg-slate-100 transition">
                    Trash
                </a>

                @can('create', App\Models\Customer::class)
                    <a href="{{ route('customers.create') }}"
                       class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                        + New
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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
                {{-- Search --}}
                <div class="p-5 border-b border-white/10">
                    <form method="GET" action="{{ route('customers.index') }}" class="flex flex-wrap gap-3 items-center">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search by name, email, phone..."
                            class="w-full md:w-96 rounded-lg border border-white/10 bg-black/20 px-4 py-2.5 text-white placeholder:text-white/40
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />

                        <button class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-500 transition">
                            Search
                        </button>

                        @if(request('q'))
                            <a href="{{ route('customers.index') }}"
                               class="px-5 py-2.5 rounded-lg border border-white/15 text-white/90 hover:bg-white/10 transition">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-white/90">
                        <thead class="text-xs uppercase text-white/70 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4 text-left">Name</th>
                                <th class="px-6 py-4 text-left">Email</th>
                                <th class="px-6 py-4 text-left">Phone</th>
                                <th class="px-6 py-4 text-right w-48">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse($customers as $c)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 font-medium">
                                        @can('view', $c)
                                            <a class="text-indigo-300 hover:underline" href="{{ route('customers.show', $c) }}">
                                                {{ $c->name }}
                                            </a>
                                        @else
                                            {{ $c->name }}
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 text-white/80">{{ $c->email }}</td>
                                    <td class="px-6 py-4 text-white/80">{{ $c->phone }}</td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            @can('view', $c)
                                                <a href="{{ route('customers.activity', $c->id) }}"
                                                   class="inline-flex items-center px-3 py-1.5 text-xs rounded-md border border-white/15 hover:bg-white/10 transition">
                                                    Activity
                                                </a>
                                            @endcan

                                            @can('update', $c)
                                                <a href="{{ route('customers.edit', $c) }}"
                                                   class="inline-flex items-center px-3 py-1.5 text-xs rounded-md border border-white/15 hover:bg-white/10 transition">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('delete', $c)
                                                <form method="POST" action="{{ route('customers.destroy', $c) }}"
                                                      onsubmit="return confirm('Delete this customer?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
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
                                    <td colspan="4" class="px-6 py-10 text-center text-white/60">
                                        No customers yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-5 border-t border-white/10">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
