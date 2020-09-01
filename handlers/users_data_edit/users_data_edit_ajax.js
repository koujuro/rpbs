function sendChosenUser(id) {
    if (id !== "") {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200)
                processResponse(this.responseText);
        };
        xhttp.open("POST", "handlers/users_data_edit/users_data_edit_handler.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("id=" + id);
    }
}

function processResponse(response) {
    let jsonResponse = JSON.parse(response);
    switch (jsonResponse.error) {
        case 'success':
            displayUsersInputs(jsonResponse);
            break;
        case 'idError':
            alert("You have to choose proper client!");
            break;
        default:
            alert("Default error!");
    }
}

function displayUsersInputs(response) {
    let firstName = response.data.fullName.split(" ")[0];
    let lastName = response.data.fullName.split(" ")[1];
    document.getElementById('editUserId').value = response.data.id;
    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editLastName').value = lastName;
    document.getElementById('editLicenceNumber').value = response.data.licenceNumber;
    document.getElementById('editUsername').value = response.data.username;
    document.getElementById('editPassword').value = response.data.password;
    document.getElementById('editRepeatPassword').value = response.data.password;
    let radios = document.getElementsByName('editUserType');

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].value === response.data.userType) {
            radios[i].checked = true;
            break;
        }
    }
}