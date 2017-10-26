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
    /**
	 * *********************************************************************************************
	 * Paramétrages - Inscriptions
	 * Validation de création de formulaire
	 * *********************************************************************************************
     */
	// Récupération des paramètres de structure de formulaire
	// Récupération des données des nouveaux champs
    // Récupération de l'ordonnancement des champs
	// Et définition des valeurs des hidden du formulaire à la validation
    var field_type_with_choice = ['choice-radio'];
    $('#form-structure-submit').on('click', function(e){
        // Statut des champs : A publier ET obligatoire
        var form_field_row_list = $('.form-field-row');
        var form_structure_current_field = [];
        form_field_row_list.each(function(){
            var published_state = $(this).find('.form-field-published').is(":checked") ? true : false;
            var mandatory_state = $(this).find('.form-field-mandatory').is(":checked") ? true : false;
            var form_structure_el = {
                "id": $(this).attr('data-field-id'),
                "published": published_state,
                "mandatory": mandatory_state
            };
            form_structure_current_field.push(form_structure_el);
        });
        $('#form_structure_current-field-list').val(JSON.stringify(form_structure_current_field));

        // Données des nouveaux champs
        var new_field_datas_list = [];
        var empty_label_state = false;
        if($('.add-field-form-block').find('.add-field-form').length > 0)
        {
            var add_field_form_list = $('.add-field-form-block').find('.add-field-form-container').find('.add-field-form');
            add_field_form_list.each(function(){
                var new_field_label = $(this).find('.input-field-label');
                if('' != new_field_label.val().trim())
                {
                    var new_field_datas_el = {
                        "label": $(this).find('.input-field-label').val(),
                        "mandatory": $(this).find('.checkbox-mandatory').is(":checked") ? true : false,
                        "field_type": $(this).find('.select-field-type').val()
                    };
                    if(field_type_with_choice.indexOf($(this).find('.select-field-type').val()) >= 0)
                    {
                        if(
                            $(this).find('.select-field-type-container')
                                .find('.select-field-option-container')
                                .find('.option-container')
                                .find('.add-option-field')
                                .length > 0
                        ) {
                            var option_list = $(this).find('.select-field-type-container')
                                .find('.select-field-option-container')
                                .find('.option-container')
                                .find('.add-option-field')
                                .find('.input-option');
                            var option_value = {};
                            option_list.each(function(){
                                if('' != $(this).val().trim())
                                {
                                    option_value[$(this).val()] = $(this).val();
                                }
                            });
                            new_field_datas_el["choices"] = option_value;
                        }
                    }

                    new_field_datas_list.push(new_field_datas_el);
                }
                else
                {
                    empty_label_state = true;
                }
            });
        }

        $('#form_structure_new-field-list').val(JSON.stringify(new_field_datas_list));

        // Ordonnancement des champs
        var field_list = $('.sortable-table').find('tbody').find('tr.form-field-row');
        var field_list_order = [];
        if(field_list.length > 0)
        {
            field_list.each(function(){
                field_list_order.push($(this).attr('data-field-id'));
            });
        }
        $('#form_structure_field-order').val(JSON.stringify(field_list_order));

        // Message
        if(true == empty_label_state)
        {
            var message = "Un ou plusieurs nouveaux champs n'ont pas d'intitulé défini. Ceux-ci ne sont pas considérés."
            var message_resp = confirm(message);
            if(false == message_resp) {
                e.preventDefault();
            }
        }
    })
    /**
     * *********************************************************************************************
     * FIN
	 * Paramétrages - Inscriptions
     * Validation de création de formulaire
     * *********************************************************************************************
     */

    /**
	 * *********************************************************************************************
	 * Paramétrages - Inscriptions
	 * Gestion d'ajout de nouveau champ
	 * *********************************************************************************************
     */
	// Ajout nouveau formulaire d'ajout de champ
	$('.add-field-link').on('click', function(e){
        e.preventDefault();
        if($('.add-field-form-block').find('.add-field-form-container').find('.add-field-form').length < 5)
		{
            var new_add_field_form = $(this).parents('.add-field-form-block').find('.add-field-form.template').clone();
            new_add_field_form.removeClass('template');
            $(this).parents('.add-field-form-block').find('.add-field-form-container').append(new_add_field_form);
            new_add_field_form.show();
		}
		else
		{
			alert('5 nouveaux champs maximum');
		}
	});

	// Ajout de nouvelle option (pour champ à choix, exp bouton radio)
	$('.add-field-form-block').on('click', '.add-option-link', function(e){
		e.preventDefault();
		var new_option_field = $(this).parents('.select-field-option-container').find('.add-option-field.template').clone();
		new_option_field.removeClass('template');
		$(this).parents('.select-field-option-container').find('.option-container').append(new_option_field);
        new_option_field.show();
	});

	// Initialisation d'un formualaire d'ajout de champ
    $('.add-field-link').trigger('click');

    // Afficher/Cacher le bloc d'option en fonction du type de champ à ajouter choisi
    $('.add-field-form-block').on('change', '.select-field-type', function(e){
        e.preventDefault();
        if(field_type_with_choice.indexOf($(this).val()) >= 0)
		{
			$(this).parents('.add-field-form').find('.select-field-option-container').show();
		}
		else
		{
            $(this).parents('.add-field-form').find('.select-field-option-container').hide();
		}
	});

    /**
     * *********************************************************************************************
	 * FIN
     * Paramétrages - Inscriptions
     * Gestion d'ajout de nouveau champ
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Réordonnancement de champs
     * *********************************************************************************************
     */
    $('.sortable-table').sortable({
        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>'
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Réordonnancement de champs
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Suppression nouveau champ
     * Suppression option
     * *********************************************************************************************
     */
    // Supprimer nouveau champ
    $('.add-field-form-block').on('click', '.remove-field-form-link', function(e){
        e.preventDefault();
        $(this).parents('.add-field-form').remove();
    });

    // Supprimer option créée
    $('.add-field-form-block').on('click', '.remove-option-field-link', function(e){
        e.preventDefault();
        $(this).parents('.add-option-field').remove();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Suppression nouveau champ
     * Suppression option
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Selection multiple : champs à publier, rendre obligatoire
     * *********************************************************************************************
     */
    $('.checkbox-publish-all').on('click', function(e){
        if($(this).is(':checked'))
        {
            $('.form-field-published').prop("checked", true);
        }
        else
        {
            $('.form-field-published').prop("checked", false);
        }
    });

    $('.checkbox-mandatory-all').on('click', function(e){
        if($(this).is(':checked'))
        {
            $('.form-field-mandatory').prop("checked", true);
        }
        else
        {
            $('.form-field-mandatory').prop("checked", false);
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Selection multiple : champs à publier, rendre obligatoire
     * *********************************************************************************************
     */

});

