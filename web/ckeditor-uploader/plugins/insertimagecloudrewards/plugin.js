CKEDITOR.plugins.add( 'insertimagecloudrewards', {
    icons: 'insertimagecloudrewards',
    init: function( editor ) {
        //Creating an Editor Command
		editor.addCommand( 'openPopUp', {
			exec: function( editor ) {
				/*
				var now = new Date();
				editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
				*/
				
				//Ouvre le popUp
				$('#conteneur-popup').show();
				$('#body-popup').removeAttr('style');
				$('#body-popup').show();
				$('#body-popup').html('');
				
				var Chargements = $('p.chargementAjax').clone();
				$('#body-popup').html(Chargements);
				$('#body-popup').find('p.chargementAjax').show();
				
				var UrlPopImgEditor = $('input#url_popup_insert_img_editor').val();
				setTimeout(function(){
					$.ajax({
						type: 'POST',
						url: UrlPopImgEditor,
						data:'',
						success: function(retour){
							$('#body-popup').html(retour);
							$('#body-popup').removeAttr('style');
							$('#body-popup').attr('style', 'display:block;height:auto;');
						}
					});
				}, 2000);
			}
		});
		
		//Creating the Toolbar Button
		editor.ui.addButton( 'Insertimagecloudrewards', {
			label: 'Inserer une image',
			command: 'openPopUp',
			toolbar: 'insert'
		});
    }
});