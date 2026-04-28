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
