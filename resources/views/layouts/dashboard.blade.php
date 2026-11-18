@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.base :title="$title ?? ($isEn ? 'Studio Dashboard — Zaffe' : 'لوحة التحكم — زفّة')">
    <div class="min-h-screen bg-[#F5F1E9] flex flex-col md:flex-row"
         x-data="{ sidebarOpen: false }"
         @keydown.escape.window="sidebarOpen = false">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-40 md:hidden"></div>

        <!-- Studio Sidebar Navigation (Direction-Aware RTL & LTR) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : 'max-md:rtl:translate-x-full max-md:ltr:-translate-x-full'"
               class="fixed md:sticky top-0 inset-y-0 start-0 h-screen w-72 bg-[#1E1A16] text-[#E4D9C8] z-50 flex flex-col border-e border-[#C6A76B]/20 transition-transform duration-200 ease-out shrink-0">
            <!-- Brand Header -->
            <div class="h-18 flex items-center justify-between px-5 border-b border-white/10 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#161311] font-amiri text-xl font-bold shadow-sm group-hover:scale-105 transition-transform">
                        ز
                    </div>
                    <div class="flex flex-col">
                        <span class="font-cairo text-lg font-extrabold text-white tracking-tight leading-none">زفّــة</span>
                        <span class="text-[10px] text-[#DFC99E] tracking-wider uppercase mt-1">
                            {{ $isEn ? 'Couple Studio' : 'استوديو العرسان' }}
                        </span>
                    </div>
                </a>
                <button type="button"
                        @click="sidebarOpen = false"
                        aria-label="{{ $isEn ? 'Close navigation' : 'إغلاق القائمة' }}"
                        class="md:hidden w-10 h-10 rounded-lg flex items-center justify-center text-[#E4D9C8]/75 hover:text-white hover:bg-white/10 cursor-pointer">
                    <x-app-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <!-- User Quick Info -->
            <div class="px-5 py-4 border-b border-white/10 flex items-center gap-3 bg-white/[0.03] shrink-0">
                <div class="w-10 h-10 rounded-full bg-[#C6A76B]/20 border border-[#C6A76B]/40 flex items-center justify-center text-[#DFC99E] font-bold text-sm overflow-hidden shrink-0">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-[#E4D9C8]/65 truncate" dir="ltr">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <!-- Primary Action: Create New Invitation -->
            <div class="p-4 shrink-0">
                <a href="{{ route('dashboard.invitations.create') }}"
                   class="btn-royal w-full !rounded-xl !text-sm">
                    <x-app-icon name="plus" class="w-4 h-4" />
                    <span>{{ $isEn ? 'New Invitation' : 'إنشاء دعوة جديدة' }}</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav aria-label="{{ $isEn ? 'Studio Navigation' : 'قائمة الاستوديو' }}"
                 class="flex-1 px-3 py-1 space-y-1 overflow-y-auto studio-scroll">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.invitations*') && !request()->routeIs('dashboard.profile*') ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/85 hover:bg-white/[0.06] hover:text-white' }}">
                    <x-app-icon name="crown" class="w-4.5 h-4.5 {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.invitations*') && !request()->routeIs('dashboard.profile*') ? 'text-[#151210]' : 'text-[#DFC99E]' }}" />
                    <span>{{ $isEn ? 'Overview' : 'نظرة عامة والملخص' }}</span>
                </a>

                <a href="{{ route('dashboard.invitations.index') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('dashboard.invitations*') ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/85 hover:bg-white/[0.06] hover:text-white' }}">
                    <x-app-icon name="heart" class="w-4.5 h-4.5 {{ request()->routeIs('dashboard.invitations*') ? 'text-[#151210]' : 'text-[#DFC99E]' }}" />
                    <span>{{ $isEn ? 'My Invitations & RSVPs' : 'دعواتي وتأكيدات الحضور' }}</span>
                </a>

                <a href="{{ route('templates.index') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold text-[#E4D9C8]/85 hover:bg-white/[0.06] hover:text-white transition">
                    <x-app-icon name="layout-grid" class="w-4.5 h-4.5 text-[#DFC99E]" />
                    <span>{{ $isEn ? 'Template Gallery' : 'معرض التصاميم والقوالب' }}</span>
                </a>

                <a href="{{ route('dashboard.profile') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('dashboard.profile') ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/85 hover:bg-white/[0.06] hover:text-white' }}">
                    <x-app-icon name="user" class="w-4.5 h-4.5 {{ request()->routeIs('dashboard.profile') ? 'text-[#151210]' : 'text-[#DFC99E]' }}" />
                    <span>{{ $isEn ? 'Profile & Security' : 'الملف الشخصي والحساب' }}</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <span class="px-3.5 text-[10px] uppercase font-bold tracking-wider text-[#DFC99E]/80 block mb-1.5">
                            {{ $isEn ? 'Platform Admin' : 'إدارة المنصة' }}
                        </span>
                        <a href="/admin" target="_blank" rel="noopener noreferrer"
                           class="flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-bold text-amber-300 hover:bg-amber-500/10 transition">
                            <span class="flex items-center gap-3">
                                <x-app-icon name="shield-check" class="w-4.5 h-4.5" />
                                <span>{{ $isEn ? 'Super Admin Panel' : 'لوحة السوبر أدمن' }}</span>
                            </span>
                            <x-app-icon name="external-link" class="w-4 h-4" />
                        </a>
                    </div>
                @endif
            </nav>

            <!-- Bottom Actions -->
            <div class="p-3.5 border-t border-white/10 space-y-1.5 shrink-0">
                <a href="{{ route('home') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-[#E4D9C8]/80 hover:bg-white/[0.05] hover:text-white transition">
                    <x-app-icon name="external-link" class="w-4 h-4 text-[#DFC99E]" />
                    <span>{{ $isEn ? 'Visit Public Site' : 'زيارة الموقع الخارجي' }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-bold text-rose-300 hover:bg-rose-500/15 hover:text-rose-200 transition cursor-pointer">
                        <x-app-icon name="logout" class="w-4 h-4" />
                        <span>{{ $isEn ? 'Log Out' : 'تسجيل الخروج' }}</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Sticky Workspace Bar (Mobile + Desktop Utility) -->
            <header class="h-16 bg-white/90 backdrop-blur-md border-b border-[#E4D9C8] px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 sticky top-0 z-30">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button"
                            @click="sidebarOpen = true"
                            aria-label="{{ $isEn ? 'Open navigation' : 'فتح القائمة الجانبية' }}"
                            class="md:hidden w-10 h-10 rounded-xl border border-[#E4D9C8] bg-[#FAF7F2] flex items-center justify-center text-[#221E1A] hover:border-[#C6A76B] cursor-pointer">
                        <x-app-icon name="menu" class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-2 text-xs font-bold text-[#52493F] truncate">
                        <span class="font-cairo text-sm sm:text-base font-extrabold text-[#221E1A] truncate">
                            {{ $isEn ? 'Zaffe Couple Studio' : 'استوديو زفّــة' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('lang.switch', $isEn ? 'ar' : 'en') }}"
                       class="inline-flex items-center gap-1.5 min-h-[38px] px-3 py-1.5 rounded-lg border border-[#E4D9C8] bg-[#FAF7F2] text-xs font-bold text-[#221E1A] hover:border-[#C6A76B] transition">
                        <x-app-icon name="globe" class="w-3.5 h-3.5 text-[#856326]" />
                        <span>{{ $isEn ? 'العربية' : 'EN' }}</span>
                    </a>
                    <a href="{{ route('dashboard.invitations.create') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 min-h-[38px] px-3.5 py-1.5 rounded-lg bg-[#221E1A] text-white text-xs font-bold hover:bg-[#352F29] transition">
                        <x-app-icon name="plus" class="w-3.5 h-3.5 text-[#DFC99E]" />
                        <span>{{ $isEn ? 'New Card' : 'دعوة جديدة' }}</span>
                    </a>
                </div>
            </header>

            <!-- Flash Session Alerts -->
            @if(session('success') || session('error'))
                <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    @if(session('success'))
                        <div role="status" class="p-4 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/25 text-[#0F4C3A] text-sm flex items-center gap-3 shadow-sm animate-fade-in">
                            <x-app-icon name="check-circle" class="w-5 h-5 text-[#0F4C3A] shrink-0" />
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div role="alert" class="p-4 rounded-xl bg-[#FAF0EC] border border-[#BE123C]/25 text-[#BE123C] text-sm flex items-center gap-3 shadow-sm">
                            <x-app-icon name="alert-triangle" class="w-5 h-5 text-[#BE123C] shrink-0" />
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Slot -->
            <main id="main-content" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
