function sendChosenDevice_Test(id) {
    if (id !== "init") {
        window.location.replace("edit_devices.php?id=" + id);
    }
}

function sendChosenPPA(id) {
    if (id !== "init") {
        window.location.replace("edit_ppas.php?id=" + id);
    }
}

function sendChosenHydrant(id) {
    if (id !== "init") {
        window.location.replace("edit_hydrants.php?id=" + id);
    }
}

function sendChosenUPP(id) {
    if (id !== "init") {
        window.location.replace("edit_upps.php?id=" + id);
    }
}

function sendChosenClient(id, deviceID) {
    window.location.replace("edit_devices.php?id=" + deviceID + "&clientID=" + id);
}

