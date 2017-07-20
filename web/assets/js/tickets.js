/********* scripts page tickets *************/

$('#previousBookingPath').attr('href', "javascript:history.go(-1)");

var checkPrice = function() {
    //Get the day, month and year of today
    var today = new Date();
    var dt = today.getDate();
    var mt = today.getMonth()+1;
    var yt = today.getFullYear();

    //for each row of the table
    for (var i=0; i< $('.ageDate').length; i++)
    {   // get the selected day, month and year
        var dd = Number($('.ageDate > select:first-child').eq(i).val());
        var md = Number($('.ageDate > select:nth-child(2)').eq(i).val());
        var yd = Number($('.ageDate > select:last-child').eq(i).val());

        var yearAge = yt - yd; //get age
        if (md > mt || (md == mt && dd >= dt)) { //if birthday's date is not reached yet
            yearAge--; //Remove one year to age
        }
        

        var price = Number($('.priceDisplayed').eq(i).text());
        //setting price according to age
        if (yearAge >= 60) {
            price = 12;
        }else if (yearAge >= 4 && yearAge < 12) {
            price = 8;
        }else if (yearAge < 4){
            price = 0;
        }else {
            price = 16;
        }
        //for the selected row, if checkbox is checked, set price to 10
        price = $(':checkbox').eq(i).is(':checked')? 10 : price;

        //get the type of booking
        var type = $('#type').text();
        //if type is "Demi-journée", price is divided by 2
        if (type == "Demi-journée") {
            price /=2 ;
        }
        //modify the value of element in DOM
        $('.priceDisplayed').eq(i).text(price); 
    }
    
};

var checkAmount = function() {
    var amount = 0;
    $('.priceDisplayed').each(function(index) {
        amount += Number($(this).text());
    });
    $('#total').text(amount);

}

//grouping the two function into one for easier call
var checkPrices = function() {
    checkPrice();
    checkAmount();
}

checkPrices();

$(function() { 
    //call to checkPrices each time a select is changed
    $('.ageDate > select').change(function() {
        checkPrices();
    });  
    // and each time a checkbox value is changed
    $(':checkbox').change(function() {
        checkPrices();
    });  
});




