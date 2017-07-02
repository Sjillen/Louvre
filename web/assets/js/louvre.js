

$(function() {
    $('.js-datepicker').datepicker({
        changeMonth: true,
        changeYear: true,          
        dateFormat: 'dd/mm/yy',
        yearRange: "-0:+1",
        minDate: 0,
        defaultDate: "0",
        beforeShowDay: function(date) {
          var day = date.getDay();
          var month = date.getMonth() + 1;
          
           return [ (!(month == 11 && day == 1) || !(month == 5 && day == 1)  || !(month == 12 && day == 25)  || !(day == 0)  || !(day == 2) ), ''];
        },

    });
});


