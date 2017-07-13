$('#booking_date').datepicker({
    changeMonth: true,
    changeYear: true,          
    dateFormat: 'dd/mm/yy',
    yearRange: "-0:+1",
    minDate: 0,
    defaultDate: 0,
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
    defaultDate: '-4y',
    yearRange: "-100:-4",
    dateFormat: 'dd/mm/yy',   
});


var checkDate = function() 
{
    var today = new Date();
    var dt = today.getDate();
    var mt = today.getMonth()+1; //January is 0!
    var yyt = today.getFullYear();
    var ht = today.getHours();     
    
    var date = $( "#booking_date" ).datepicker( "getDate");
    var dd = date.getDate();
    var md = date.getMonth()+1; //January is 0!
    var yyd = date.getFullYear();
    
    if(ht>=14 && dd==dt && md==mt && yyd==yyd) {
      $('input#booking_type_0').attr('disabled', true);
      $(':radio').val(['Demi-journée']);
    }else {
        $('input#booking_type_0').attr('disabled', false);
    }
};

checkDate();

$(function() {
    $('#booking_date').change(function() {   
        checkDate();
    });
});


var checkAge = function(){
    var date = $('.js-datepicker').datepicker('getDate');
    var today = new Date();
    var dt = today.getDate();
    var mt = today.getMonth()+1; //January is 0!
    var yyt = today.getFullYear();
    console.log(date);   
    var dd = date.getDate();
    var md = date.getMonth()+1; //January is 0!
    var yyd = date.getFullYear();
    var yearAge = yyt-yyd;
    if (md == mt)
    {
        if(dd > dt)
        {
            yearAge--;
        }
    }else if (md > mt)
    {
        yearAge--;
    }
    var age = yearAge;
    console.log(age);
    var price = 16;
    if(age < 4){
        price = 0;
    }else if (age >= 4 && age <= 12) {
        price = 8;
    }else if (age >= 60) {
        price = 12;
    }    
    console.log(price);
    $('.price').text(price);
 }
 
$('.js-datepicker').change(function() {
    checkAge();
 });