function applyFiltersToSession(filtersArray, fromPage) {
    let postData = ((filtersArray[0] !== "") ? "clientFilter=" + filtersArray[0] + "&" : "") +
        ((filtersArray[1] !== "") ? "objectFilter=" + filtersArray[1] + "&" : "") +
        ((filtersArray[2] !== "") ? "typeFilter=" + filtersArray[2] + "&" : "") +
        ((filtersArray[3] !== "") ? "timeFilter=" + filtersArray[3] + "&" : "");

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200)
            window.location.href = "../../pages/" + fromPage + "/" + fromPage + ".php";
    };
    xhttp.open("POST", "../../handlers/add_filter/filter_handler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send((postData !== "") ? postData.substring(0, postData.length - 1) : "");
}

function deleteFilter(filterName) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200)
            window.location.reload();
    };
    xhttp.open("POST", "../../handlers/add_filter/filter_handler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("deleteFilter=" + filterName);
}

function sendChosenClient(id) {
    let clientID = id.split(':');
    window.location.replace('add_filter.php?clientID=' + clientID[0]);
}

function ajaxCallApplyTimeFilterForFollowingPeriod(timeFilter) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200)
            window.location.reload();
    };
    xhttp.open("POST", "../../handlers/add_filter/filter_handler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("timeFilter=" + timeFilter);
}
