/********* scripts page review *************/


$('#recapTicketPath').attr("href", "javascript:history.go(-1)");

$(function() {
	for (var i=0; i < $('.priceTicket').length; i++)
	{
		switch(Number($('.priceTicket').eq(i).text()))
		{
			case 0:
				$('.discount').eq(i).text('Gratuit');
				break;
			case 8:
				$('.discount').eq(i).text('Enfant');
				break;
			case 10:
				$('.discount').eq(i).text('Réduit');
				break;
			case 12:
				$('.discount').eq(i).text('Sénior');
				break;
			case 16:
				$('.discount').eq(i).text('Normal');
				break;
		}
	}
});
