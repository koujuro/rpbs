function displayClientFilter(idName) {
    if (idName !== "init") {
        let argumentArray = idName.split(";");
        document.getElementById('clientFilterSection').innerHTML = "<input type='text' name='clientFilterName' id='clientFilterName' value='" + argumentArray[1] + "' disabled/>" +
            "<button id='deleteClientFilter' onclick='deleteFilter(this)'>X</button>";
    }
}

function displayObjectFilter(idName) {
    if (idName !== "init") {
        let argumentArray = idName.split(";");
        document.getElementById('objectFilterSection').innerHTML = "<input type='text' name='objectFilterName' id='objectFilterName' value='" + argumentArray[1] + "' disabled/>" +
            "<button id='deleteObjectFilter' onclick='deleteFilter(this)'>X</button>";
    }
}

function deleteFiltersFromDashboard(filterName) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200)
            window.location.reload();
    };
    xhttp.open("POST", "../../handlers/add_filter/filter_handler.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("deleteFilter=" + filterName);
}

function applyFilters(fromPage) {
    let filters = [];
    filters[0] = applyClientFilter();
    filters[1] = applyObjectFilter();
    filters[2] = applyTypeFilter();
    filters[3] = applyTimeFilter();

    applyFiltersToSession(filters, fromPage);

}

function applyClientFilter() {
    let clientFilter = "";
    let clients = document.getElementsByClassName('clients filter-option');

    for (let i = 0; i < clients.length; i++) {
        let clientClasses = clients[i].classList;
        for (let j = 0; j < clientClasses.length; j++) {
            if (clientClasses[j] === 'active') {
                clientFilter += clients[i].id;
                break;
            }
        }
    }

    return clientFilter;
}

function applyObjectFilter() {
    let objectFilter = "";
    let objects = document.getElementsByClassName('objects filter-option');

    for (let i = 0; i < objects.length; i++) {
        let objectClasses = objects[i].classList;
        for (let j = 0; j < objectClasses.length; j++) {
            if (objectClasses[j] === 'active') {
                objectFilter += objects[i].id;
                break;
            }
        }
    }

    return objectFilter;
}

function applyTypeFilter() {
    let typeFilter = "";
    let types = document.getElementsByClassName('type filter-option');

    for (let i = 0; i < types.length; i++) {
        let typeClasses = types[i].classList;
        for (let j = 0; j < typeClasses.length; j++) {
            if (typeClasses[j] === 'active') {
                typeFilter += types[i].id;
                break;
            }
        }
    }

    return typeFilter;
}

function applyTimeFilter() {
    let pickmeupInputs = document.getElementsByClassName('pmu-selected');
    let dateFrom = new Date(Date.parse(pickmeupInputs[2].innerHTML + " " + pickmeupInputs[1].innerHTML + " " + pickmeupInputs[0].innerHTML));
    let dateTo = new Date(Date.parse(pickmeupInputs[5].innerHTML + " " + pickmeupInputs[4].innerHTML + " " + pickmeupInputs[3].innerHTML));

    return dateFrom.getTime() + ":" + dateTo.getTime();
}

function sendControlType(type) {
    window.location.replace("notifications.php?typeOfControl=" + type);
}

function applyTimeFilterForFollowingPeriod(id) {
    let followingDays = document.getElementById(id).parentNode.childNodes[1].value;
    if (followingDays > 0) {
        let timeFilter = parseTimeFilterForFollowingPeriod(followingDays);
        ajaxCallApplyTimeFilterForFollowingPeriod(timeFilter);
    }
}

function parseTimeFilterForFollowingPeriod(followingDays) {
    let dateFrom = new Date();
    let dateTo = new Date();
    dateTo.setDate(dateTo.getDate() + parseInt(followingDays));
    dateTo.setSeconds(0); dateTo.setMinutes(0); dateTo.setHours(0);
    return dateFrom.getTime() + ":" + dateTo.getTime();
}


