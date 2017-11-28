$(document).ready(function(){
	$(document).on('click', 'ul.list-choix-page li[data-role="onglet"]', function(){
		$('ul.list-choix-page li[data-role="onglet"]').each(function(e){
			if($(this).hasClass('page-list-active')){
				$(this).removeClass('page-list-active');
				$(this).addClass('pages-list');
				$(this).find('span.check-onglet-choix-page').css('opacity', '0.5');
				$(this).find('span.checkon-onglet-choix-page').css('opacity', '0.5');
			}
		});

		$(this).removeClass('pages-list');
		$(this).addClass('page-list-active');
		$(this).find('span.check-onglet-choix-page').css('opacity', '1');
		$(this).find('span.checkon-onglet-choix-page').css('opacity', '1');
	});
	
	$(document).on('click', 'span[data-role="checked-unchecked"]', function(){
		if($(this).hasClass('check-onglet-choix-page')){
			$(this).removeClass('check-onglet-choix-page');
			$(this).addClass('checkon-onglet-choix-page');
		}else{
			$(this).removeClass('checkon-onglet-choix-page');
			$(this).addClass('check-onglet-choix-page');
		}
	});
});