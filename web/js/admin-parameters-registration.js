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
    var admin_new_registration_form_field_url = $('input[name=admin-new-registration-form-field]').val();
    $('.add-field-link').on('click', function(e){
        e.preventDefault();
        var custom_field_allowed = $("input[name=custom-field-allowed]").val();

        custom_field_allowed = parseInt(custom_field_allowed);
        if(custom_field_allowed >0) {
            $.ajax({
                type: 'GET',
                url:admin_new_registration_form_field_url,
                success: function(html){
                    $('.modal-content .content').html(html);
                    $('#btn-modal').trigger('click');
                }
            });
        }
        else
        {
            alert(custom_field_allowed+' nouveau(x) champ(s) maximum');
        }
    });

    $(document).on('click', '.btn-valider.add-field', function(e){
        e.preventDefault();
        var type = $("input[name=type_field]:checked").val();
        var label = $("input[name=intitule]").val();

        if(label.trim() == "")
        {
            alert("ajouter un intitulé");
        }
        else if("undefined" == typeof type)
        {
            alert("choisir un type");
        }
        else
        {
            var data = {"label": label, 'field_type': type, 'validate': true};
            $.ajax({
                type: 'POST',
                data: data,
                url: admin_new_registration_form_field_url,
                success: function(html){
                    var block_table = $('.block-table');
                    block_table.find('tbody').append(html);
                    var custom_field_allowed = $('input[name=custom-field-allowed]');
                    custom_field_allowed.val(parseInt(custom_field_allowed.val()) - 1);
                    $('.custom-field-allowed-container').text(custom_field_allowed.val());

                    if(parseInt(custom_field_allowed.val()) <= 0)
                    {
                        $('.add-field-link-container').hide();
                    }
                    $('.close-modal').trigger('click');
                }
            });
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
     * Gestion édition champ
     * *********************************************************************************************
     */
    function retrieveOptionList(row_element){
        var option_list = [];
        var option_container = row_element.find('.block-label').find('.champs-form-radio').find('.input-libelle-radio');
        option_container.each(function(){
            option_list.push($(this).text());
        });
        return option_list;
    }

    var admin_edit_registration_form_field_url = $('input[name=admin-edit-registration-form-field]').val();
    $(document).on('click', '.edit-field-row-link', function(e){
        e.preventDefault();
        var form_field_row = $(this).parents('.form-field-row');
        var field_id = form_field_row.attr('data-field-id');
        var data = {"field_id": field_id};
        $.ajax({
            type: "GET",
            data: data,
            url: admin_edit_registration_form_field_url,
            success: function(html){
                $('.modal-content .content').html(html);
                $('#btn-modal').trigger('click');
            }
        });
    });

    $(document).on('click', '.btn-valider.edit-field', function(e){
        e.preventDefault();
        var type = $("input[name=type_field]:checked").val();
        var label = $("input[name=intitule]").val();
        var field_id = $("input[name=field_id]").val();
        if(label.trim() == "")
        {
            alert("ajouter un intitulé");
        }
        else if("undefined" == typeof type)
        {
            alert("choisir un type");
        }
        else
        {
            var data = {"label": label, 'field_type': type, 'field_id': field_id, 'validate': true};
            if("choice-radio" == type)
            {
                var choice_radio_row = $('.row.choice-radio-row');
                var option_list = retrieveOptionList(choice_radio_row);
                data["options"] = option_list;
            }
            $.ajax({
                type: 'POST',
                data: data,
                url: admin_edit_registration_form_field_url,
                success: function(html){
                    var current_field_row = $('.form-field-row[data-field-id='+field_id+']');
                    if("undefined" != typeof current_field_row)
                    {
                        current_field_row.before(html);
                        current_field_row.remove();
                    }
                    $('.close-modal').trigger('click');
                }
            });
        }
    });



    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Gestion édition champ
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Réordonnancement de champs
     * *********************************************************************************************
     */
    // reord - monter
    $(document).on('click', '.reorder-up-field-row-link', function(e){
        e.preventDefault();
        var upper_row = $(this).parents('.form-field-row').prev('.form-field-row');
        if(upper_row.length > 0)
        {
            upper_row.before($(this).parents('.form-field-row'));
        }
    });

    // reord - descendre
    $(document).on('click', '.reorder-down-field-row-link', function(e){
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
    /*$('.add-field-form-block').on('click', '.remove-field-form-link', function(e){
        e.preventDefault();
        $(this).parents('.add-field-form').remove();
    });*/

    // Supprimer option créée
    /*$('.add-field-form-block').on('click', '.remove-option-field-link', function(e){
        e.preventDefault();
        $(this).parents('.add-option-field').remove();
    });*/

    /*$('.delete-field-row-link').on('click', function(e){
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
    });*/

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
        $('.upload-img-button').css('background-position', '15px');
        $('.upload-img-button').find('.img-name-container').css('margin-left', '30px');
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
    $('.fieldset').on('mouseenter', function(e){
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

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Checkbox publier et obligatoire
     * *********************************************************************************************
     */
    $(document).on("click", ".form-field-published", function(){
        if(!$(this).is(":checked"))
        {
            $(this).parents('.block-table').find('.checkbox-publish-all').prop("checked", false);
        }
    });

    $(document).on("click", ".form-field-mandatory", function(){
        if(!$(this).is(":checked"))
        {
            $(this).parents('.block-table').find('.checkbox-mandatory-all').prop("checked", false);
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Checkbox publier et obligatoire
     * *********************************************************************************************
     */

});