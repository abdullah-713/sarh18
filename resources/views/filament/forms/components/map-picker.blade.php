<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            lat: $wire.entangle('data.latitude'),
            lng: $wire.entangle('data.longitude'),
            radius: $wire.entangle('data.geofence_radius'),
            map: null,
            marker: null,
            circle: null,

            init() {
                this.$nextTick(() => {
                    const defaultLat = this.lat || 24.7136;
                    const defaultLng = this.lng || 46.6753;
                    const defaultRadius = this.radius || 100;

                    this.map = L.map(this.$refs.map, {
                        center: [defaultLat, defaultLng],
                        zoom: 15,
                        scrollWheelZoom: true,
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 19,
                    }).addTo(this.map);

                    this.marker = L.marker([defaultLat, defaultLng], {
                        draggable: true,
                    }).addTo(this.map);

                    this.circle = L.circle([defaultLat, defaultLng], {
                        radius: defaultRadius,
                        color: '#f97316',
                        fillColor: '#f97316',
                        fillOpacity: 0.15,
                        weight: 2,
                    }).addTo(this.map);

                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.lat = parseFloat(pos.lat.toFixed(7));
                        this.lng = parseFloat(pos.lng.toFixed(7));
                        this.circle.setLatLng(pos);
                    });

                    this.map.on('click', (e) => {
                        this.lat = parseFloat(e.latlng.lat.toFixed(7));
                        this.lng = parseFloat(e.latlng.lng.toFixed(7));
                        this.marker.setLatLng(e.latlng);
                        this.circle.setLatLng(e.latlng);
                    });

                    this.$watch('radius', (val) => {
                        if (this.circle && val) {
                            this.circle.setRadius(parseInt(val));
                        }
                    });

                    this.$watch('lat', (val) => {
                        if (this.marker && val && this.lng) {
                            const latlng = L.latLng(parseFloat(val), parseFloat(this.lng));
                            this.marker.setLatLng(latlng);
                            this.circle.setLatLng(latlng);
                            this.map.panTo(latlng);
                        }
                    });

                    this.$watch('lng', (val) => {
                        if (this.marker && val && this.lat) {
                            const latlng = L.latLng(parseFloat(this.lat), parseFloat(val));
                            this.marker.setLatLng(latlng);
                            this.circle.setLatLng(latlng);
                            this.map.panTo(latlng);
                        }
                    });

                    setTimeout(() => this.map.invalidateSize(), 250);
                });
            }
        }"
        wire:ignore
        class="w-full"
    >
        {{-- Leaflet CSS/JS --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <div
            x-ref="map"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 shadow-sm"
            style="height: 400px; z-index: 1;"
        ></div>

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ __('branches.map_picker_help') }}
        </p>
    </div>
</x-dynamic-component>
