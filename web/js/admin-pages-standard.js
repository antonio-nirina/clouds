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
		
		var OngletActif = $.trim($(this).find('span.lib-onglet-choix-page').html());
		$('input#onglet-selectionner-page').val(OngletActif);
		
		//Liste des pages par defaut
		var ListePageDefaut = [ 
			'présentation de la société',
			'découvrez le programme',
			'actualité des partenaires',
			'cadeaux',
			'contact',
			'mentions légales <span style="font-size:12px;">(footer)</span>',
			'règlement <span style="font-size:12px;">(footer)</span>'
		];
		
		//Afficher bouton supprimer
		if(ListePageDefaut.indexOf(OngletActif) < 0){
			$('button#btn-suppr-page-standard').show();
		}else{
			$('button#btn-suppr-page-standard').hide();
		}

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
		
		
		if($('textarea#login_portal_data_form_text_'+IdLi+'').hasClass('contact-textarea')){
			var UrlCustomCkeditor = $('input#url_customs_ckeditor_simple').val();
		}else{
			var UrlCustomCkeditor = $('input#url_customs_ckeditor').val();
		}
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
		HtmlNewPageContent += '<input id = "input-menu-'+LastIdLi+'" class = "input-form-text renomer-page" type = "text" name = "menu_page[]" value = "Page '+LastNewPage+'" style = "padding-left:10px!important;">';
		HtmlNewPageContent += '<span class="delete-input"></span>';
		HtmlNewPageContent += '</label>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		
		HtmlNewPageContent += '<div class = "titre-section-page-standard"><span>Bannière de haut de page</span></div>';
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		HtmlNewPageContent += '<label class = "champForm">';
		HtmlNewPageContent += '<span class = "lib-form">titre</span>';
		HtmlNewPageContent += '<input style = "padding-left:10px!important;" class = "input-form-text" type = "text" name = "titre_page[]" value = "Page '+LastNewPage+'">';
		HtmlNewPageContent += '<span class="delete-input"></span>';
		HtmlNewPageContent += '</label>';
		
		
		HtmlNewPageContent += '<div class = "clearBoth"></div>';
		
		
		HtmlNewPageContent += '<div class = "champForm">';
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
		HtmlNewPageContent += '</div>';
		
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
	
	$(document).on('click', 'button#btn-suppr-page-standard', function(){
		$('ul.list-choix-page li').each(function(i){
			if($(this).hasClass('page-list-active')){
				var IdLiActive = $(this).attr('id');
				var ArrayIdLiActive = new Array;
				ArrayIdLiActive = IdLiActive.split('-');
				var ID = ArrayIdLiActive[3];
				
				//On supprime la page
				$('#conteneur-popup').show();
				$('#body-popup').show();
				
				var Html = '';
				Html += '<div class = "InfosSuppressionPage" style = "padding:20px;">';
				Html += '<h3 class = "titrePopUp">supprimer la page<span id = "fermerPopUp">X</span></h3>';
				Html += '<p style = "margin: 0;padding: 0;margin-left: 11px;margin-top:20px;">cette page sera supprimée des onglets et du menu, vous pourrez la recréer en cliquant sur "ajouter une page".</p>';
				Html += '<input id = "id-id-page" type = "hidden" name = "id-page" value = "'+ID+'">';
				Html += '<p style = "text-align:center;margin-top:20px;"><button id = "btn-supprimer-page-confirmation" class="btn-valider valider submit-form">';
				Html += 'valider';
				Html += '</button></p>';
				Html += '</div>';
				$('#body-popup').html(Html);
			}
		});
	});
	
	$(document).on('click', 'button#btn-supprimer-page-confirmation', function(){
		var IdPage = $('input#id-id-page').val();
		var Chargements = $('p.chargementAjax').clone();
		$('div.InfosSuppressionPage').html(Chargements);
		$('div.InfosSuppressionPage').find('p.chargementAjax').show();
		$('div.InfosSuppressionPage').find('p.chargementAjax img').css('top', '17px');
		
		var UrlSupprPage = $('input#url_suppr_page').val();
		setTimeout(function(){
			$.ajax({
				url: UrlSupprPage, 
				data: 'idpage='+IdPage+'',                         
				type: 'POST',
				success: function(response){
					$('li#li-onglet-page-'+IdPage+'').remove();
					$('div#id-content-page-body-'+IdPage+'').remove();
					$('#conteneur-popup').hide();
					$('#body-popup').hide();
					$('ul.list-choix-page li[data-role="onglet"]:first-child').trigger('click');
				}
			});
		},500);
	});
	

    // reord - monter
    $(document).on('click', '.reorder-up-field-row-link', function(e){
        e.preventDefault();
        var upper_row = $(this).parents('.form-field-row').prev('.form-field-row');

        if(upper_row.length > 0)
        {
            upper_row.before($(this).parents('.form-field-row'));
			$('input.order-input').each(function(i){
				$(this).val(i+1);
			});
        }
    });

    // reord - descendre
    $(document).on('click', '.reorder-down-field-row-link', function(e){
        e.preventDefault();
        var lower_row = $(this).parents('.form-field-row').next('.form-field-row');
        if(lower_row.length > 0)
        {
            lower_row.after($(this).parents('.form-field-row'));
			$('input.order-input').each(function(i){
				$(this).val(i+1);
			});
        }
    });
	
	//Modification champ form contact 
	$(document).on('click', 'a.edit-field-row-link', function(){
		var ID = $(this).attr('id');
		var ArrayID = new Array;
		ArrayID = ID.split('-');
		var IdElmt = ArrayID[4];
		
		AjoutModifChampFormContact(IdElmt);
		
		return false;
	});
	
	$(document).on('click', 'button#btn-modif-champ-form-contact', function(){
		var IdChamp = $('input#Id-champs-form-contact').val();
		
		
		var Libelle = $('input#libelle_label').val();
		var ChoixType = "";
		$('input.type_de_champ').each(function(e){
			if($(this).prop('checked')){
				ChoixType = $(this).val();
			}
		});
		
		if($.trim(Libelle) != ""){
			
			if(IdChamp > 0){
				$('span#span-label-'+IdChamp+'').html(Libelle);

				if(ChoixType == 'text'){
					$('div#content-input-'+IdChamp+'').html("");
					var DivHtml = '';
					DivHtml += '<div class="champs-form">';
					DivHtml += '<input id="cl4-row'+IdChamp+'" class="type-input" name="type_champ[]" value="input-text" type="hidden">';
					DivHtml += '</div>';
					$('div#content-input-'+IdChamp+'').html(DivHtml);
					
				}else if(ChoixType == 'choice-radio'){
					$('div#content-input-'+IdChamp+'').html("");
					var DivHtml = '';
					DivHtml += '<div class="champs-form-radio">';
					DivHtml += '<span class="input-radio"></span>';
					DivHtml += '<span class="input-libelle-radio">oui</span>';
					DivHtml += '</div>';
					
					DivHtml += '<div class="champs-form-radio">';
					DivHtml += '<span class="input-radio"></span>';
					DivHtml += '<span class="input-libelle-radio">non</span>';
					DivHtml += '</div>';
					DivHtml += '<input id="cl4-row'+IdChamp+'" class = "type-input" name = "type_champ[]" type = "hidden" value = "input-radio">';
					$('div#content-input-'+IdChamp+'').html(DivHtml);
				}
			}else{
				var IDAl = aleatoire(54646468);
				var OrderEnCours = $('tr.form-field-row').length;
				var Ordre = parseInt(OrderEnCours) + 1;
				
				var DivHtml = '';
				DivHtml += '<tr class="form-field-row" data-field-id="'+IDAl+'">';

				DivHtml += '<td class="field-parameter-state-container">';
				DivHtml += '<input id="cl1-row'+IDAl+'" class="form-field-published styled-checkbox" name="publier[message]" type="checkbox">';
				DivHtml += '<label for="cl1-row'+IDAl+'"><span></span></label>';
				DivHtml += '<input id="cl0-row'+IDAl+'" class="order-input" name="ordre[]" value="'+Ordre+'" type="hidden">';
				DivHtml += '</td>';

				DivHtml += '<td class="field-parameter-state-container">';
				DivHtml += '<input id="cl2-row'+IDAl+'" class="form-field-mandatory styled-checkbox" name="obligatoire[message]" type="checkbox">';
				DivHtml += '<label for="cl2-row'+IDAl+'"><span></span></label>';    
				DivHtml += '</td>';

				DivHtml += '<td class="field-container">';
				DivHtml += '<div class="content-champ-form">';
				DivHtml += '<div class="content-label">';
				DivHtml += '<label>';
				DivHtml += '<span id="span-label-'+IDAl+'">'+Libelle+'</span>';
				DivHtml += '<input id="cl3-row'+IDAl+'" class="label-input" name="label[]" value="'+Libelle+'" type="hidden">';
				DivHtml += '</label>';
				DivHtml += '</div>';
				
				DivHtml += '<div id="content-input-'+IDAl+'" class="content-input">';

				if(ChoixType == 'text'){
					DivHtml += '<div class="champs-form">';
					DivHtml += '<input id="cl4-row'+IDAl+'" class="type-input" name="type_champ[]" value="input-text" type="hidden">';
					DivHtml += '</div>';
				}else if(ChoixType == 'choice-radio'){
					DivHtml += '<div class="champs-form-radio">';
					DivHtml += '<span class="input-radio"></span>';
					DivHtml += '<span class="input-libelle-radio">oui</span>';
					DivHtml += '</div>';
					
					DivHtml += '<div class="champs-form-radio">';
					DivHtml += '<span class="input-radio"></span>';
					DivHtml += '<span class="input-libelle-radio">non</span>';
					DivHtml += '</div>';
					DivHtml += '<input id="cl4-row'+IDAl+'" class = "type-input" name = "type_champ[]" type = "hidden" value = "input-radio">';
				}
				
				
				DivHtml += '</div>';
				
				DivHtml += '</div>';
				
				DivHtml += '</td>';

				DivHtml += '<td class="field-edit-option-container"><a id="edit-field-row-link-'+IDAl+'" href="#" class="edit-field-row-link"></a></td>';

				DivHtml += '<td class="field-reorder-option-container">';
				DivHtml += '<a href="#" class="reorder-up-field-row-link"></a>';
				DivHtml += '<a href="#" class="reorder-down-field-row-link"></a>';
				DivHtml += '</td>';
				DivHtml += '</tr>';
				
				
				var DernierTr = parseInt($('table#ListeChampFormContact tbody tr').length) - 1;
				var TrId = $('table#ListeChampFormContact tbody tr').eq(DernierTr).attr('data-field-id');
				
				if(DernierTr <= 9){
					$('tr[data-field-id="'+TrId+'"]').after(DivHtml);
				}
			}
			
			
			$('#conteneur-popup').hide();
			$('#body-popup').hide();
			$('#body-popup').html('');
		}
	});
	
	//Ajout champ
	$(document).on('click', 'a.add-field-link', function(){
		AjoutModifChampFormContact(0);
		return false;
	});
	
	
	//Cliqur sur la premiere onglet
	var OngletActif = $('input#onglet-selectionner-page').val();
	if($.trim(OngletActif) != ""){
		$('ul.list-choix-page li[data-role="onglet"]').each(function(i){
			var PageName = $(this).find('span.lib-onglet-choix-page').html();
			if($.trim(PageName) == $.trim(OngletActif)){
				var Id = $(this).attr('id');
				$('ul.list-choix-page li#'+Id+'').trigger('click');
			}
		});
	}else{
		$('ul.list-choix-page li[data-role="onglet"]:first-child').trigger('click');
	}
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

function AjoutModifChampFormContact(IdChamp){
	$('#conteneur-popup').show();
	$('#body-popup').show();
	
	var IdElmt = IdChamp;
	
	var Label = "";
	if(IdElmt > 0){
		Label = $('span#span-label-'+IdElmt+'').html();
	}
	
	var TypeChamp = "";
	if(IdElmt > 0){
		TypeChamp = $('input#cl4-row'+IdElmt+'').val();
	}
	
	var Html = '';
	Html += '<div class = "ModifChampForm" style = "padding:20px;">';
	Html += '<h3 class = "titrePopUp" style = "font-size:25px;">personnaliser le champ<span id = "fermerPopUp">X</span></h3>';
	
	Html += '<form style="width: 90%;margin: 43px auto;">';
	Html += '<input id = "Id-champs-form-contact" type = "hidden" name = "Id-champs-form-contact" value = "'+IdElmt+'">';
	Html += '<div class="row form-row">';
	Html += '<div class="col-lg-3">';
	Html += '<label class="form-label">intitulé du champ</label>';
	Html += '</div>';
	Html += '<div class="col-lg-9 vertical-align-element-container">';
	Html += '<input id = "libelle_label" class="input-text removable-content-input fixed-size" name="intitule" placeholder="par exemple : nom de l\'agence, région, etc." type="text" value = "'+Label+'">';
	Html += '<span class="delete-input"></span>';
	Html += '</div>';
	Html += '</div>';
	

	Html += '<div class="row form-row">';
	Html += '<div class="col-lg-3">';
	Html += '<label class="form-label" for="alpha-radio">type alphanumérique</label>';
	Html += '</div>';
	Html += '<div class="col-lg-9">';
	
	if(TypeChamp == 'input-text' || TypeChamp == 'input-textarea'){
		Html += '<input class="styled-radio type_de_champ" id="alpha-radio" name="type_field" value="text" type="radio" checked = "checked">';
	}else{
		Html += '<input class="styled-radio type_de_champ" id="alpha-radio" name="type_field" value="text" type="radio">';
	}
	
	Html += '<label for="alpha-radio"></label>';
	Html += '<label for="alpha-radio" class="narrow-block-label">';
	Html += '<div class="champs-form" style = "height:30px;width:200px;margin-left: 11px !important;"></div>';
	Html += '</label>';
	Html += '</div>';
	Html += '</div>';


	Html += '<div class="row form-row choice-radio-row">';
	Html += '<div class="col-lg-3">';
	Html += '<label class="form-label" for="choice-radio">type case à cocher</label>';
	Html += '</div>';
	Html += '<div class="col-lg-9">';
	
	if(TypeChamp == 'input-radio'){
		Html += '<input class="styled-radio type_de_champ" id="choice-radio" name="type_field" value="choice-radio" type="radio" checked = "checked">';
	}else{
		Html += '<input class="styled-radio type_de_champ" id="choice-radio" name="type_field" value="choice-radio" type="radio">';
	}
	
	Html += '<label for="choice-radio"></label>';
	Html += '<label for="choice-radio" class="block-label">';
	Html += '<div class="champs-form-radio">';
	Html += '<span class="input-radio"></span>';
	Html += '<span class="input-libelle-radio">oui</span>';
	Html += '</div>';
	Html += '<div class="champs-form-radio">';
	Html += '<span class="input-radio"></span>';
	Html += '<span class="input-libelle-radio">non</span>';
	Html += '</div>';
	Html += '</label>';
	Html += '</div>';
	Html += '</div>';
	

	
	Html += '<p style = "text-align:center;margin-top:20px;clear:both;"><button id = "btn-modif-champ-form-contact" class="btn-valider valider submit-form" type = "button">';
	
	if(IdElmt > 0){
		Html += 'sauvegarder';
	}else{
		Html += 'ajouter';
	}
	
	Html += '</button></p>';
	Html += '</form>';
	Html += '</div>';
	
	$('#body-popup').html('');
	$('#body-popup').html(Html);
	$('#body-popup').css('height','auto');
	$('#body-popup').css('width','60%!important');
}

function aleatoire(i) {
	if (!i) { i = 100 ; }
	return Math.round(Math.random()*i);
}