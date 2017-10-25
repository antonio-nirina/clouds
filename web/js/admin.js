$(document).ready(function(){
	//Gestions choix récompenses
	$(document).on('click', '.radioBtn', function(){
		$('.radioBtn').each(function(i){
			$(this).addClass('unchecked');
			$(this).removeClass('checked');
			
			$(this).parent().parent().removeClass("active");
			$(this).parent().parent().addClass("inactive");
		});
		
		$(this).addClass('checked');
		
		$(this).parent().parent().removeClass("inactive");
		$(this).parent().parent().addClass("active");
	});
	
	//Gestions des choix mode multi-opérations
	$(document).on('click', '.checkboxBtn', function(){
		/*
		$('.checkboxBtn').each(function(i){
			$(this).addClass('no-mode');
			$(this).removeClass('est-mode');
		});
		*/
		
		if($(this).hasClass('est-mode')){
			$(this).addClass('no-mode');
			$(this).removeClass('est-mode');
		}else{
			$(this).addClass('est-mode');
			$(this).removeClass('no-mode');
		}
	});

	// Récupération des paramètres de structure de formulaire
	// Et définition des valeurs des hidden du formulaire à la validation
	$('#form-structure-submit').on('click', function(e){
		var form_field_row_list = $('.form-field-row');
		var form_structure_current_field = [];
		form_field_row_list.each(function(){
			var published_state = $(this).find('.form-field-published').is(':checked') ? true : false;
			var mandatory_state = $(this).find('.form-field-mandatory').is(':checked') ? true : false;
			var form_structure_el = {
				"id": $(this).attr('data-field-id'),
				"published": published_state,
				"mandatory": mandatory_state
			};
            form_structure_current_field.push(form_structure_el);
		});
		$('#form_structure_current-field-list').val(JSON.stringify(form_structure_current_field));
	})
});

