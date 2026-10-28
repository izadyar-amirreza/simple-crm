<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left: Logo + Links --}}
            <div class="flex items-center gap-8">

                {{-- Brand (Custom Application Logo) --}}
                <a href="{{ route('dashboard') }}" class="hover:opacity-90 transition-opacity">
                    <x-application-logo />
                </a>

                {{-- Desktop Links --}}
                <div class="hidden sm:flex sm:items-center sm:gap-6 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'text-teal-400 font-semibold' : 'text-slate-300 hover:text-white' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('customers.index') }}"
                       class="transition duration-150 ease-in-out {{ request()->routeIs('customers.*') ? 'text-teal-400 font-semibold' : 'text-slate-300 hover:text-white' }}">
                        Customers
                    </a>

                    {{-- ✅ Tasks --}}
                    <a href="{{ route('tasks.index') }}"
                       class="transition duration-150 ease-in-out {{ request()->routeIs('tasks.*') ? 'text-teal-400 font-semibold' : 'text-slate-300 hover:text-white' }}">
                        Tasks
                    </a>

                    <a href="{{ route('leads.index') }}"
                       class="transition duration-150 ease-in-out {{ request()->routeIs('leads.*') ? 'text-teal-400 font-semibold' : 'text-slate-300 hover:text-white' }}">
                        Leads
                    </a>

                    @can('activity.view')
                        <a href="{{ route('activity.index') }}"
                           class="transition duration-150 ease-in-out {{ request()->routeIs('activity.*') ? 'text-teal-400 font-semibold' : 'text-slate-300 hover:text-white' }}">
                            Global Activity
                        </a>
                    @endcan

                </div>
            </div>

            {{-- Right: User Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:gap-4">

                {{-- Admin badge --}}
                @if(auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin'))
                    <span class="px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-400">
                        ADMIN
                    </span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition border border-transparent hover:border-white/5">
                            <span class="font-medium">{{ Auth::user()->name }}</span>

                            <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-white/5 bg-slate-900">
                            <div class="text-sm font-semibold text-white">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ Auth::user()->email }}
                            </div>
                        </div>

                        <div class="bg-slate-900">
                            <x-dropdown-link :href="route('profile.edit')" class="text-slate-300 hover:bg-slate-800 hover:text-white">
                                Profile
                            </x-dropdown-link>
    
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();"
                                                 class="text-slate-300 hover:bg-slate-800 hover:text-white">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-white transition">
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
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden border-t border-white/10 bg-slate-900">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-teal-500/10 text-teal-400 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Dashboard
            </a>
            <a href="{{ route('customers.index') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-teal-500/10 text-teal-400 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Customers
            </a>

            {{-- ✅ Tasks --}}
            <a href="{{ route('tasks.index') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('tasks.*') ? 'bg-teal-500/10 text-teal-400 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Tasks
            </a>

            <a href="{{ route('leads.index') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('leads.*') ? 'bg-teal-500/10 text-teal-400 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Leads
            </a>
            @can('activity.view')
                <a href="{{ route('activity.index') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('activity.*') ? 'bg-teal-500/10 text-teal-400 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Global Activity
                </a>
            @endcan    
        </div>

        <div class="pt-4 pb-4 border-t border-white/10 px-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-slate-400">{{ Auth::user()->email }}</div>
                </div>
                 @if(auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin'))
                    <span class="ml-auto px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-400">
                        ADMIN
                    </span>
                @endif
            </div>

            <div class="mt-4 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>