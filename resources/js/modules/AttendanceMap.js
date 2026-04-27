class AttendanceMap {
    static instances = {};

    constructor(element) {
        this.element = element;
        this.mapId = element.id;

        this.officeLat = parseFloat(element.dataset.officeLat);
        this.officeLng = parseFloat(element.dataset.officeLng);
        this.officeRadius = parseInt(element.dataset.officeRadius);

        this.componentId = element
            .closest("[wire\\:id]")
            ?.getAttribute("wire:id");

        this.map = null;
        this.officeCircle = null;
        this.userMarker = null;

        this.watchId = null;
    }

    init() {
        if (!this.element) return;

        if (AttendanceMap.instances[this.mapId]) return;

        this.map = L.map(this.element).setView(
            [this.officeLat, this.officeLng],
            16,
        );

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(this.map);

        const office = L.latLng(this.officeLat, this.officeLng);

        this.officeCircle = L.circle(office, {
            radius: this.officeRadius,
            color: "#22c55e",
            fillOpacity: 0.1,
        }).addTo(this.map);

        this.initButtons(office);

        // this.initDevice();

        this.startLocationWatcher();

        AttendanceMap.instances[this.mapId] = this;
    }

    initButtons(office) {
        const btnUser = document.getElementById("btnUserLocation");
        const btnOffice = document.getElementById("btnOfficeLocation");

        if (btnUser) {
            btnUser.onclick = () => {
                if (this.userMarker) {
                    this.map.setView(this.userMarker.getLatLng(), 17);
                }
            };
        }

        if (btnOffice) {
            btnOffice.onclick = () => {
                this.map.setView(office, 17);
            };
        }
    }

    // initDevice() {
    //     let uuid = localStorage.getItem("device_uuid");

    //     if (!uuid) {
    //         uuid = crypto.randomUUID();

    //         localStorage.setItem("device_uuid", uuid);
    //     }

    //     this.deviceUuid = uuid;
    // }

    startLocationWatcher() {
        if (!navigator.geolocation) {
            console.warn("Geolocation not supported");
            return;
        }

        this.watchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                const user = L.latLng(lat, lng);

                if (!this.userMarker) {
                    this.userMarker = L.marker(user).addTo(this.map);

                    this.map.setView(user, 17);
                } else {
                    this.userMarker.setLatLng(user);
                }

                const office = L.latLng(this.officeLat, this.officeLng);

                const distance = this.map.distance(user, office);

                this.updateLivewire(lat, lng, distance);
            },

            (error) => {
                console.warn("Location error", error);

                if (this.componentId) {
                    const component = Livewire.find(this.componentId);

                    if (component) {
                        component.dispatch("alert", {
                            title: "Location Error",
                            message:
                                "Unable to retrieve GPS location. Please enable location access.",
                            variant: "danger",
                        });
                    }
                }
            },

            {
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 15000,
            },
        );
    }

    updateLivewire(lat, lng, distance) {
        if (!this.componentId) return;

        const component = Livewire.find(this.componentId);

        if (!component) return;

        component.set("latitude", lat);
        component.set("longitude", lng);
        this.updateLivewire(lat, lng);

        // if (this.deviceUuid) {
        //     component.call("setDevice", this.deviceUuid);

        //     this.deviceUuid = null;
        // }
    }

    updateLivewire(lat, lng) {
        if (!this.componentId) return;

        const component = Livewire.find(this.componentId);

        if (!component) return;

        component.set("latitude", lat);
        component.set("longitude", lng);

        // if (this.deviceUuid) {
        //     component.call("setDevice", this.deviceUuid);

        //     this.deviceUuid = null;
        // }
    }

    destroy() {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
        }

        if (this.map) {
            this.map.remove();
        }

        delete AttendanceMap.instances[this.mapId];
    }

    static destroyAll() {
        Object.keys(AttendanceMap.instances).forEach((id) => {
            AttendanceMap.instances[id].destroy();
        });
    }
}

export default AttendanceMap;
