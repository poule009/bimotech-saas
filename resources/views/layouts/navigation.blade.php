<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="
                        @if(auth()->user()->isAdmin()){{ route('admin.dashboard') }}
                        @elseif(auth()->user()->isProprietaire()){{ route('proprietaire.dashboard') }}
                        @else{{ route('locataire.dashboard') }}
                        @endif
                    ">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    @if(auth()->user()->isAdmin())
                       
    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
        Dashboard
    </x-nav-link>
    <x-nav-link :href="route('admin.paiements.index')" :active="request()->routeIs('admin.paiements.*')">
        Paiements
    </x-nav-link>
    <x-nav-link :href="route('admin.contrats.index')" :active="request()->routeIs('admin.contrats.*')">
        Contrats
    </x-nav-link>
    <x-nav-link :href="route('biens.index')" :active="request()->routeIs('biens.*')">
        Biens
    </x-nav-link>
    <x-nav-link :href="route('admin.users.proprietaires')" :active="request()->routeIs('admin.users.proprietaires')">
        Propriétaires
    </x-nav-link>
    <x-nav-link :href="route('admin.users.locataires')" :active="request()->routeIs('admin.users.locataires')">
        Locataires
    </x-nav-link>
    <x-nav-link :href="route('admin.rapports.financier')" :active="request()->routeIs('admin.rapports.*')">
    📊 Rapport
    </x-nav-link> 
    <x-nav-link :href="route('admin.impayes.index')" :active="request()->routeIs('admin.impayes.*')">
    ⚠️ Impayés
</x-nav-link>
                    @elseif(auth()->user()->isProprietaire())
                      
    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
        Dashboard
    </x-nav-link>
    <x-nav-link :href="route('admin.paiements.index')" :active="request()->routeIs('admin.paiements.*')">
        Paiements
    </x-nav-link>
    <x-nav-link :href="route('admin.contrats.index')" :active="request()->routeIs('admin.contrats.*')">
        Contrats
    </x-nav-link>
    <x-nav-link :href="route('biens.index')" :active="request()->routeIs('biens.*')">
        Biens
    </x-nav-link>
    <x-nav-link :href="route('admin.users.proprietaires')" :active="request()->routeIs('admin.users.proprietaires')">
        Propriétaires
    </x-nav-link>
    <x-nav-link :href="route('admin.users.locataires')" :active="request()->routeIs('admin.users.locataires')">
        Locataires
    </x-nav-link>

                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="text-xs text-gray-400">Connecté en tant que</div>
                            <div class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-indigo-500 capitalize">{{ Auth::user()->role }}</div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.paiements.index')" :active="request()->routeIs('admin.paiements.*')">
                    Paiements
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('biens.index')" :active="request()->routeIs('biens.*')">
                    Biens
                </x-responsive-nav-link>

            @elseif(auth()->user()->isProprietaire())
                <x-responsive-nav-link :href="route('proprietaire.dashboard')" :active="request()->routeIs('proprietaire.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('biens.index')" :active="request()->routeIs('biens.*')">
                    Mes Biens
                </x-responsive-nav-link>

            @elseif(auth()->user()->isLocataire())
                <x-responsive-nav-link :href="route('locataire.dashboard')" :active="request()->routeIs('locataire.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('locataire.paiements')" :active="request()->routeIs('locataire.paiements')">
                    Mes Reçus
                </x-responsive-nav-link>

            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="text-xs text-indigo-500 capitalize mt-1">{{ Auth::user()->role }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
