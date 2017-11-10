$(document).ready(function(){
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
        var field_list = $('.reorder-table').find('tbody').find('tr.form-field-row');
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

        /*var custom_field_allowed = $("input[name=custom-field-allowed]").val();
        if($('.add-field-form-block').find('.add-field-form-container').find('.add-field-form').length < custom_field_allowed)
        {
            var new_add_field_form = $(this).parents('.add-field-form-block').find('.add-field-form.template').clone();
            new_add_field_form.removeClass('template');
            $(this).parents('.add-field-form-block').find('.add-field-form-container').append(new_add_field_form);
            new_add_field_form.show();
        }
        else
        {
            alert(custom_field_allowed+' nouveau(x) champ(s) maximum');
        }*/
    });

    // Ajout de nouvelle option (pour champ à choix, exp bouton radio)
    $('.add-field-form-block').on('click', '.add-option-link', function(e){
        e.preventDefault();
        var new_option_field = $(this).parents('.select-field-option-container').find('.add-option-field.template').clone();
        new_option_field.removeClass('template');
        $(this).parents('.select-field-option-container').find('.option-container').append(new_option_field);
        new_option_field.show();
    });

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
    // reord - monter
    $('.reorder-up-field-row-link').on('click', function(e){
        e.preventDefault();
        var upper_row = $(this).parents('.form-field-row').prev('.form-field-row');
        if(upper_row.length > 0)
        {
            upper_row.before($(this).parents('.form-field-row'));
        }
    });

    // reord - descendre
    $('.reorder-down-field-row-link').on('click', function(e){
        e.preventDefault();
        var lower_row = $(this).parents('.form-field-row').next('.form-field-row');
        if(lower_row.length > 0)
        {
            lower_row.after($(this).parents('.form-field-row'));
        }
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
     * Suppression champ existant
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

    $('.delete-field-row-link').on('click', function(e){
        e.preventDefault();
        var form_field_row = $(this).parents('.form-field-row');
        var field_id = form_field_row.attr('data-field-id');
        var delete_field_action_input = $('#form_structure_delete-field-action-list');
        var current_delete_field_action_list = delete_field_action_input.val();
        var new_delete_field_action_list = '';
        if("" == current_delete_field_action_list.trim() || "undefined" == typeof current_delete_field_action_list)
        {
            new_delete_field_action_list = field_id;
        }
        else
        {
            new_delete_field_action_list = current_delete_field_action_list + ',' + field_id;
        }
        delete_field_action_input.val(new_delete_field_action_list);
        $(this).parents('.form-field-row').remove();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Suppression nouveau champ
     * Suppression option
     * Suppression champ existant
     * *********************************************************************************************
     */

    

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Image Header
     * *********************************************************************************************
     */
    //  preview d'image après choix d'image
    function createImagePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.header-image-preview-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


    $('.header-image-input').on('change', function(){
        createImagePreview(this);
        var image_file_name = $(this).val().split('\\').pop();
        $('.upload-img-button').find('i').css('margin-right', '10px');
        $('.upload-img-button').find('.img-name-container').text(image_file_name);
    });

    // preview du message
    $('.header-message-input').on('input', function(){
        $('.header-message-preview').text($(this).val());
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Image Header
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Section active
     * *********************************************************************************************
     */
    $('.fieldset').on('click', function(e){
        $('.fieldset').removeClass('active');
        $(this).addClass('active');

        $('.cke_wysiwyg_frame').contents().find('body').css(
            'background-color', '#F5F5F5'
        )
        $(this).find('iframe.cke_wysiwyg_frame').contents().find('body').css(
            'background-color', '#FFFFFF'
        );
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Section active
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Edition champ
     * *********************************************************************************************
     */
    $(document).on('click', '.edit-field-row-link', function(e){
        e.preventDefault();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Edition champ
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Upload image header
     * *********************************************************************************************
     */
    $('.upload-img-button').on('click', function(e){
        e.preventDefault();
        $('.header-image-input').trigger('click');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Upload image header
     * *********************************************************************************************
     */

});