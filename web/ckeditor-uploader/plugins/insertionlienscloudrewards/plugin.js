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
				//$('#body-popup').attr('style', 'width:60%;margin-left:-30%;height:auto;');
				$('#body-popup').show();

				
				var HTML = '';
				HTML += '<h3 class="titrePopUp"><span id="fermerPopUp" class = "close-modal">X</span></h3>';
				HTML += '<div class = "conteneur-form-insert-img-ckeditor">';
				HTML += '<div class="titre-section-page-standard"><h4 style = "color: #2A6BD9;">créer un lien</h4></div>';
				HTML += '<form class = "formAjoutLienPopUp" name = "ajouterLien" method = "POST" action = "" style = "width:90%;">';
				
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form" style = "width:20%;display:inline-block;">url</span>';
				HTML += '<input id = "url-val" class = "input-form-text popuplien" type = "text" name = "url" value = "" style = "padding-left:10px!important;width:70%;margin-right:34px;!important;" placeholder = "mettez votre url ici. ex: http://www.exemple.com">';
				HTML += '<span class="delete-input" style = "top:0!important;"></span>';
				HTML += '<p style = "clear: both;width: 70%;float: right;margin-right: 34px;">le lien s\'ouvrira dans un nouvel onglet du navigateur</p>';
				HTML += '</label>';
				
				HTML += '<label class="champForm" style = "clear:both;">';
				HTML += '<span>ou</span>';
				HTML += '<hr style = "display: inline-block;height: 1px;background: #efefef;width: 90%;vertical-align: middle;text-align: right;margin-left: 1%;clear: right;">';
				HTML += '</label>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form" style = "width:20%;display:inline-block;">mailto</span>';
				HTML += '<input id = "mail-val" class = "input-form-text popuplien" type = "text" name = "mailto" style = "padding-left:10px!important;width:70%;margin-right:34px;!important;" placeholder = "service_client@cloudrewards.com">';
				HTML += '<span class="delete-input" style = "top:0!important;"></span>';
				HTML += '</label>';
				
				HTML += '<div class = "clearBoth"></div>';
				HTML += '<div class = "clearBoth"></div>';
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<span class = "lib-form" style = "width:20%;display:inline-block;">texte à afficher</span>';
				HTML += '<input id = "text-val" class = "input-form-text popuplien" type = "text" name = "texte_afficher" style = "padding-left:10px!important;width:70%;margin-right:34px;!important;" placeholder = "service_client@cloudrewards.com">';
				HTML += '<span class="delete-input" style = "top:0!important;"></span>';
				HTML += '</label>';
				
				HTML += '<div class = "clearBoth"></div>';
				
				HTML += '<label class="champForm">';
				HTML += '<button id = "submit-form-ajout-url" class="btn-valider valider submit-form block-center" type = "button">';
				HTML += 'valider';
				HTML += '</button>';
				HTML += '</label>';
				
				HTML += '<div id = "msg-erreur">';
				HTML += '<ul>';
				HTML += '<li>Le champ "texte à afficher" est obligatoire</li>';
				HTML += '<li>L\' une des 2 champs "url" et "mailto" doit être renseigner</li>';
				HTML += '</ul>';
				HTML += '</div>';
				
				HTML += '<input id = "id_editor_name" type = "hidden" name = "editor_name" value = "'+editor.name+'">';
				
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
		var urlval = $('input#url-val').val();
		var mailval = $('input#mail-val').val();
		var textval = $('input#text-val').val();
		
		var editorName = $('input#id_editor_name').val();
		var editor = CKEDITOR.instances[editorName];
		
		if($.trim(textval) != ''){
			if($.trim(urlval) != '' && $.trim(mailval) != ''){
				$('div#msg-erreur ul').show();
			}else if($.trim(urlval) != ''){
				
				var Href = '';
				if(urlval.indexOf('http://') == '-1'){
					Href += '<a target = "_blank" href = "http://'+urlval+'">'+textval+'</a>';
				}else{
					Href += '<a target = "_blank" href = "'+urlval+'">'+textval+'</a>';
				}
				
				var Span = editor.document.createElement('span');
				Span.setHtml(Href);
				editor.insertElement(Span);
				
				$('span#fermerPopUp').click();
			}else if($.trim(mailval) != ''){
				var Href = '';
				if(mailval.indexOf('mailto:') == '-1'){
					Href += '<a href = "mailto:'+mailval+'">'+textval+'</a>';
				}else{
					Href += '<a href = "'+mailval+'">'+textval+'</a>';
				}
				
				var Span = editor.document.createElement('span');
				Span.setHtml(Href);
				editor.insertElement(Span);
				
				$('span#fermerPopUp').click();
			}else{
				$('div#msg-erreur ul').show();
			}
		}else{
			$('div#msg-erreur ul').show();
		}
	});
});