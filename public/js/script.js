$('#startDate').change( function() {
    alert($('#startDate').value());
    $('#endDate').attr({
        "min" : $var
     });    
});