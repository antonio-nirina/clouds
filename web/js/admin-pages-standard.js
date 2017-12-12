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
		
		$('div.content-page-body').each(function(j){
			$(this).hide();
		});
		
		$('div#id-content-dynamic-pages').show();
		$('div#id-content-page-body-'+IdLi+'').show();
		
		var UrlCustomCkeditor = $('input#url_customs_ckeditor').val();
		CKEDITOR.replace( 'login_portal_data_form_text_'+IdLi+'', {
			language: 'fr',
			uiColor: '#9AB8F3',
			height: 150,
			width: 600,
			customConfig: UrlCustomCkeditor,
		});
		
	});
	
	$(document).on('click', 'span[data-role="checked-unchecked"]', function(){
		var IdPages = $(this).attr('id');
		var ArrayIdPages = new Array;
		ArrayIdPages = IdPages.split('-');
		var Id = ArrayIdPages[2];
		
		if($(this).hasClass('check-onglet-choix-page')){
			$(this).removeClass('check-onglet-choix-page');
			$(this).addClass('checkon-onglet-choix-page');
			$('#id-status-page-input-'+Id+'').val('1');
		}else{
			$(this).removeClass('checkon-onglet-choix-page');
			$(this).addClass('check-onglet-choix-page');
			$('#id-status-page-input-'+Id+'').val('0');
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
		HtmlNewPage += '<input id = "id-nom-page-input-'+LastIdLi+'" type = "hidden" name = "nom_page[]" value = "Page '+LastNewPage+'">';
		HtmlNewPage += '<input id = "id-status-page-input-'+LastIdLi+'" type = "hidden" name = "status_page[]" value = "0">';
		HtmlNewPage += '<input id = "id-id-page-input-'+LastIdLi+'" type = "hidden" name = "id_page[]" value = "'+LastIdLi+'">';
		HtmlNewPage += '</li>';
		
		$(HtmlNewPage).insertAfter('ul.list-choix-page li:eq('+LastLiPos+')');
		
		var HtmlNewPageContent = '';
		HtmlNewPageContent += '<div id = "id-content-page-body-'+LastIdLi+'" class = "content-page-body" style = "display:none;">';
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form">nom du menu</span>';
		HtmlNewPageContent += '<input id = "input-menu-'+LastIdLi+'" class = "input-form-text renomer-page" type = "text" name = "menu_page[]" value = "Page '+LastNewPage+'" placeholder = "Page '+LastNewPage+'" style = "padding-left:10px!important;">';
		HtmlNewPageContent += '</label>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		
		HtmlNewPageContent += '<div class = "titre-section-page-standard"><span>Bannière de haut de page</span></div>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form">titre</span>';
		HtmlNewPageContent += '<input style = "padding-left:10px!important;" class = "input-form-text" type = "text" name = "titre_page[]" value = "Page '+LastNewPage+'" placeholder = "Page '+LastNewPage+'">';
		HtmlNewPageContent += '</label>';
		
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form block">ajouter logo ou image</span>';
		HtmlNewPageContent += '<button id = "btn-upload-img-page-standard-'+LastIdLi+'" class="btn-valider btn-upload choose-upload-img-button" type = "button">';
		HtmlNewPageContent += '<span id = "id-lib-upload-'+LastIdLi+'" class="upload"></span> ';
		HtmlNewPageContent += '<span id = "lib-btn-pages-'+LastIdLi+'">choisissez un fichier...</span>';
		HtmlNewPageContent += '</button>';
		HtmlNewPageContent += '<input style = "display:none;" id = "input-upload-img-page-standard-'+LastIdLi+'" type = "file" name = "img_page[]" class = "input-upload-img-page-standard">';
		HtmlNewPageContent += '<div class = "info-container">';
		HtmlNewPageContent += '<p>les fichiers doivent peser moins de <span class="bigger">8 Mo</span></p>';
		HtmlNewPageContent += '<p>formats de fichier pris en charge : <span class="bigger">jpeg, png, gif</span></p>';
		HtmlNewPageContent += '</div>';
		HtmlNewPageContent += '</label>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form block">prévisualisation</span>';
		HtmlNewPageContent += '<span id = "img-preview-'+LastIdLi+'" class = "previsualisation-im-page-standard block"></span>';
		HtmlNewPageContent += '</label>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		HtmlNewPageContent += '<div class = "titre-section-page-standard"><span>Contenu de la page</span></div>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form block editeur">vos textes, images, etc que vous voulez voir apparaitre dans la page</span>';
		HtmlNewPageContent += '<textarea id="login_portal_data_form_text_'+LastIdLi+'" name="contenu_page[]" class="large-textarea"></textarea>';
		HtmlNewPageContent += '</label>';
		
		HtmlNewPageContent += '</div>';
		
		
		$(HtmlNewPageContent).insertAfter('div#id-content-page-body-'+ArrayIdLastLi[3]+'');
	});
	
	//Saisie sur le nom du menu
	$(document).on('keyup', 'input.renomer-page', function(){
		var IdInputMenu = $(this).attr('id');
		var ArrayIdInputMenu = new Array;
		ArrayIdInputMenu = IdInputMenu.split('-');
		var Id = ArrayIdInputMenu[2];
		var NomMenuPage = $(this).val();
		
		$('li#li-onglet-page-'+Id+'').find('span.lib-onglet-choix-page').html(NomMenuPage);
		$('li#li-onglet-page-'+Id+'').find('input#id-nom-page-input-'+Id+'').val(NomMenuPage);
	});
	
	//Simuler btn upload file
	$(document).on('click', '.choose-upload-img-button', function(){
		var Id = $(this).attr('id');
		var ArrayId = new Array;
		ArrayId = Id.split('-');
		var IdBtn = ArrayId[5];
		
		$('#input-upload-img-page-standard-'+IdBtn+'').click();
		return;
	});
	
	$(document).on('change', '.input-upload-img-page-standard', function(){
		var Id = $(this).attr('id');
		var ArrayId = new Array;
		ArrayId = Id.split('-');
		var IdBtn = ArrayId[5];
		
		$('#btn-upload-img-page-standard-'+IdBtn+'').removeClass('btn-valider');
		$('#btn-upload-img-page-standard-'+IdBtn+'').addClass('btn-valider-etat');
		$('#id-lib-upload-'+IdBtn+'').removeClass('upload');
		$('#id-lib-upload-'+IdBtn+'').addClass('uploaded');
		//$('#img-delete-img-'+IdBtn+'').show();
		
		var FilePath = $(this).val();
		var ArrayFilePath = new Array;
		ArrayFilePath = FilePath.split("\\");
		
		var ArrayFilePathLenght = ArrayFilePath.length;
		var FileName = ArrayFilePath[ArrayFilePathLenght-1];
		$('#lib-btn-pages-'+IdBtn+'').html(FileName);
		$('#lib-btn-pages-'+IdBtn+'').removeAttr('style');
		$('#lib-btn-pages-'+IdBtn+'').attr('style', 'color:var(--couleur_1)!important;');
		
		//Afficher la prévisualisation
		readURL(this, IdBtn);
	});
	
	$(document).on('click', '.img-delete-img', function(){
		var Id = $(this).attr('id');
		var ArrayId = new Array;
		ArrayId = Id.split('-');
		var IdImg = ArrayId[3];
		
		
		//Supprimer l'image 
		var UrlSupprimeImg = $('input#url_ajax_supprimer_img').val();
		$.ajax({
            type: 'POST',
            url: UrlSupprimeImg,
			data:'id_page='+IdImg+'',
            success: function(retour){
				if(retour == 'ok'){
					$('#btn-upload-img-page-standard-'+IdImg+'').removeClass('btn-valider-etat');
					$('#btn-upload-img-page-standard-'+IdImg+'').addClass('btn-valider');
					$('#id-lib-upload-'+IdImg+'').removeClass('uploaded');
					$('#id-lib-upload-'+IdImg+'').addClass('upload');
					$('#lib-btn-pages-'+IdImg+'').html('choisissez un fichier...');
					$('#img-delete-img-'+IdImg+'').hide();
					$('span#img-preview-'+IdImg+' img').remove();
				}
            }
        });
		return false;
	});
	
	$(document).on('click', 'span#fermerPopUp', function(){
		$('#conteneur-popup').hide();
		$('#body-popup').hide();
	});
	
	//Simuler click bouton upload editor
	$(document).on('click', 'button#btn-upload-img-ckeditor', function(){
		$('input#input-upload-img-ckeditor').click();
	});
	
	//upload file ckeditor
	$(document).on('change', 'input#input-upload-img-ckeditor', function(){
		$('form#UploadImgEditor').submit();
	});
	
	$(document).on('submit', 'form#UploadImgEditor', function(event) {
		event.preventDefault();
		var form = $('form#UploadImgEditor').get(0);
		var formData = new FormData(form);
		var UrlUploadImgEditor = $('input#url_upload_img_editor').val();
		
		var Chargements = $('p.chargementAjax').clone();
		$('div.conteneur-liste-galery').html(Chargements);
		$('div.conteneur-liste-galery').find('p.chargementAjax').show();
		$('div.conteneur-liste-galery').find('p.chargementAjax img').css('top', '23px');
		
		setTimeout(function(){
			$.ajax({
				url: UrlUploadImgEditor, 
				data: formData,                         
				type: 'POST',
				processData: false,
				contentType: false,
				success: function(response){
					//On liste les images dans la galérie
					ListeImgGalerie(response);
				}
			});
		},500);
	
		return false;
	});
	
	$(document).on('click', 'span.select-img-editor', function(){
		var Id = $(this).attr('id');
		$('span.select-img-editor').each(function(i){
			if($(this).hasClass('checkon-onglet-choix-page')){
				$(this).removeClass('checkon-onglet-choix-page');
				$(this).addClass('check-onglet-choix-page');
			}
		});
		
		$(this).addClass('checkon-onglet-choix-page');
		$('input#img-a-inserer').val(Id);
	});
	
	$(document).on('click', 'button#btn-inserer-img-editor-ok', function(){
		var Img = $('input#img-a-inserer').val();
		if($.trim(Img) != ""){
			var IdCk = $('li.page-list-active').attr('id');
			var ArrayIdCk = new Array;
			ArrayIdCk = IdCk.split('-');
			var Id = ArrayIdCk[3];
			
			var img_html = "<img src='"+Img+"'/>'";
			var editorName = 'login_portal_data_form_text_'+Id+'';
			var editor = CKEDITOR.instances[editorName];
			editor.insertHtml(img_html);
			$('span#fermerPopUp').click();
		}
	});
	
	
	//Cliqur sur la premiere onglet
	$('ul.list-choix-page li[data-role="onglet"]:first-child').trigger('click');
});

function ListeImgGalerie(programme_id){
	$('div.conteneur-liste-galery').html('');
	
	var Chargements = $('p.chargementAjax').clone();
	$('div.conteneur-liste-galery').html(Chargements);
	$('div.conteneur-liste-galery').find('p.chargementAjax').show();
	$('div.conteneur-liste-galery').find('p.chargementAjax img').css('top', '23px');
	
	var UrlListeImg = $('input#url_list_img_editor').val();
	setTimeout(function(){
		$.ajax({
			url: UrlListeImg, 
			data: 'programme_id='+programme_id+'',                         
			type: 'POST',
			success: function(response){
				$('div.conteneur-liste-galery').html('');
				$('div.conteneur-liste-galery').html(response);
			}
		});
	},500);
	
}

function readURL(input, idLine) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			var Img = '';
			Img += '<img src = "'+e.target.result+'" alt = "Chargement ..." style = "width: inherit;max-height: inherit;object-fit: contain;">';
			$('span#img-preview-'+idLine+'').html(Img);
		}
		reader.readAsDataURL(input.files[0]);
	}
}