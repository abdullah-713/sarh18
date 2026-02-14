<x-filament-panels::page>
    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="text-3xl font-bold text-orange-500">{{ $stats['total_branches'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">إجمالي الفروع</div>
            <div class="text-xs text-green-500 mt-1">{{ $stats['active_branches'] }} نشط</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="text-3xl font-bold text-blue-500">{{ $stats['total_employees'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">إجمالي الموظفين</div>
            <div class="text-xs text-green-500 mt-1">{{ $stats['active_employees'] }} نشط</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="text-3xl font-bold {{ $stats['attendance_logs'] > 0 ? 'text-red-500' : 'text-green-500' }}">{{ number_format($stats['attendance_logs']) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">سجلات الحضور</div>
            @if($stats['attendance_logs'] > 0)
                <div class="text-xs text-red-400 mt-1">بحاجة لتصفير</div>
            @else
                <div class="text-xs text-green-500 mt-1">مُصفّر ✓</div>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="text-3xl font-bold {{ ($stats['leave_requests'] + $stats['payrolls'] + $stats['financial_reports']) > 0 ? 'text-red-500' : 'text-green-500' }}">{{ number_format($stats['leave_requests'] + $stats['payrolls'] + $stats['financial_reports']) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">سجلات أخرى</div>
            <div class="text-xs text-gray-400 mt-1">إجازات + رواتب + تقارير</div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚡ عمليات التهيئة</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <button wire:click="resetAllRecords"
                    wire:confirm="⚠️ سيتم حذف جميع سجلات الحضور والإجازات والرواتب والتقارير. هل تريد المتابعة؟"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-red-500/10 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-500/20 transition font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                تصفير السجلات
            </button>
            <button wire:click="resetAllPasswords"
                    wire:confirm="سيتم إعادة تعيين كلمات المرور لجميع الموظفين إلى 123456. هل تريد المتابعة؟"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-amber-500/10 border border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 rounded-xl hover:bg-amber-500/20 transition font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                كلمات المرور 123456
            </button>
            <button wire:click="setLogoAsAvatar"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-purple-500/10 border border-purple-300 dark:border-purple-700 text-purple-600 dark:text-purple-400 rounded-xl hover:bg-purple-500/20 transition font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                صورة الشعار للجميع
            </button>
            <button wire:click="applyStandardShift"
                    wire:confirm="سيتم تطبيق مناوبة واحدة 08:00—21:00 (عدا الجمعة) لجميع الموظفين. هل تريد المتابعة؟"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-500/10 border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-500/20 transition font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                مناوبة 08:00—21:00
            </button>
            <button wire:click="runFullDeploymentReset"
                    wire:confirm="⚠️ سيتم: تصفير السجلات + كلمات مرور 123456 + صورة الشعار + مناوبة موحدة. هل تريد تنفيذ الكل؟"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-bold text-sm shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                تنفيذ الكل دفعة واحدة
            </button>
        </div>
    </div>

    {{-- Shift Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">🕐 المناوبة المعيارية</h3>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">الاسم:</span>
                <span class="font-bold text-blue-600 dark:text-blue-400 mr-1">{{ $shiftInfo['name'] }}</span>
            </div>
            <div class="px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">البداية:</span>
                <span class="font-bold text-green-600 dark:text-green-400 mr-1">{{ $shiftInfo['start'] }}</span>
            </div>
            <div class="px-4 py-2 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">النهاية:</span>
                <span class="font-bold text-red-600 dark:text-red-400 mr-1">{{ $shiftInfo['end'] }}</span>
            </div>
            <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900/20 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400">يوم الراحة:</span>
                <span class="font-bold text-gray-700 dark:text-gray-300 mr-1">الجمعة</span>
            </div>
        </div>
    </div>

    {{-- Branches Map --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🗺️ خريطة الفروع</h3>
        </div>
        <div id="deployment-branches-map" class="w-full" style="height: 350px; z-index: 1;"></div>
        @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        @endassets
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('deployment-branches-map');
            if (!el || el._leaflet_id) return;
            const branches = @json($branches);
            const validBranches = branches.filter(b => b.latitude && b.longitude);
            if (validBranches.length === 0) return;
            const map = L.map(el).setView([validBranches[0].latitude, validBranches[0].longitude], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            const bounds = [];
            validBranches.forEach(b => {
                const marker = L.marker([b.latitude, b.longitude]).addTo(map);
                marker.bindPopup(
                    `<div style="text-align:right;font-family:Cairo,sans-serif;min-width:160px">` +
                    `<strong>${b.name}</strong><br>` +
                    `<span style="color:#666">${b.code}</span><br>` +
                    `<span style="color:#059669">📍 ${b.latitude}, ${b.longitude}</span><br>` +
                    `<span style="color:#FF8C00">🎯 نطاق: ${b.radius}م</span><br>` +
                    `<span style="color:#2563eb">👥 ${b.employees} موظف</span>` +
                    `</div>`
                );
                if (b.radius) {
                    L.circle([b.latitude, b.longitude], {
                        radius: b.radius,
                        color: '#FF8C00',
                        fillColor: '#FF8C00',
                        fillOpacity: 0.12,
                        weight: 2,
                    }).addTo(map);
                }
                bounds.push([b.latitude, b.longitude]);
            });
            if (bounds.length > 1) map.fitBounds(bounds, { padding: [30, 30] });
        });
        </script>
    </div>

    {{-- Branches Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🏢 الفروع ({{ count($branches) }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الفرع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">المدينة</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">خط العرض</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">خط الطول</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">النطاق (م)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">الموظفون</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($branches as $i => $branch)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $branch['name'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $branch['code'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $branch['city'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-center font-mono text-xs {{ $branch['latitude'] ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                                {{ $branch['latitude'] ?? 'غير محدد' }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-xs {{ $branch['longitude'] ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                                {{ $branch['longitude'] ?? 'غير محدد' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $branch['radius'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600 dark:text-blue-400">{{ $branch['employees'] }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($branch['is_active'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">معطل</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">لا توجد فروع</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Employees Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">👥 الموظفون ({{ count($employees) }})</h3>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full">
                    🔑 كلمة المرور الافتراضية: <strong>123456</strong>
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الرقم الوظيفي</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الاسم</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">البريد الإلكتروني</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الهاتف</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الفرع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">المسمى الوظيفي</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">الحالة</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">الصورة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($employees as $i => $emp)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $emp['employee_id'] ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $emp['name'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-blue-600 dark:text-blue-400 select-all" dir="ltr">{{ $emp['email'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500" dir="ltr">{{ $emp['phone'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $emp['branch'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $emp['job_title'] }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($emp['status'] === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ $emp['status'] ?? 'غير محدد' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($emp['has_avatar'])
                                    <span class="text-green-500">✓</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">لا يوجد موظفون</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
