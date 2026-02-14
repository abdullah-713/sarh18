<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    {{-- SPA-safe: @assets loads the script once across all navigations --}}
    @assets
    <script src="{{ asset('js/sarh-map-picker.js') }}"></script>
    @endassets

    <div
        x-data="sarhMapPicker()"
        wire:ignore
        class="w-full"
    >
        {{-- Loading state --}}
        <template x-if="!loaded && !error">
            <div class="flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"
                 style="height: 350px;">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-orange-500 mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">جاري تحميل الخريطة...</p>
                </div>
            </div>
        </template>

        {{-- Error state --}}
        <template x-if="error">
            <div class="flex items-center justify-center rounded-xl border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700"
                 style="height: 200px;">
                <div class="text-center text-red-600 dark:text-red-400">
                    <svg class="h-8 w-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <p class="text-sm font-medium">تعذّر تحميل الخريطة</p>
                    <p class="text-xs mt-1">تحقق من اتصال الإنترنت وأعد تحميل الصفحة</p>
                </div>
            </div>
        </template>

        {{-- Map container --}}
        <div
            x-ref="map"
            x-show="loaded && !error"
            x-transition
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 shadow-sm"
            style="height: 350px; min-height: 250px; z-index: 1;"
        ></div>

        {{-- Refresh map button --}}
        <div x-show="loaded && !error" class="mt-2 flex items-center justify-center gap-3">
            <button type="button"
                    @click="forceResize()"
                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                تحديث الخريطة
            </button>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                📍 اضغط على الخريطة أو اسحب المؤشر لتحديد الموقع
            </p>
        </div>
    </div>
</x-dynamic-component>
