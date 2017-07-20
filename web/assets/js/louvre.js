/********* scripts global *********/

if 
$(function() {
	var progess = Number($('.progress-bar').attr('aria-valuenow'));
	switch (progess) {
	case 25:
		$('#bc-home').replaceWith('<li id="bc-home"><a href="javascript:history.go(-1)">Accueil</a></li>');

		$('#bc-booking').attr('style', 'display');
		$('#bc-booking').addClass('active');
		break;
	case 50:
		$('#bc-booking').removeClass('active');
		$('#bc-ticket').attr('style', 'display');
		$('#bc-ticket').addClass('active');
		break;
	case 75:
		$('#bc-ticket').removeClass('active');
		$('#bc-review').attr('style', 'display');
		$('#bc-review').addClass('active');
		break;
	case 100:
		$('#bc-review').removeClass('active');
		$('#bc-confirm').attr('style', 'display');
		$('#bc-confirm').addClass('active');
		break;
	}
});
