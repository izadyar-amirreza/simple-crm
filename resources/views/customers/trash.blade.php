<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white">Customer Trash</h2>
                <p class="text-sm text-white/70">Deleted customers.</p>
            </div>

            <a href="{{ route('customers.index') }}"
               class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-800 bg-white hover:bg-slate-100 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-white/90">
                        <thead class="text-xs uppercase text-white/70 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4 text-left">Name</th>
                                <th class="px-6 py-4 text-left">Email</th>
                                <th class="px-6 py-4 text-left">Deleted At</th>
                                <th class="px-6 py-4 text-right w-72">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse($customers as $c)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 font-medium">{{ $c->name }}</td>
                                    <td class="px-6 py-4 text-white/80">{{ $c->email }}</td>
                                    <td class="px-6 py-4 text-white/80">{{ $c->deleted_at }}</td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">

                                            @can('restore', $c)
                                                <form method="POST" action="{{ route('customers.restore', $c->id) }}">
                                                    @csrf
                                                    <button class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                   border border-white/15 hover:bg-white/10 transition">
                                                        Restore
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('forceDelete', $c)
                                                <form method="POST" action="{{ route('customers.forceDelete', $c->id) }}"
                                                      onsubmit="return confirm('Delete permanently?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="inline-flex items-center px-3 py-1.5 text-xs rounded-md
                                                                   border border-red-400/40 text-red-200 hover:bg-red-500/15 transition">
                                                        Delete forever
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-white/60">
                                        Trash is empty.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t border-white/10">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
