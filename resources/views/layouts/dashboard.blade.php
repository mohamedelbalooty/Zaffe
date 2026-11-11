<x-layouts.base :title="$title ?? 'لوحة التحكم — زفّة'">
    <div class="min-h-screen bg-[#F6F4EF] flex flex-col md:flex-row" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 z-40 md:hidden"
             style="display: none;"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
               class="fixed md:sticky top-0 right-0 h-screen w-72 bg-[#292521] text-[#E8DED0] z-50 flex flex-col border-l border-[#C6A76B]/20 transition-transform duration-300 ease-in-out">
            <!-- Brand Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-[#E8DED0]/10">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#292521] font-amiri text-xl font-bold shadow-md">
                        ز
                    </div>
                    <div class="flex flex-col">
                        <span class="font-cairo text-xl font-bold text-white tracking-tight">زفّــة</span>
                        <span class="text-[10px] text-[#C6A76B] tracking-wider uppercase">Customer Studio</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-[#E8DED0]/60 hover:text-white">
                    <x-app-icon name="x" class="w-6 h-6" />
                </button>
            </div>

            <!-- User Quick Info -->
            <div class="p-5 border-b border-[#E8DED0]/10 flex items-center gap-3 bg-white/5">
                <div class="w-11 h-11 rounded-full bg-[#C6A76B]/20 border border-[#C6A76B]/40 flex items-center justify-center text-[#C6A76B] font-bold text-base overflow-hidden">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-[#E8DED0]/60 truncate">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <!-- Quick Action: Create New Invitation -->
            <div class="p-4">
                <a href="{{ route('dashboard.invitations.create') }}" 
                   class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:shadow-lg hover:opacity-95 transition">
                    <x-app-icon name="plus" class="w-4 h-4" />
                    <span>إنشاء دعوة جديدة</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.invitations*') && !request()->routeIs('dashboard.profile*') ? 'bg-[#C6A76B] text-[#292521] shadow-md' : 'text-[#E8DED0]/80 hover:bg-white/5 hover:text-white' }}">
                    <x-app-icon name="crown" class="w-5 h-5 {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.invitations*') && !request()->routeIs('dashboard.profile*') ? 'text-[#292521]' : 'text-[#C6A76B]' }}" />
                    <span>نظرة عامة والملخص</span>
                </a>

                <a href="{{ route('dashboard.invitations.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.invitations*') ? 'bg-[#C6A76B] text-[#292521] shadow-md' : 'text-[#E8DED0]/80 hover:bg-white/5 hover:text-white' }}">
                    <x-app-icon name="heart" class="w-5 h-5 {{ request()->routeIs('dashboard.invitations*') ? 'text-[#292521]' : 'text-[#C6A76B]' }}" />
                    <span>دعواتي وتأكيدات الحضور</span>
                </a>

                <a href="{{ route('templates.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#E8DED0]/80 hover:bg-white/5 hover:text-white transition">
                    <x-app-icon name="sparkles" class="w-5 h-5 text-[#C6A76B]" />
                    <span>معرض التصاميم والقوالب</span>
                </a>

                <a href="{{ route('dashboard.profile') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.profile') ? 'bg-[#C6A76B] text-[#292521] shadow-md' : 'text-[#E8DED0]/80 hover:bg-white/5 hover:text-white' }}">
                    <x-app-icon name="users" class="w-5 h-5 {{ request()->routeIs('dashboard.profile') ? 'text-[#292521]' : 'text-[#C6A76B]' }}" />
                    <span>الملف الشخصي والحساب</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-4 mt-4 border-t border-[#E8DED0]/10">
                        <span class="px-4 text-[10px] uppercase font-bold tracking-wider text-[#C6A76B]">إدارة المنصة</span>
                        <a href="/admin" target="_blank"
                           class="flex items-center justify-between px-4 py-3 mt-1 rounded-xl text-sm font-semibold text-amber-300 hover:bg-amber-500/10 transition">
                            <span class="flex items-center gap-3">
                                <x-app-icon name="shield-check" class="w-5 h-5" />
                                <span>لوحة السوبر أدمن</span>
                            </span>
                            <x-app-icon name="external-link" class="w-4 h-4" />
                        </a>
                    </div>
                @endif
            </nav>

            <!-- Bottom Log Out & Back to site -->
            <div class="p-4 border-t border-[#E8DED0]/10 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#E8DED0]/70 hover:text-white transition">
                    <x-app-icon name="external-link" class="w-4 h-4 text-[#C6A76B]" />
                    <span>زيارة الموقع الخارجي</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-red-400 hover:bg-red-500/10 transition">
                        <x-app-icon name="logout" class="w-4 h-4" />
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Top Mobile Bar -->
            <header class="md:hidden h-16 bg-[#FAF7F2] border-b border-[#E8DED0] px-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="font-cairo text-lg font-bold text-[#292521]">زفّــة</span>
                </a>
                <button @click="sidebarOpen = true" class="p-2 text-[#292521] hover:text-[#C6A76B]">
                    <x-app-icon name="menu" class="w-6 h-6" />
                </button>
            </header>

            <!-- Flash Session Alerts -->
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm animate-fade-in">
                        <x-app-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                        <x-app-icon name="x" class="w-5 h-5 text-rose-600 shrink-0" />
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Slot -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
