

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
               return [(day != 2), ''];
            }
       });
    });
