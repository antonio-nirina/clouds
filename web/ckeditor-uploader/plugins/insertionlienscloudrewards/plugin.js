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
				$('#body-popup').attr('style', 'width:40%;margin-left:-20%;');
				$('#body-popup').show();

				
				var HTML = '';
				HTML += '<h3 class="titrePopUp"><span id="fermerPopUp">X</span></h3>';
				HTML += '<div class = "conteneur-form-insert-img-ckeditor">';
				HTML += '<div class="titre-section-page-standard"><span>Ajouté un lien</span></div>';
				HTML += '<form class = "formAjoutLienPopUp" name = "ajouterLien" method = "POST" action = "">';
				HTML += '<label class="champForm">';
				HTML += '<select id = "url-type-popup" class = "select-form" name = "typeUrl">';
				HTML += '<option value = "">Url</option>';
				HTML += '<option value = "http://">http://</option>';
				HTML += '<option value = "https://">https://</option>';
				HTML += '<option value = "mailto:">mailto:</option>';
				HTML += '</select>';
				HTML += '<input id = "url-valeur-popup" style="padding-left:10px!important;" class="input-form-text-pop" name="url" value="" placeholder="" type="text">';
				HTML += '<span class="delete-input"></span>';
				HTML += '</label>';
				
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