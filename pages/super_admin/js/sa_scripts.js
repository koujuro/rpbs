function showAccountName() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200)
            processShowAccountName(this.responseText);
    };
    xhttp.open("POST", "../../handlers/log_in/show_account_name.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

function processShowAccountName(response) {
    if (response === "redirect") {
        window.location.replace("index.php");
    } else
        displayAccountName(response);
}

function displayAccountName(response) {
    document.getElementById('accountName').innerHTML = response;
}