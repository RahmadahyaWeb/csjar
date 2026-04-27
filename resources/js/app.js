import L from "leaflet";
import "leaflet/dist/leaflet.css";

import AttendanceMap from "./modules/AttendanceMap";

window.AttendanceMap = AttendanceMap;

document.addEventListener("livewire:navigated", () => {
    const element = document.getElementById("attendance-map");

    if (!element) {
        AttendanceMap.destroyAll();
        return;
    }

    const map = new AttendanceMap(element);

    map.init();
});
