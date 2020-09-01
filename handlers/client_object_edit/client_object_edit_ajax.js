function sendChosenClient(id) {
    if (id === "init")
        document.getElementById('userInputs').innerHTML = "";
    else {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200)
                processResponse(this.responseText);
        };
        xhttp.open("POST", "handlers/client_object_edit/client_object_edit_handler.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("clientId=" + id);
    }
}

function processResponse(response) {
    let jsonResponse = JSON.parse(response);
    switch (jsonResponse.error) {
        case 'success':
            displayClientInputs(jsonResponse);
            break;
        case 'idError':
            alert("You have to choose proper client!");
            break;
        default:
            alert("Default error!");
    }
}

function displayClientInputs(response) {
    document.getElementById('editClientId').value = response.data.id;
    document.getElementById('editClientName').value = response.data.clientName;
    document.getElementById('editClientStreetAndNumber').value = response.data.streetAndNumber;
    document.getElementById('editClientCity').value = response.data.city;
    document.getElementById('editClientPAC').value = response.data.PAC;
}

function sendChosenNewObjectClient(id) {
    if (id !== "init")
        window.location.replace("client_object_edit.php?newObjectClientId=" + id);
}

function sendChosenObject(id) {
    if (id === "init")
        document.getElementById('userInputs').innerHTML = "";
    else {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200)
                processObjectResponse(this.responseText);
        };
        xhttp.open("POST", "handlers/client_object_edit/client_object_edit_handler.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("objectId=" + id);
    }
}

function processObjectResponse(response) {
    let jsonResponse = JSON.parse(response);
    switch (jsonResponse.error) {
        case 'success':
            displayObjectInputs(jsonResponse);
            break;
        case 'idError':
            alert("You have to choose proper client!");
            break;
        default:
            alert("Default error!");
    }
}

function displayObjectInputs(response) {
    let objectName = response.data.objectName.split(" ")[0];
    let objectPurpose = response.data.objectName.substr(response.data.objectName.indexOf(' ') + 1);
    document.getElementById('editObjectId').value = response.data.id;
    document.getElementById('editObjectName').value = objectName;
    document.getElementById('editObjectPurpose').value = objectPurpose;
    document.getElementById('editObjectStreetAndNumber').value = response.data.streetAndNumber;
    document.getElementById('editObjectCity').value = response.data.city;
    document.getElementById('editObjectPAC').value = response.data.PAC;
    document.getElementById('editFloorsAboveGround').value = response.data.floorsAboveGround;
    document.getElementById('editFloorsUnderground').value = response.data.floorsUnderground;
    document.getElementById('editHighestObjectAltitude').value = response.data.highestObjectAltitude;
}

function sendChosenClientForEdit(clientId) {
    if (clientId !== 'init')
        window.location.replace("client_object_edit.php?editClientId=" + clientId);
}