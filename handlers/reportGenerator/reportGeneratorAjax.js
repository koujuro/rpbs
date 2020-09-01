function sendReportData(reportFor) {
    window.location.href ="../report_generating/report_generating.php?reportFor=" + reportFor;
}

function sendChosenMeasuringDevices() {
    let measuringDevices = getChosenMeasuringDevices();
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            console.log(this.responseText);
        }
    };
    xhttp.open("POST", "../../handlers/reportGenerator/setMeasuringDevicesInSessionHandler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("measuringDevices=" + measuringDevices);
}

function getChosenMeasuringDevices() {
    let measuringDevices = "";
    let devices = document.getElementsByClassName('devices filter-option');

    for (let i = 0; i < devices.length; i++) {
        let deviceClasses = devices[i].classList;
        for (let j = 0; j < deviceClasses.length; j++) {
            if (deviceClasses[j] === 'active') {
                measuringDevices += devices[i].id + ";";
            }
        }
    }
    measuringDevices = measuringDevices.substring(0, measuringDevices.length - 1);
    return measuringDevices;
}