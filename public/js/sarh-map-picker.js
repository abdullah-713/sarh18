/**
 * SARH Map Picker Alpine.js Component
 * Registers sarhMapPicker globally for SPA-compatible lazy loading.
 */
(function () {
    function register() {
        Alpine.data('sarhMapPicker', () => ({
            lat: null,
            lng: null,
            radius: null,
            map: null,
            marker: null,
            circle: null,
            loaded: false,
            error: false,
            mapReady: false,
            _pushing: false,

            loadLeaflet() {
                return new Promise((resolve, reject) => {
                    if (window.L) { resolve(); return; }
                    if (!document.querySelector('link[href*="leaflet"]')) {
                        const css = document.createElement('link');
                        css.rel = 'stylesheet';
                        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(css);
                    }
                    const js = document.createElement('script');
                    js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    js.onload = () => resolve();
                    js.onerror = () => reject(new Error('فشل تحميل مكتبة الخرائط'));
                    document.head.appendChild(js);
                });
            },

            forceResize() {
                if (!this.map) return;
                this.map.invalidateSize();
                setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 100);
                setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 300);
                setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 600);
                setTimeout(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                        if (this.marker) this.map.panTo(this.marker.getLatLng());
                    }
                }, 1000);
            },

            syncToForm() {
                this._pushing = true;
                this.$wire.set('data.latitude', this.lat);
                this.$wire.set('data.longitude', this.lng);
                setTimeout(() => { this._pushing = false; }, 600);
            },

            syncFromForm() {
                if (this._pushing) return;
                const lat = parseFloat(this.$wire.get('data.latitude'));
                const lng = parseFloat(this.$wire.get('data.longitude'));
                const radius = parseInt(this.$wire.get('data.geofence_radius'));

                let changed = false;
                if (!isNaN(lat) && Math.abs(lat - (this.lat || 0)) > 0.00000005) { this.lat = lat; changed = true; }
                if (!isNaN(lng) && Math.abs(lng - (this.lng || 0)) > 0.00000005) { this.lng = lng; changed = true; }
                if (!isNaN(radius) && radius !== this.radius) { this.radius = radius; changed = true; }

                if (changed) this.updateMapView();
            },

            updateMapView() {
                if (!this.marker || !this.circle || !this.map) return;
                if (!this.lat || !this.lng) return;
                const ll = L.latLng(this.lat, this.lng);
                this.marker.setLatLng(ll);
                this.circle.setLatLng(ll);
                if (this.radius) this.circle.setRadius(parseInt(this.radius));
                this.map.panTo(ll);
            },

            initMap() {
                if (this.mapReady) return;
                const container = this.$refs.map;
                if (!container || container.offsetHeight < 10) return;

                this.mapReady = true;
                const dLat = this.lat || 24.7136;
                const dLng = this.lng || 46.6753;
                const dRadius = this.radius || 100;

                this.map = L.map(container, {
                    center: [dLat, dLng],
                    zoom: 15,
                    scrollWheelZoom: true,
                    tap: true,
                    dragging: true,
                    touchZoom: true,
                    zoomControl: true,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                    maxZoom: 19,
                }).addTo(this.map);

                this.marker = L.marker([dLat, dLng], { draggable: true }).addTo(this.map);

                this.circle = L.circle([dLat, dLng], {
                    radius: dRadius,
                    color: '#FF8C00',
                    fillColor: '#FF8C00',
                    fillOpacity: 0.12,
                    weight: 2,
                }).addTo(this.map);

                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = parseFloat(pos.lat.toFixed(7));
                    this.lng = parseFloat(pos.lng.toFixed(7));
                    this.circle.setLatLng(pos);
                    this.syncToForm();
                });

                this.map.on('click', (e) => {
                    this.lat = parseFloat(e.latlng.lat.toFixed(7));
                    this.lng = parseFloat(e.latlng.lng.toFixed(7));
                    this.marker.setLatLng(e.latlng);
                    this.circle.setLatLng(e.latlng);
                    this.syncToForm();
                });

                this.$watch('radius', (val) => {
                    if (this.circle && val) this.circle.setRadius(parseInt(val));
                });

                this.forceResize();
            },

            async init() {
                this.lat = parseFloat(this.$wire.get('data.latitude')) || null;
                this.lng = parseFloat(this.$wire.get('data.longitude')) || null;
                this.radius = parseInt(this.$wire.get('data.geofence_radius')) || 100;

                this.$wire.on('map-sync-needed', () => {
                    this.$nextTick(() => this.syncFromForm());
                });

                try {
                    await this.loadLeaflet();
                    this.loaded = true;

                    this.$nextTick(() => {
                        this.initMap();

                        const obs = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.initMap();
                                    this.forceResize();
                                }
                            });
                        }, { threshold: 0.1 });
                        if (this.$refs.map) obs.observe(this.$refs.map);

                        const section = this.$el.closest('.fi-section');
                        if (section) {
                            new MutationObserver(() => {
                                setTimeout(() => { this.initMap(); this.forceResize(); }, 200);
                            }).observe(section, { attributes: true, childList: true, subtree: true });
                        }

                        const tabPanel = this.$el.closest('[role="tabpanel"]') || this.$el.closest('.fi-fo-tabs') || this.$el.closest('.fi-fo-wizard-step');
                        if (tabPanel && tabPanel.parentElement) {
                            new MutationObserver(() => {
                                setTimeout(() => { this.initMap(); this.forceResize(); }, 300);
                            }).observe(tabPanel.parentElement, { attributes: true, childList: true, subtree: true });
                        }
                    });
                } catch (e) {
                    this.error = true;
                    console.error('Leaflet load error:', e);
                }
            }
        }));
    }

    // Register immediately if Alpine is available (SPA navigation),
    // otherwise wait for alpine:init (initial page load).
    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
})();
