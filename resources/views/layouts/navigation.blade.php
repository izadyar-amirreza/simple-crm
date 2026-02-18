<nav x-data="{ open: false }" class="bg-slate-900 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left: Logo + Links --}}
            <div class="flex items-center gap-8">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="text-white font-semibold tracking-wide">
                    CRM
                </a>

                {{-- Desktop Links --}}
                <div class="hidden sm:flex sm:items-center sm:gap-6 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="text-white/80 hover:text-white transition {{ request()->routeIs('dashboard') ? 'text-white font-semibold' : '' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('customers.index') }}"
                       class="text-white/80 hover:text-white transition {{ request()->routeIs('customers.*') ? 'text-white font-semibold' : '' }}">
                        Customers
                    </a>

                    {{-- ✅ Tasks --}}
                    <a href="{{ route('tasks.index') }}"
                       class="text-white/80 hover:text-white transition {{ request()->routeIs('tasks.*') ? 'text-white font-semibold' : '' }}">
                        Tasks
                    </a>

                    <a href="{{ route('leads.index') }}"
                       class="text-white/80 hover:text-white transition {{ request()->routeIs('leads.*') ? 'text-white font-semibold' : '' }}">
                        Leads
                    </a>

                    @can('activity.view')
                        <a href="{{ route('activity.index') }}"
                        class="text-white/80 hover:text-white transition {{ request()->routeIs('activity.*') ? 'text-white font-semibold' : '' }}">
                            Global Activity
                        </a>
                    @endcan

                </div>
            </div>

            {{-- Right: User Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:gap-3">

                {{-- Admin badge (اختیاری) --}}
                @if(auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin'))
                    <span class="px-2 py-1 text-[11px] rounded bg-emerald-600/90 text-white">
                        ADMIN
                    </span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-white/90 hover:bg-white/10 transition">
                            <span class="font-medium">{{ Auth::user()->name }}</span>

                            <svg class="fill-current h-4 w-4 text-white/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-slate-200/20 bg-slate-900">
                            <div class="text-sm font-semibold text-white">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-xs text-white/60">
                                {{ Auth::user()->email }}
                            </div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-white/80 hover:text-white hover:bg-white/10 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }"
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                Dashboard
            </a>
            <a href="{{ route('customers.index') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                Customers
            </a>

            {{-- ✅ Tasks --}}
            <a href="{{ route('tasks.index') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                Tasks
            </a>

            <a href="{{ route('leads.index') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                Leads
            </a>
            @can('activity.view')
                <a href="{{ route('activity.index') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                    Global Activity
                </a>
            @endcan    
        </div>

        <div class="pt-4 pb-4 border-t border-white/10 px-4">
            <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
            <div class="text-sm text-white/60">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded text-white/80 hover:bg-white/10">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded text-white/80 hover:bg-white/10">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
