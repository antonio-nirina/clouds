CKEDITOR.plugins.add( 'insertionlienscloudrewards', {
    icons: 'insertionlienscloudrewards',
    init: function( editor ) {
        //Creating an Editor Command
		editor.addCommand( 'openPopUpLink', {
			exec: function( editor ) {
				/*
				var now = new Date();
				editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
				*/

				//Ouvre le popUp
				$('#conteneur-popup').show();
				$('#body-popup').removeAttr('style');
				$('#body-popup').attr('style', 'width:60%;margin-left:-30%;height:auto;');
				$('#body-popup').show();

				
				var HTML = '';
				HTML += '<h3 class="titrePopUp">créer un lien<span id="fermerPopUp">X</span></h3>';
				HTML += '<div class = "conteneur-form-insert-img-ckeditor">';
				//HTML += '<div class="titre-section-page-standard"><span>créer un lien</span></div>';
				HTML += '<form class = "formAjoutLienPopUp" name = "ajouterLien" method = "POST" action = "">';
				
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form">url</span>';
				HTML += '<input class = "input-form-text" type = "text" name = "url" value = "http://" style = "padding-left:10px!important;width:80%;">';
				HTML += '<span class="delete-input"></span>';
				HTML += '<p style = "clear: both;width: 80%;float: right;margin-right: 33px;">le lien s\'ouvrira dans un nouvel onglet du navigateur</p>';
				HTML += '</label>';
				
				HTML += '<label class="champForm" style = "clear:both;">';
				HTML += '<span>ou</span>';
				HTML += '<hr style = "display: inline-block;height: 2px;background: #efefef;width: 92%;vertical-align: middle;text-align: right;margin-left: 1%;clear: right;">';
				HTML += '</label>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form">mailto</span>';
				HTML += '<input class = "input-form-text" type = "text" name = "mailto" style = "padding-left:10px!important;width:80%;" placeholder = "service_client@cloudrewards.com">';
				HTML += '<span class="delete-input"></span>';
				HTML += '</label>';
				
				HTML += '<div class = "clearBoth"></div>';
				HTML += '<div class = "clearBoth"></div>';
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form">texte à afficher</span>';
				HTML += '<input class = "input-form-text" type = "text" name = "texte_afficher" style = "padding-left:10px!important;width:80%;" placeholder = "service_client@cloudrewards.com">';
				HTML += '<span class="delete-input"></span>';
				HTML += '</label>';
				
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<button id = "submit-form-ajout-url" class="btn-valider valider submit-form block-center" type = "button">';
				HTML += 'valider';
				HTML += '</button>';
				HTML += '</label>';
				
				HTML += '</form>';
				HTML += '</div>';
				

				$('#body-popup').html(HTML);
			}
		});
		
		//Creating the Toolbar Button
		editor.ui.addButton( 'Insertionlienscloudrewards', {
			label: 'Inserer un lien',
			command: 'openPopUpLink',
			toolbar: 'insert'
		});
    }
});

$(document).ready(function(){
	$(document).on('click', 'span.delete-input', function(){
		$(this).prev('input[type="text"]').val('');
	});
	
	$(document).on('click', 'button#submit-form-ajout-url', function(){
		var UrlVal = $('input#url-valeur-popup').val();
		var UrlType = $('select#url-type-popup').val();
		
		if($.trim(UrlVal) != '' && $.trim(UrlType) != ''){
			var UrlTxt = UrlType+''+UrlVal;
			var IdCk = $('li.page-list-active').attr('id');
			var ArrayIdCk = new Array;
			ArrayIdCk = IdCk.split('-');
			var Id = ArrayIdCk[3];
			
			var link_html = '<a href = "'+UrlTxt+'">'+UrlTxt+'</a>';
			var editorName = 'login_portal_data_form_text_'+Id+'';
			var editor = CKEDITOR.instances[editorName];
			editor.insertHtml(link_html);
			$('span#fermerPopUp').click();
		}
	});
});