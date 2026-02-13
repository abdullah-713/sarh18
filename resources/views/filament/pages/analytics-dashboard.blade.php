<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Row 1: Lost Opportunity Clock --}}
        <div class="animate-fadeInUp">
            @livewire(\App\Filament\Widgets\LostOpportunityClockWidget::class)
        </div>

        {{-- Row 2: Efficiency Score Card --}}
        <div class="animate-fadeInUp" style="animation-delay: 0.1s">
            @livewire(\App\Filament\Widgets\EfficiencyScoreCardWidget::class)
        </div>

        {{-- Row 3: Heatmap + ROI Matrix --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <div class="animate-fadeInUp" style="animation-delay: 0.15s">
                @livewire(\App\Filament\Widgets\AttendanceHeatmapWidget::class)
            </div>
            <div class="animate-fadeInUp" style="animation-delay: 0.2s">
                @livewire(\App\Filament\Widgets\ROIMatrixWidget::class)
            </div>
        </div>

        {{-- Row 4: Alerts + Patterns --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            {{-- Recent Loss Alerts --}}
            <div class="animate-fadeInUp" style="animation-delay: 0.25s">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-bell-alert class="w-5 h-5 text-red-600" />
                            <h3 class="text-base font-bold text-red-800 dark:text-red-200">تنبيهات الخسائر</h3>
                            <span class="mr-auto bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ count($recentAlerts) }}
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                        @forelse($recentAlerts as $alert)
                            @php
                                $sevColors = ['critical' => 'red', 'high' => 'amber', 'medium' => 'sky', 'low' => 'gray'];
                                $sevColor = $sevColors[$alert['severity']] ?? 'gray';
                                $sevLabels = ['critical' => 'حرج', 'high' => 'عالي', 'medium' => 'متوسط', 'low' => 'منخفض'];
                            @endphp
                            <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <span class="mt-1 inline-block w-2 h-2 rounded-full bg-{{ $sevColor }}-500 flex-shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $alert['description_ar'] }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-400">{{ $alert['branch']['name_ar'] ?? '—' }}</span>
                                        <span class="text-xs text-{{ $sevColor }}-600 font-medium">{{ $sevLabels[$alert['severity']] ?? '' }}</span>
                                        <span class="text-xs text-gray-400">{{ $alert['alert_date'] }}</span>
                                    </div>
                                </div>
                                <button wire:click="acknowledgeAlert({{ $alert['id'] }})"
                                        class="text-xs text-primary-600 hover:text-primary-800 font-medium flex-shrink-0">
                                    اطّلعت
                                </button>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                لا توجد تنبيهات جديدة ✅
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- High Risk Employee Patterns --}}
            <div class="animate-fadeInUp" style="animation-delay: 0.3s">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-amber-50 dark:bg-amber-900/20">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
                            <h3 class="text-base font-bold text-amber-800 dark:text-amber-200">أنماط عالية المخاطر</h3>
                            <span class="mr-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ count($highRiskPatterns) }}
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                        @forelse($highRiskPatterns as $pattern)
                            @php
                                $riskColors = ['critical' => 'red', 'high' => 'amber', 'medium' => 'sky', 'low' => 'emerald'];
                                $riskColor = $riskColors[$pattern['risk_level']] ?? 'gray';
                                $patternLabels = [
                                    'frequent_late' => 'تأخير متكرر',
                                    'pre_holiday_absence' => 'غياب ما قبل الإجازة',
                                    'monthly_cycle' => 'نمط شهري',
                                    'burnout_risk' => 'خطر إرهاق',
                                ];
                            @endphp
                            <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <span class="mt-1 inline-block w-2 h-2 rounded-full bg-{{ $riskColor }}-500 flex-shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $pattern['user']['name_ar'] ?? '—' }}
                                        </span>
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-{{ $riskColor }}-100 text-{{ $riskColor }}-700">
                                            {{ $patternLabels[$pattern['pattern_type']] ?? $pattern['pattern_type'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $pattern['description_ar'] }}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-gray-400">{{ $pattern['branch']['name_ar'] ?? '' }}</span>
                                        <span class="text-xs text-red-600 font-medium tabular-nums">
                                            خسارة: {{ number_format($pattern['financial_impact'], 0) }} ر.س
                                        </span>
                                        <span class="text-xs text-gray-400 tabular-nums">
                                            احتمال: {{ $pattern['frequency_score'] }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                لا توجد أنماط عالية المخاطر ✅
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
