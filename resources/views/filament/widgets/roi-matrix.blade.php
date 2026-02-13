<div>
    @php
        $matrix = $matrix ?? [];
        $quadrants = [
            'star'     => ['label' => 'نجوم ⭐', 'desc' => 'عائد عالي + انضباط عالي', 'color' => 'emerald', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20'],
            'cash_cow' => ['label' => 'أبقار نقدية 💰', 'desc' => 'عائد عالي + انضباط منخفض', 'color' => 'amber', 'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
            'potential' => ['label' => 'إمكانيات 🚀', 'desc' => 'عائد منخفض + انضباط عالي', 'color' => 'sky', 'bg' => 'bg-sky-50 dark:bg-sky-900/20'],
            'at_risk'  => ['label' => 'خطر ⚠️', 'desc' => 'عائد منخفض + انضباط منخفض', 'color' => 'red', 'bg' => 'bg-red-50 dark:bg-red-900/20'],
        ];
        $grouped = collect($matrix)->groupBy('quadrant');
    @endphp

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5 text-purple-600" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">مصفوفة العائد مقابل الانضباط</h3>
                    <p class="text-sm text-gray-500">تصنيف الفروع حسب الأداء المالي والانضباطي</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
            @foreach($quadrants as $key => $q)
                <div class="{{ $q['bg'] }} rounded-xl p-4 border border-{{ $q['color'] }}-200 dark:border-{{ $q['color'] }}-800">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-{{ $q['color'] }}-800 dark:text-{{ $q['color'] }}-200">{{ $q['label'] }}</h4>
                        <span class="text-xs text-{{ $q['color'] }}-600 dark:text-{{ $q['color'] }}-400">{{ $q['desc'] }}</span>
                    </div>

                    @if($grouped->has($key))
                        <div class="space-y-2">
                            @foreach($grouped[$key] as $item)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['branch_name'] }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            الميزانية: {{ number_format($item['budget'], 0) }} ر.س
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-bold text-{{ $q['color'] }}-600 tabular-nums">{{ $item['roi'] }}%</div>
                                        <div class="text-xs text-gray-400">كفاءة: {{ $item['efficiency'] }}%</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span class="text-xs text-gray-400">لا توجد فروع في هذا التصنيف</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        @if(count($matrix) > 0)
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>إجمالي الفروع: {{ count($matrix) }}</span>
                    <span>متوسط العائد: {{ number_format(collect($matrix)->avg('roi'), 1) }}%</span>
                    <span>متوسط الكفاءة: {{ number_format(collect($matrix)->avg('efficiency'), 1) }}%</span>
                </div>
            </div>
        @endif
    </div>
</div>
