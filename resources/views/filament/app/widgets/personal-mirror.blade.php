<div>
    @php
        $m = $mirror ?? [];
        $score = $m['performance_score'] ?? 0;
        $scoreColor = match(true) {
            $score >= 90 => 'emerald',
            $score >= 70 => 'amber',
            $score >= 50 => 'orange',
            default => 'red',
        };
    @endphp

    {{-- المرآة الشخصية --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-l from-primary-500 to-primary-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <x-heroicon-o-eye class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">المرآة الشخصية</h3>
                        <p class="text-sm text-white/70">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                </div>
                {{-- Performance Score Circle --}}
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"/>
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none" stroke="white" stroke-width="3"
                              stroke-dasharray="{{ $score }}, 100"
                              stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-white">{{ $score }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Motivational Message --}}
        @if(!empty($m['message']))
            <div class="px-6 py-3 bg-{{ $scoreColor }}-50 dark:bg-{{ $scoreColor }}-900/20 border-b border-{{ $scoreColor }}-200 dark:border-{{ $scoreColor }}-800">
                <p class="text-sm font-medium text-{{ $scoreColor }}-800 dark:text-{{ $scoreColor }}-200 text-center">
                    {{ $m['message'] }}
                </p>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-gray-700">
            {{-- Present Days --}}
            <div class="bg-white dark:bg-gray-900 p-4 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $m['present_days'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">أيام الحضور</div>
                <div class="text-xs text-gray-400 mt-0.5">من {{ $m['working_days'] ?? 0 }} يوم</div>
            </div>

            {{-- Late Days --}}
            <div class="bg-white dark:bg-gray-900 p-4 text-center">
                <div class="text-2xl font-bold text-amber-600">{{ $m['late_days'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">مرات التأخير</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $m['total_delay'] ?? 0 }} دقيقة</div>
            </div>

            {{-- Financial Loss --}}
            <div class="bg-white dark:bg-gray-900 p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ number_format($m['total_loss'] ?? 0, 0) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">خسارتك المالية</div>
                <div class="text-xs text-gray-400 mt-0.5">ريال سعودي</div>
            </div>

            {{-- Streak --}}
            <div class="bg-white dark:bg-gray-900 p-4 text-center">
                <div class="text-2xl font-bold text-primary-600">{{ $m['streak'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">سلسلة الانضباط</div>
                <div class="text-xs text-gray-400 mt-0.5">أيام متتالية</div>
            </div>
        </div>

        {{-- Bottom Row: Branch Rank + Rates --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Branch Ranking --}}
                @if(!empty($m['branch_rank']))
                    <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                            <x-heroicon-o-trophy class="w-5 h-5 text-primary-600" />
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $m['branch_rank']['position'] }} من {{ $m['branch_rank']['total'] }}
                            </div>
                            <div class="text-xs text-gray-500">ترتيبك في الفرع</div>
                        </div>
                        <div class="mr-auto">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if(($m['branch_rank']['percentile'] ?? 0) >= 80) bg-emerald-100 text-emerald-800
                                @elseif(($m['branch_rank']['percentile'] ?? 0) >= 50) bg-amber-100 text-amber-800
                                @else bg-red-100 text-red-800
                                @endif">
                                أعلى {{ $m['branch_rank']['percentile'] ?? 0 }}%
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Attendance Rate --}}
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600" />
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $m['attendance_rate'] ?? 0 }}%</div>
                        <div class="text-xs text-gray-500">نسبة الحضور</div>
                    </div>
                </div>

                {{-- Punctuality Rate --}}
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-clock class="w-5 h-5 text-sky-600" />
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $m['punctuality_rate'] ?? 0 }}%</div>
                        <div class="text-xs text-gray-500">نسبة الانضباط</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
