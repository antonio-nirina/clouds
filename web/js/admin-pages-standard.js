$(document).ready(function(){
	$(document).on('click', 'ul.list-choix-page li[data-role="onglet"]', function(){
		//Géstions des clicks des onglets
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
		
		//Géstions des affichages des contenus des pages
		var Id = $(this).attr('id');
		var ArrayId = new Array;
		ArrayId = Id.split('-');
		var IdLi = ArrayId[3];
		

		var UrlAffichePagesStandard = $('input#url_ajax_affiche_page').val();
		
		var CloneChargement = $('p.chargementAjax').clone();
		$('div#id-content-dynamic-pages').html(CloneChargement);
		$('div#id-content-dynamic-pages p.chargementAjax').show();
		
		var NewPageName = '';
		if($(this).hasClass('new_page')){
			NewPageName = $(this).find('span.lib-onglet-choix-page').html();
		}
		
		$.ajax({
            type: 'POST',
            url: UrlAffichePagesStandard,
			data:'id_page='+IdLi+'&new_page='+NewPageName+'',
            success: function(html){
                $('div#id-content-dynamic-pages').html('');
                $('div#id-content-dynamic-pages').html(html);
            }
        });

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
	
	//Ajout des nouvel page standard
	$(document).on('click', 'li.pages-list-ajout' ,function(){
		var NbrePages = 0;
		$('ul.list-choix-page li').each(function(e){
			NbrePages++;
		});
		
		var NbreNewPage = 0;
		$('ul.list-choix-page li.new_page').each(function(i){
			NbreNewPage++;
		});
		
		var LastNewPage = NbreNewPage + 1;
		
		var LastLiPos = NbrePages - 2;
		
		
		var IdLastLi = $('ul.list-choix-page li:eq('+LastLiPos+')').attr('id');
		var ArrayIdLastLi = new Array;
		ArrayIdLastLi = IdLastLi.split('-');
		
		var LastIdLi = parseInt(ArrayIdLastLi[3])+1;
		
		var HtmlNewPage = '';
		HtmlNewPage += '<li id = "li-onglet-page-'+LastIdLi+'" data-role = "onglet" class = "pages-list new_page">';
		HtmlNewPage += '<span class = "lib-onglet-choix-page">Page '+LastNewPage+'</span>';
		HtmlNewPage += '<span id = "onglet-page-LastIdLi" class = "check-onglet-choix-page" data-role = "checked-unchecked"></span>';
		HtmlNewPage += '</li>';
		
		$(HtmlNewPage).insertAfter('ul.list-choix-page li:eq('+LastLiPos+')');
	});
	
	//Saisie sur le nom du menu
	$(document).on('keyup', 'input.renomer-page', function(){
		var IdInputMenu = $(this).attr('id');
		var ArrayIdInputMenu = new Array;
		ArrayIdInputMenu = IdInputMenu.split('-');
		var Id = ArrayIdInputMenu[2];
		var NomMenuPage = $(this).val();
		
		$('li#li-onglet-page-'+Id+'').find('span.lib-onglet-choix-page').html(NomMenuPage);
	});
	
	//Simuler btn upload file
	$(document).on('click', '#btn-upload-img-page-standard', function(){
		$('#input-upload-img-page-standard').click();
		return;
	});
	
	$(document).on('change', '#input-upload-img-page-standard', function(){
		$('#btn-upload-img-page-standard').removeClass('btn-valider');
		$('#btn-upload-img-page-standard').addClass('btn-valider-etat');
		$('#id-lib-upload').removeClass('upload');
		$('#id-lib-upload').addClass('uploaded');
		$('.img-delete-img').show();
		
		var FilePath = $(this).val();
		var ArrayFilePath = new Array;
		ArrayFilePath = FilePath.split("\\");
		
		var ArrayFilePathLenght = ArrayFilePath.length;
		var FileName = ArrayFilePath[ArrayFilePathLenght-1];
		$('#lib-btn-pages').html(FileName);
		$('#lib-btn-pages').removeAttr('style');
		$('#lib-btn-pages').attr('style', 'color:var(--couleur_1)!important;');
	});
	
	//Cliqur sur la premiere onglet
	$('ul.list-choix-page li[data-role="onglet"]:first-child').trigger('click');
});