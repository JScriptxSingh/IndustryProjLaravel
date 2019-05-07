$(document).ready(setMinEndDate());

function setMinEndDate() {
    var date = new Date($('#startDate').val());

    var month = date.getMonth() + 1;
    var year = date.getFullYear();

    if (month == 12) {
        year += 1;
        month = 1;
    } else {
        month += 1;
    }

    $('#endDate').prop(
        "min", year + '-' + month.toString().padStart(2, '0')
    );
}