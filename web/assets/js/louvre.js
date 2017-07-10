$('#booking_date').datepicker({
    changeMonth: true,
    changeYear: true,          
    dateFormat: 'dd/mm/yy',
    yearRange: "-0:+1",
    minDate: 0,
    defaultDate: "0",
    beforeShowDay: function(date) {
        var d = date.getDate();
        var m = date.getMonth() + 1;
        var closed = date.getDay();
        var isDisabled = (closed === 0 || closed === 2) || (d === 1 && m === 5) || ( d === 1 && m === 11) || (d === 25 && m === 12);
        return [!isDisabled];
    }
});

$('.js-datepicker').datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:-4",
    dateFormat: 'dd/mm/yy',   
});


