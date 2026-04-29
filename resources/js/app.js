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

import * as faceapi from "face-api.js";

window.faceapi = faceapi;

// ======================
// GLOBAL STATE
// ======================
let faceInterval = null;
let videoStream = null;

// ======================
// MAIN INIT (LIVEWIRE)
// ======================
document.addEventListener("livewire:navigated", async () => {
    const video = document.getElementById("video");

    // ======================
    // JIKA TIDAK ADA VIDEO
    // ======================
    if (!video) {
        stopFace();
        return;
    }

    // ======================
    // JIKA SUDAH REGISTER FACE
    // ======================
    if (video.style.display === "none") {
        console.log("Face already registered, camera skipped");
        stopFace();
        return;
    }

    // ======================
    // LOAD MODEL (ONCE)
    // ======================
    if (!window.__faceLoaded) {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
            faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
            faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
        ]);

        window.__faceLoaded = true;
        console.log("Face models loaded");
    }

    // ======================
    // START CAMERA
    // ======================
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false,
        });

        video.srcObject = stream;
        await video.play();

        videoStream = stream;

        console.log("Camera started");
    } catch (e) {
        console.error("Camera error:", e);
        return;
    }

    // ======================
    // CLEAR OLD LOOP
    // ======================
    if (faceInterval) {
        clearInterval(faceInterval);
    }

    // ======================
    // DETECTION LOOP
    // ======================
    faceInterval = setInterval(async () => {
        if (video.readyState !== 4) return;

        try {
            const detection = await faceapi
                .detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 320,
                        scoreThreshold: 0.3,
                    }),
                )
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                console.log("No face detected");
                return;
            }

            const descriptor = Array.from(detection.descriptor);

            console.log("DESCRIPTOR LENGTH:", descriptor.length);

            window.__lastFaceDescriptor = descriptor;
        } catch (e) {
            console.error("Detection error:", e);
        }
    }, 1500);
});

// ======================
// ENROLL FUNCTION
// ======================
window.enrollFace = async function () {
    if (!window.__lastFaceDescriptor) {
        alert("Face not detected");
        return;
    }

    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!token) {
        alert("CSRF token missing");
        return;
    }

    await fetch("/face/enroll", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
        },
        body: JSON.stringify({
            descriptor: window.__lastFaceDescriptor,
        }),
    });

    // ======================
    // STOP CAMERA
    // ======================
    stopFace();

    // ======================
    // REFRESH LIVEWIRE
    // ======================
    const el = document.querySelector("[wire\\:id]");
    if (el && window.Livewire) {
        const component = window.Livewire.find(el.getAttribute("wire:id"));

        if (component) {
            component.call("refreshFaceStatus");
        }
    }

    alert("Face registered successfully");
};

// ======================
// STOP FUNCTION (GLOBAL)
// ======================
function stopFace() {
    if (faceInterval) {
        clearInterval(faceInterval);
        faceInterval = null;
    }

    if (videoStream) {
        videoStream.getTracks().forEach((track) => track.stop());
        videoStream = null;
    }
}

window.addEventListener("face-reset", () => {
    console.log("Face reset, restarting camera");

    const video = document.getElementById("video");

    if (!video) return;

    video.style.display = "block";

    // trigger ulang init
    document.dispatchEvent(new Event("livewire:navigated"));
});

window.verifyFace = async function () {
    if (!window.__lastFaceDescriptor) {
        console.log("FACE STATUS: NO FACE DETECTED");
        alert("Face not detected");
        return false;
    }

    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const res = await fetch("/face/verify", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
        },
        body: JSON.stringify({
            descriptor: window.__lastFaceDescriptor,
        }),
    });

    const data = await res.json();

    console.log("FACE DISTANCE:", data.distance);

    if (data.match) {
        console.log("FACE STATUS: MATCH ✅");
    } else {
        console.log("FACE STATUS: NOT MATCH ❌");
    }

    return data.match;
};

window.checkInWithFace = async function () {
    // ======================
    // STEP 1: VERIFY (CLIENT SIDE)
    // ======================
    const isValid = await window.verifyFace();

    if (!isValid) {
        alert("Face not recognized");
        return;
    }

    // ======================
    // STEP 2: CALL LIVEWIRE
    // ======================
    const el = document.querySelector("[wire\\:id]");
    if (!el || !window.Livewire) return;

    const component = window.Livewire.find(el.getAttribute("wire:id"));

    if (component) {
        component.call("checkIn", window.__lastFaceDescriptor);
    }
};

window.checkOutWithFace = async function () {
    // ======================
    // STEP 1: VERIFY (CLIENT SIDE)
    // ======================
    const isValid = await window.verifyFace();

    if (!isValid) {
        alert("Face not recognized");
        return;
    }

    // ======================
    // STEP 2: CALL LIVEWIRE
    // ======================
    const el = document.querySelector("[wire\\:id]");
    if (!el || !window.Livewire) return;

    const component = window.Livewire.find(el.getAttribute("wire:id"));

    if (component) {
        component.call("checkOut", window.__lastFaceDescriptor);
    }
};

// ======================
// CLEANUP NAVIGATION
// ======================
document.addEventListener("livewire:navigating", () => {
    stopFace();
});

// resources/js/app.js

import ApexCharts from "apexcharts";

window.renderDashboardCharts = function (data) {
    // =========================
    // ATTENDANCE CHART (LINE - 7 DAYS)
    // =========================
    const attendanceEl = document.querySelector("#attendanceChart");

    if (attendanceEl) {
        if (attendanceEl.__chart) {
            attendanceEl.__chart.destroy();
        }

        const chart = new ApexCharts(attendanceEl, {
            chart: {
                type: "line",
                height: 280,
                toolbar: { show: false },
            },
            stroke: {
                curve: "smooth",
                width: 3,
            },
            series: [
                {
                    name: "Present",
                    data: data.attendance.present,
                },
                {
                    name: "Absent",
                    data: data.attendance.absent,
                },
            ],
            xaxis: {
                categories: data.attendance.labels,
            },
        });

        chart.render();
        attendanceEl.__chart = chart;
    }

    // =========================
    // LEAVE CHART (DONUT)
    // =========================
    const leaveEl = document.querySelector("#leaveChart");

    if (leaveEl) {
        if (leaveEl.__chart) {
            leaveEl.__chart.destroy();
        }

        const chart = new ApexCharts(leaveEl, {
            chart: {
                type: "donut",
                height: 280,
            },
            labels: ["Pending", "Approved", "Rejected"],
            series: [
                data.leave.pending,
                data.leave.approved,
                data.leave.rejected,
            ],
        });

        chart.render();
        leaveEl.__chart = chart;
    }

    // =========================
    // LATE RANK (BAR)
    // =========================
    const lateEl = document.querySelector("#lateChart");

    if (lateEl) {
        if (lateEl.__chart) {
            lateEl.__chart.destroy();
        }

        const chart = new ApexCharts(lateEl, {
            chart: {
                type: "bar",
                height: 280,
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                },
            },
            series: [
                {
                    name: "Late Minutes",
                    data: data.late.values,
                },
            ],
            xaxis: {
                categories: data.late.labels,
            },
        });

        chart.render();
        lateEl.__chart = chart;
    }

    // =========================
    // WEEKLY CHART (LINE)
    // =========================
    const weeklyEl = document.querySelector("#weeklyChart");

    if (weeklyEl) {
        if (weeklyEl.__chart) {
            weeklyEl.__chart.destroy();
        }

        const present = Array.isArray(data.weekly?.present)
            ? data.weekly.present
            : [];

        const absent = Array.isArray(data.weekly?.absent)
            ? data.weekly.absent
            : [];

        const labels = Array.isArray(data.weekly?.labels)
            ? data.weekly.labels
            : [];

        const chart = new ApexCharts(weeklyEl, {
            chart: {
                type: "line",
                height: 280,
                toolbar: { show: false },
            },
            stroke: {
                curve: "smooth",
                width: 3,
            },
            series: [
                {
                    name: "Present",
                    data: present,
                },
                {
                    name: "Absent",
                    data: absent,
                },
            ],
            xaxis: {
                categories: labels,
            },
        });

        chart.render();
        weeklyEl.__chart = chart;
    }

    // =========================
    // MONTHLY CHART (BAR)
    // =========================
    const monthlyEl = document.querySelector("#monthlyChart");

    if (monthlyEl) {
        if (monthlyEl.__chart) {
            monthlyEl.__chart.destroy();
        }

        const chart = new ApexCharts(monthlyEl, {
            chart: {
                type: "bar",
                height: 280,
                toolbar: { show: false },
            },
            series: [
                {
                    name: "Present",
                    data: data.monthly.present,
                },
                {
                    name: "Absent",
                    data: data.monthly.absent,
                },
            ],
            xaxis: {
                categories: data.monthly.labels,
            },
        });

        chart.render();
        monthlyEl.__chart = chart;
    }
};
