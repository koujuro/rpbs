function sendChosenMD(id) {
    if (id !== "init") {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200){
                processResponse(this.responseText);
            }
        };
        xhttp.open("POST", "../../handlers/measuring_devices/measuring_devices_handler.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("id=" + id);
    }
}

function processResponse(response) {
    let jsonResponse = JSON.parse(response);
    switch (jsonResponse.error) {
        case 'success':
            displayMDInputs(jsonResponse);
            break;
        case 'idError':
            alert("You have to choose proper client!");
            break;
        default:
            alert("Default error!");
    }
}

function displayMDInputs(response) {
    document.getElementById('editMD_id').value = response.data.id;
    document.getElementById('editMD_type').value = response.data.type;
    document.getElementById('editMD_manufacturer').value = response.data.manufacturer;
    document.getElementById('editMD_fabricID').value = response.data.fabricID;
    document.getElementById('editMD_accuracyClass').value = response.data.accuracyClass;
    document.getElementById('editMD_calibrationTestimonial').value = response.data.calibrationTestimonial;
}