function sendDataForLogin(configData) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200)
            handlePhpResponse(this.responseText);
    };
    xhttp.open("POST", "handlers/log_in/login_handler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("usersData=" + processData(configData));
}

function processData(configData) {
    var data = configData.split(";");
    var usersData = "";
    for(var i = 0; i < data.length; i++) {
        usersData += document.getElementById(data[i]).value + ";";
    }

    return usersData;
}

function handlePhpResponse(response) {
    switch(response) {
        case 'success':
            successedLogIn();
            break;
        case 'Username or password are not correct.':
            alert(response);
            redirectedLogIn();
            break;
        case 'success_super':
            successSuperAdminLogIn();
            break;
        default:
            alert(response);
            break;
    }
}

function successedLogIn() {
    window.location.replace("pages/dashboard/dashboard.php");
}

function successSuperAdminLogIn() {
    window.location.replace("pages/super_admin/super_admin_dashboard.php");
}

function redirectedLogIn() {
    window.location.replace("index.php");
}