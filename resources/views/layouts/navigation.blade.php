@php
$path = Storage::url('/uploads/background-card.png');
@endphp
<nav x-data="{ open: false }" class="bg-gray-600 border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700" style="background-image: url('{{url($path)}}')">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex items-center shrink-0">
                    <a href="{{ route('absen.dashboard') }}">
                        <img src="{{asset('assets/img/blm.jpg')}}" alt="Company Logo" class="object-cover w-12 h-12 rounded-full">
                    </a>
                </div>

                <div class="hidden mr-2 space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('absen.dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <br>
                    @if(auth()->user()->jabatan === 'SUPERADMIN' || auth()->user()->jabatan === 'TEAM WAGNER' || auth()->user()->jabatan === 'ADMIN')
                    <x-nav-link :href="route('dashboardadmin')" :active="request()->routeIs('dashboardadmin')">
                        {{ __('Admin Page') }}
                    </x-nav-link>
                    @endif
                </div>

            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-gray-500 border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                            {{-- User's photo in circular style for desktop dropdown --}}
                            @if(!empty(auth()->user()->foto))
                                @php
                                $userPhotoPath = Storage::url('uploads/karyawan/'. auth()->user()->foto);
                                @endphp
                                <img src="{{ $userPhotoPath }}" alt="User Avatar" class="object-cover w-10 h-10 mr-2 bg-white border-2 border-gray-200 rounded-full" />
                            @else
                                @php
                                $defaultAvatarPath = asset('assets/img/blm.jpg');
                                @endphp
                                <img src="{{ $defaultAvatarPath }}" alt="User Avatar" class="object-cover w-10 h-10 mr-2 bg-white border-2 border-gray-200 rounded-full" />
                            @endif
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content" class="bg-gray-500">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('absen.dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(auth()->user()->jabatan === 'SUPERADMIN')
            <x-responsive-nav-link :href="route('dashboardadmin')" :active="request()->routeIs('dashboardadmin')">
                {{ __('Admin Page') }}
            </x-responsive-nav-link>
            @endif
        </div>


        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                {{-- User's photo in circular style for responsive menu --}}
                @if(!empty(auth()->user()->foto))
                    @php
                    $userPhotoPath = Storage::url('uploads/karyawan/'. auth()->user()->foto);
                    @endphp
                    <img src="{{ $userPhotoPath }}" alt="User Avatar" class="object-cover w-24 h-16 mb-2 bg-white border-2 border-gray-200 rounded-full" />
                @else
                    @php
                    $defaultAvatarPath = asset('assets/img/blm.jpg');
                    @endphp
                    <img src="{{ $defaultAvatarPath }}" alt="User Avatar" class="object-cover w-24 h-16 mb-2 bg-white border-2 border-gray-200 rounded-full" />
                @endif
                <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
