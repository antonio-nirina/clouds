
jQuery(document).ready(function() {
	
	jQuery('span#checked-unchecked-radio-souvenir').on('click', function(){
		$(this).removeClass('checked-souvenir');
		$(this).removeClass('check-souvenir');
		if($('input#remember_me').prop('checked')){
			$('input#remember_me').removeAttr('checked');
			$(this).addClass('check-souvenir');
			$(this).removeClass('checked-souvenir');
		}else{
			$('input#remember_me').attr('checked', true);
			$(this).removeClass('check-souvenir');
			$(this).addClass('checked-souvenir');
		}
	});
});
