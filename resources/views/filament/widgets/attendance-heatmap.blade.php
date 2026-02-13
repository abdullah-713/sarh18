<div>
    @php
        $hourly = $heatmap['hourly_distribution'] ?? [];
        $daily  = $heatmap['daily_distribution'] ?? [];
        $maxHourly = max(1, max($hourly ?: [1]));
        $dayNames = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    @endphp

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-map class="w-5 h-5 text-sky-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">خريطة الحضور الحرارية</h3>
                        <p class="text-sm text-gray-500">آخر 30 يوم</p>
                    </div>
                </div>
                <select wire:model.live="selectedBranch"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                    @foreach($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Hourly Distribution --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">توزيع الحضور بالساعة</h4>
                <div class="flex items-end gap-1 h-32">
                    @foreach($hourly as $hour => $count)
                        @php
                            $height = $maxHourly > 0 ? ($count / $maxHourly) * 100 : 0;
                            $intensity = $maxHourly > 0 ? $count / $maxHourly : 0;
                            $color = $intensity > 0.7 ? 'bg-emerald-500' : ($intensity > 0.3 ? 'bg-amber-400' : 'bg-gray-300 dark:bg-gray-600');
                        @endphp
                        <div class="flex flex-col items-center flex-1">
                            <div class="{{ $color }} rounded-t w-full transition-all duration-300"
                                 style="height: {{ max($height, 2) }}%"
                                 title="{{ $hour }}:00 — {{ $count }} تسجيل حضور"></div>
                            <span class="text-[9px] text-gray-400 mt-1">{{ $hour }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Daily Distribution --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">توزيع الحضور حسب اليوم</h4>
                <div class="space-y-2">
                    @foreach($daily as $dow => $counts)
                        @php
                            $total = ($counts['present'] ?? 0) + ($counts['late'] ?? 0);
                            $late = $counts['late'] ?? 0;
                            $maxDaily = max(1, collect($daily)->sum(fn($c) => ($c['present'] ?? 0) + ($c['late'] ?? 0)) / max(count($daily), 1) * 2);
                            $presentWidth = ($counts['present'] ?? 0) / max($maxDaily, 1) * 100;
                            $lateWidth = $late / max($maxDaily, 1) * 100;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-500 w-16 text-left">{{ $dayNames[$dow] ?? $dow }}</span>
                            <div class="flex-1 flex gap-0.5">
                                <div class="bg-emerald-400 h-5 rounded-r transition-all duration-300"
                                     style="width: {{ min($presentWidth, 100) }}%"></div>
                                <div class="bg-amber-400 h-5 rounded-l transition-all duration-300"
                                     style="width: {{ min($lateWidth, 100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-12 text-left tabular-nums">{{ $total }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3 justify-center">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-emerald-400"></div>
                        <span class="text-xs text-gray-500">حضور منتظم</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-amber-400"></div>
                        <span class="text-xs text-gray-500">حضور متأخر</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
