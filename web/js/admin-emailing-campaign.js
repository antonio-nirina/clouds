$(document).ready(function() {
	$('#preview-template-dialog').modal('hide');

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Selection multiple dans liste campagne
     * *********************************************************************************************
     */
    $(document).on('change', ".campagne-name .styled-checkbox", function() {//selection des campagnes
        checked = getChecked();
        text = (checked.length == 1)?checked.length+" campagne sélectionnée":((checked.length > 1)?checked.length+" campagnes sélectionnées":"");
        $(".selected-count input").val(text);

        if (checked.length > 0) {
            $('.filter.selected-campaign').css('display',"flex");
            $('.selected-count .delete-input').css('display','block');
        } else {
            $('.filter.selected-campaign').css('display',"none");
        }
    });

    $('.selected-count .delete-input').on("click", function () {//désélection des campaignes
        var checked = getChecked();
        for (i in checked) {
            $("#"+checked[i]).click();
        }
        $('.filter.selected-campaign').css('display',"none");
    });

    $(document).on('mouseleave', '.dropdown-menu', function() {// sortir des dropdown, en général
        $(document).click();
    });

    $(document).on('keyup', '.form-line input', function() {
        if ($(this).val()) {
            $(this).next('span').css('display', 'inline-block');
        } else {
            $(this).next('span').css('display', 'none');
        }
    })


    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Selection multiple dans liste campagne
     * *********************************************************************************************
     */

	/**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Filtres
     * *********************************************************************************************
     */
    $(document).on('click','.filtres.dropdown .delete-input', function(){//annuler filtre
        $(this).off('click');
        $(this).parent().find('button').html($(this).parent().find('button').removeClass('active').attr('data-default'));
        $(this).css({'visibility':'hidden','display':'inline-block'});
        setTimeout(sendFilter($(this)), 0);
    });

    $(document).on('click','.filtres.clearable .dropdown-item', function(e){//activer filtre
        e.preventDefault();
        $(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        $(this).parents('.dropdown').find('.delete-input').css({'visibility':'visible','display':'inline-block'});
        setTimeout(sendFilter($(this)), 0);
        resetCampaignCountBlock();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Filtres
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Création campagne
     * *********************************************************************************************
     */
    $(document).on('click', "#new-campaign-modal .previous", function() {//affichage de l'onglet precedent
        if ($(this).hasClass('keep') && $('#create-tabs a.activated.active').hasClass("step-3")) {
            $(this).removeClass('keep');
            $('#new-campaign-modal').find('.modal-step-3.first').css('display','block');
            $('#new-campaign-modal').find('.modal-step-3.second').css('display','none');
        } else {
            if ($('#create-tabs a.activated.active').hasClass("done")) {
                $('#create-tabs a.activated.active').removeClass("done");
            }
            $('#create-tabs a.activated.active').removeClass("activated").prev('a').click();
        }
        showPrevious();
    });

    //boutton de programmation
    $(document).on("change", "input.programmed-state-input", function() {
        if ($(this).val() =="false") {
            $(".btn-end-step-4").css("display", "initial");
            $(".btn-program-step-4").css("display", "none");
            $(".select-date").css("display", "none");
        } else {
            $(".btn-end-step-4").css("display", "none");
            $(".btn-program-step-4").css("display", "initial");
            $(".select-date").css("display", "block");
        }
    });

    $(document).on('click', '.btn-end-step', function(e) { // terminer une étape pour aller à l'autre
        e.preventDefault();
        if (isDone()) {
            clickNext();
        }
    });



    //envoie ou programmation de campagne
    $(document).on("click", ".btn-end-step.btn-end-step-4", function() {
        $('.chargementAjax').removeClass('hidden');
        var new_campaign_url = $('input[name=new_campaign_url]').val();
        var current_end_step_button = $(this);
        $('#new-campaign-modal form').ajaxSubmit({
            type: 'POST',
            url: new_campaign_url,
            success: function(data){
                if(data['error']){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text(data['error']);
                    $('#new-campaign-modal').find('.content').html(data['content']);
                    $('#create-tabs a.activated:last').tab('show');
                    showPrevious();
                    initCalendar();
                    initSelectChosen();
                    setSelectedContactList();
                    $('#new-campaign-modal').modal('show');
                } else {
                    var launch_date =  $("#new-campaign-modal").find('.date_launch_campaign').val();
                    $("#new-campaign-modal").modal("hide");
                    if('send' == current_end_step_button.attr('data-button-type')){
                        setTimeout(function(data){
                            $("#sent-campaign-modal").modal("show");
                        },0);
                    } else if ('program' == current_end_step_button.attr('data-button-type')){
                        setTimeout(function(){
                            setLaunchDateInModal(launch_date);
                            $("#done-campaign-modal").modal("show");
                        },0);
                    }
                }
            },
            statusCode:{
                404: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#new-campaign-modal').find('.content').html('');
                },
                500: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#new-campaign-modal').find('.content').html('');
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    $('#new-campaign-modal').on('hidden.bs.modal', function(){
        $('#new-campaign-modal').find('.error-message-container.general-message').text('');
        $('#new-campaign-modal').find('content').html('');
    });

    /*$(document).on("click", ".btn-end-step.btn-program-step-4", function() {//envoie campagne programmée
        $("#new-campaign-modal").modal("hide");
        $("span.date-envoi").html($('.date_launch_campaign').val());
        setTimeout(function(){
            $("#done-campaign-modal").modal("show");
        },0);
    });*/

    // création de nouvelle campagne
    $('.create-campaign-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_campaign_url = $("input[name=new_campaign_url").val();
        $.ajax({
            type: 'POST',
            url: create_campaign_url,
            success: function(data){
                $('#new-campaign-modal').find('.error-message-container.general-message').text('');
                $('#new-campaign-modal').find(".modal-content .content").html(data.content);
                activateCampaignConfirmationOnClose();
                $('#create-tabs a.activated:last').tab('show');
                showPrevious();
                initCalendar();
                initSelectChosen();
            },
            statusCode: {
                404: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#new-campaign-modal').find('.previous').hide();
                    deactivateCampaignConfirmationOnClose();
                },
                500: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#new-campaign-modal').find('.previous').hide();
                    deactivateCampaignConfirmationOnClose()
                }
            },
            complete: function(){
                $('#new-campaign-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    $('#new-campaign-modal').on('hidden.bs.modal', function(){
        $('#new-campaign-modal').find('.error-message-container.general-message').text('');
        $('#new-campaign-modal').find(".modal-content .content").html('');
        $('#new-campaign-modal').find('.previous').show();
    });

    // selection liste de diffusion
    $(document).on('click', '.list-choice .dropdown-menu .dropdown-item', function(e){
        e.preventDefault();
        var data_value = $(this).attr('data-value');
        $(this).parents('.list-choice').find('.list-choice-select').find('option[value='+data_value+']').prop('selected', true);
        $(this).parents('.dropdown').find('button.dropdown-toggle').text($(this).text());
        $(this).parents('.dropdown').find('.delete-input').css({'visibility':'visible','display':'inline-block'});
    });

    // rechargement de la liste de campagne après fermeture du modal de confirmation d'envoi de campagne
    $('#sent-campaign-modal').on('hidden.bs.modal', function(){
        sendFilter();
    });

    // rechargement de la liste de campagne après fermeture du modal de confirmation de programmation de campagne
    $('#done-campaign-modal').on('hidden.bs.modal', function(){
        sendFilter();
    });

    // annulation selection de liste de diffusion
    const DEFAUTL_LIST_CHOICE_LABEL = 'CHOISIR UNE LISTE';
    $(document).on('click', '.list-choice .delete-input.delete-selection', function(){
        $(this).parents('.dropdown').find('button.dropdown-toggle').text(DEFAUTL_LIST_CHOICE_LABEL);
        $(this).parents('.list-choice').find('select option[value=""]').prop('selected', true);
    });


    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Création campagne
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Duplidation de campagne
     * *********************************************************************************************
     */
    // calling duplicate form
    $(document).on('click', '.campaign-duplicate', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var duplicate_campaign_url = $('input[name=duplicate_campaign_url]').val(),
        data = {};
        data.campaign_draft_id = $(this).attr('data-campaign-draft-id');
        $.ajax({
            type: 'POST',
            url: duplicate_campaign_url,
            data: data,
            success: function(data){
                $('#duplicate-campaign-dialog').find('.modal-body-container').html(data.content);
                $('#duplicate-campaign-dialog').find('.general-message').html('');
            },
            statusCode: {
                404: function(){
                    $('#duplicate-campaign-dialog').find('.general-message').html('Page non trouvée');
                },
                500: function(){
                    $('#duplicate-campaign-dialog').find('.general-message').html('Erreur interne');
                }
            },
            complete: function(){
                $('#duplicate-campaign-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    $('#duplicate-campaign-dialog').on('hidden.bs.modal', function(){
        $(this).find('.general-message').html('');
    });

    // close duplicate form
    $(document).on('click', '#duplicate-campaign-dialog .button-container .cancel-duplication', function(e){
        e.preventDefault();
        $('#duplicate-campaign-dialog').find('.modal-body-container').html('');
        $('#duplicate-campaign-dialog').modal('hide');
    });

    // submit duplicate form
    $(document).on('click', '#duplicate-campaign-dialog .button-container .save-duplication', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var duplicate_campaign_url = $('input[name=duplicate_campaign_url]').val(),
            data = {};
        data.campaign_draft_id = $('#duplicate-campaign-dialog').find('.duplication-source-id').val();
        $('#duplicate-campaign-dialog form').ajaxSubmit({
            type: 'POST',
            url: duplicate_campaign_url,
            data: data,
            success: function(data){
                window.location.replace($('input[name=campaign_list_url]').val());
            },
            statusCode: {
                404: function(){
                    $('#duplicate-campaign-dialog').find('.general-message').html('Page non trouvée');
                },
                500: function(){
                    $('#duplicate-campaign-dialog').find('.general-message').html('Erreur interne');
                }
            },
            complete: function(){
                $('#duplicate-campaign-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Duplidation de campagne
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Suppression de campagne
     * *********************************************************************************************
     */
    $('.btn-valider.btn-delete-campaign').on("click", function() { //suppression campagenes
        checked = getChecked();
        var data = {'ids' : checked.join(',')};
        var url = $("input[name=delete-campaign-link]").val();
        $.ajax({
            type: "POST",
            data: data,
            url: url,
            success: function() {
            }
        });
        setTimeout(sendFilter(), 250);
        $(".modal").modal('hide');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Suppression de campagne
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Création de nouveau modèle, dans création campagne
     * *********************************************************************************************
     */
    //création nouveau template
    $(document).on('click', '#new-campaign-modal .create-new-template-link', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var template_model = $('input[name=template_model_text_and_image]').val();

        if(null !== template_model){
            var add_template_url = $('input[name=add_template_form_url]').val();
            add_template_url = add_template_url+'/'+template_model;

            $.ajax({
                type: 'GET',
                url: add_template_url,
                success: function(data){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                },
                statusCode: {
                    404: function(data){
                        var message = 'undefined' === typeof data.responseJSON ? 'Page non trouvée' : data.responseJSON.message;
                        $('#create-template-dialog').find('.error-message-container.general-message').text(message);
                        $('#create-template-dialog').find('.modal-body-container').html('');
                        setTimeout(function(){
                            $('#create-template-dialog').find('a.previous').show();
                            $('#create-template-dialog').modal('show');
                        }, 0);
                    },
                    500: function(){
                        $('#preview-template-dialog').find('.error-message-container.general-message').text('Erreur interne');
                        $('#preview-template-dialog').find('.modal-body-container').html('');
                    }
                },
                complete: function(){
                    $('#create-template-dialog').find('.previous').hide();
                    $('#create-template-dialog').find('.close-modal').attr('data-dismiss', 'modal');
                    $('#create-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }
    });

    $('#create-template-dialog').on('shown.bs.modal', function(){
        // installer color picker
        installColorPicker();

        // installer wysiwyg
        installWysiwyg();
    });

    $('#create-template-dialog').on('hidden.bs.modal', function(e){
        $(this).find('.error-message-container.general-message').text('');
        $(this).find('.modal-body-container').html('');
    });

    // enregistrement de création de modèle
    $(document).on('click', '#create-template-dialog .btn-valider.save', function(e){
        e.preventDefault();
        $('.options-wrapper').addClass('active');
        $('#create-template-dialog').find('.btn-valider.modify').removeClass('hidden');
        $('#create-template-dialog').find('.btn-valider.validate').removeClass('hidden');
        $('#create-template-dialog').find('.template-name-container').removeClass('hidden');
        $(this).addClass('hidden');
    });

    // retour à la modification de modèle
    $(document).on('click', '#create-template-dialog .btn-valider.modify', function(e){
        e.preventDefault();
        $('.options-wrapper').removeClass('active');
        $('#create-template-dialog').find('.btn-valider.validate').addClass('hidden');
        $('#create-template-dialog').find('.template-name-container').addClass('hidden');
        $('#create-template-dialog').find('.btn-valider.save').removeClass('hidden');
        $(this).addClass('hidden');
    });


    //validation de création de modèle
    $(document).on('click', '#create-template-dialog button.btn-valider.validate.validate-add', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var add_template_url = $('input[name=add_template_form_url]').val();
        $('#create-template-dialog').find('.block-model-container').remove();

        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }

        $('#create-template-dialog form').ajaxSubmit({
            type: 'POST',
            url: add_template_url,
            success: function(data){
                if(data['error']){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                    $('#create-template-dialog').find('.btn-valider.save').trigger('click');
                    installColorPicker();
                    installWysiwyg();
                    $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
                } else {
                    var new_template_id = data.id;
                    var create_campaign_url = $("input[name=new_campaign_url").val();
                    $.ajax({
                        type: 'POST',
                        url: create_campaign_url,
                        success: function(data){
                            var updated_structure = $(data.content);
                            var update_form_structure = $(updated_structure[4]);
                            $('#new-campaign-modal .template-lists ').replaceWith($(update_form_structure).find('.template-lists'));
                            $('#new-campaign-modal').find('.template-lists input[value='+new_template_id+']').prop('checked', true);
                            $('.step-3').addClass('done');
                            clickNext();
                            $('#create-template-dialog').modal('hide');
                        },
                        complete: function(){
                            $('.chargementAjax').addClass('hidden');
                        }
                    });
                }
            },
            statusCode: {
                404: function(){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-template-dialog').find('.error-message-container.general-message').text(message);
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    //upload images
    $(document).on('click', '.btn-upload.choose-upload-img-button', function(e){
        e.preventDefault();
        $(this).parent().find('.image-input').trigger('click');
    });

    $(document).on('click', '.upload-img-button-container', function(e){
        e.preventDefault();
        $(this).parent().find('.image-input').trigger('click');
    });

    $(document).on('change', '.image-input', function(){
        if('' == $(this).val().trim()){
            var initial_image_name = $(this).parent().find('input.initial-image').val();
            if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button').addClass('hidden-button');
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload.choose-upload-img-button').removeClass('hidden-button');
            } else {
                $(this).parent().find('.img-name-container').text(initial_image_name);
            }
        } else {
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').removeClass('hidden-button');
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload.choose-upload-img-button').addClass('hidden-button');
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
            $(this).parent().find('.delete-link.delete-image').show();
        }
    });

    // suppression logo
    $(document).on('click', '.delete-logo-image', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        resetUploadButton(current_delete_link);
        $('#create-template-dialog').find('input.delete-logo-image-command-hidden').val(true);
    });

    // suppression image de contenu de type image
    $(document).on('click', '.delete-content-image', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        resetUploadButton(current_delete_link);

        var delete_contents_image_hidden = $('#create-template-dialog').find('input.delete-contents-image-command-hidden');
        var current_delete_contents_image_command = delete_contents_image_hidden.val();
        var content_id = $(this).attr('data-content-id');
        if(('undefined' != typeof content_id) && ('' != content_id)){
            if('' == current_delete_contents_image_command){
                delete_contents_image_hidden.val(content_id);
            } else {
                var arr_current_delete_contents_image_command = current_delete_contents_image_command.split(',');
                if(!arr_current_delete_contents_image_command.includes(content_id)){
                    delete_contents_image_hidden.val(current_delete_contents_image_command+','+content_id);
                }
            }
        }
    });

    // ---------------------------------------------------------------------------------------------
    // partie prévisualisation instantanée dans popup
    // ---------------------------------------------------------------------------------------------
    /*$('#new-campaign-modal').on('new-template-creation-started', function(){
        setInstantaneousPreview($(this));
        setMiniInstantaneousPreview($(this));
    });*/

    $('#create-template-dialog').on('shown.bs.modal', function(){
        setInstantaneousPreview($(this));
        setMiniInstantaneousPreview($(this));
    });

    $('#create-template-dialog').on('hidden.bs.modal', function(){
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').html('');
    });

    $(document).on('click', '.magnify-template-preview', function(e){
        e.preventDefault();
        $('#instantaneous-preview-template-dialog').modal('show');
    });

    // changement logo
    $(document).on('change', '.logo-image-input', function(){
        var logo_image_block_no_image = $('#instantaneous-preview-template-dialog .pseudo-body-table .logo-img-tr-no-image');
        if(logo_image_block_no_image.length > 0){
            var new_logo_image_block = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.logo-img-tr').clone();
            logo_image_block_no_image.replaceWith(new_logo_image_block);
        }
        var logo_image_block = $('#instantaneous-preview-template-dialog .pseudo-body-table .logo-img-tr');
        createImagePreview(this, logo_image_block.find('img'));
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement alignement logo
    $(document).on('click', '.logo-alignment-option-radio', function(){
        var alignment_option_value = $(this).val();
        var logo_alignment = 'center';
        var logo_width = 200;
        switch(alignment_option_value){
            case $('input[name=template_logo_alignment_left]').val():
                logo_alignment = 'left';
                break;
            case $('input[name=template_logo_alignment_right]').val():
                logo_alignment = 'right';
                break;
            case $('input[name=template_logo_alignment_expanded]').val():
                logo_width = '100%';
                break;
        }
        var logo_image_block = $('#instantaneous-preview-template-dialog .pseudo-body-table .logo-img-tr');
        logo_image_block.find('td.logo-img-td').attr('align', logo_alignment);
        logo_image_block.find('td.logo-img-td img').attr('width', logo_width);
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement de contenu de type image
    $(document).on('change', '.image-content-input', function(e){
        var image_content_index = $(this).parents('.image-content-block').index('.image-content-block');
        var corresponding_image_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.img-content-tr').eq(image_content_index);
        if(corresponding_image_content_tr.hasClass('no-image')){
            var new_image_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.img-content-tr').not('.no-image').clone();
            corresponding_image_content_tr.replaceWith(new_image_content_tr);
            createImagePreview(this, new_image_content_tr.find('img'));
        } else {
            createImagePreview(this, corresponding_image_content_tr.find('img'));
        }
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement de contenu de type bouton
    // changement de texte
    function getCorrespondingButtonContentTr(element)
    {
        var button_content_index = element.parents('.button-content-block').index('.button-content-block');
        var corresponding_button_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.button-content-tr').eq(button_content_index);
        return corresponding_button_content_tr;
    }

    $(document).on('input', '.action-button-text-input', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a span').text($(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('click', '.delete-action-button-text', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a span').text('');
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement couleur de fond
    $(document).on('change', '.action-button-background-color', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a').css("background-color", $(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement couleur de couleur de texte
    $(document).on('change', '.action-button-text-color', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a').css("color", $(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // ajout nouvel image
    $(document).on('click', '.add-image-link', function(){
        var new_image_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.img-content-tr.no-image').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_image_content_tr);
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // ajout nouveau bouton
    $(document).on('click', '.add-button-link', function(){
        var new_button_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.button-content-tr').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_button_content_tr);
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changement de couleur de fond
        // couleur d'email
    $(document).on('change', '.email-color', function(){
        var main_table = $('#instantaneous-preview-template-dialog').find('.main-table');
        main_table.css('background-color', $(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });
        // couleur de fond
    $(document).on('change', '.template-background-color', function(){
        var pseudo_body_table = $('#instantaneous-preview-template-dialog').find('.pseudo-body-table');
        pseudo_body_table.css('background-color', $(this).val());
        var same_bg_color_as_background = $('#instantaneous-preview-template-dialog').find('.same-bg-color-as-background');
        same_bg_color_as_background.find('td').css('background-color', $(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // changements textes footer
    $(document).on('input', '.footer-text-option-input.company-info', function(){
        var footer_text_company_info = $('#instantaneous-preview-template-dialog').find('.footer-text-company-info');
        footer_text_company_info.text($(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('input', '.footer-text-option-input.contact', function(){
        var footer_text_contact = $('#instantaneous-preview-template-dialog').find('.footer-text-contact');
        footer_text_contact.text($(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('input', '.footer-text-option-input.unsubscribe', function(){
        var footer_text_unsubscribe = $('#instantaneous-preview-template-dialog').find('.footer-text-unsubscribe');
        footer_text_unsubscribe.text($(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('input', '.footer-text-option-input.additional-info', function(){
        var footer_text_additional_info = $('#instantaneous-preview-template-dialog').find('.footer-text-additional-info');
        footer_text_additional_info.text($(this).val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('click','.delete-footer-text-additional-info', function(){
        var footer_text_additional_info = $('#instantaneous-preview-template-dialog').find('.footer-text-additional-info');
        footer_text_additional_info.text('');
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // mise à jour prévisualisation à la suppression d'image de logo
    $(document).on('click', '.delete-logo-image', function(e){
        e.preventDefault();
        $('#instantaneous-preview-template-dialog').find('.pseudo-body-table .logo-img-tr img').attr('src', '');
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // mise à jour prévisualisation à la suppression d'image de contenu
    $(document).on('click', '.delete-content-image', function(e){
        e.preventDefault();
        var content_block = $(this).parents('.template-content-block');
        var image_content_block_index = content_block.index('.image-content-block');
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.img-content-tr').eq(image_content_block_index).find('img').attr('src', '');
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    // mise à jour prévisualisation en miniature
    $(document).on('template-preview-modified', '#instantaneous-preview-template-dialog .modal-body-container', function(){
        setTimeout(function(){
            var instantaneous_preview_content = $('#instantaneous-preview-template-dialog').find('.modal-body-container').children().first().clone();
            instantaneous_preview_content.find('.block-model-container').remove();
            // $('#create-template-dialog').find('.template-preview-container .template-preview-content-container').html('');
            $('#create-template-dialog').find('.template-preview-container .template-preview-content-container').find('.template-preview-wrapper').next().remove();
            $('#create-template-dialog').find('.template-preview-container .template-preview-content-container').append(instantaneous_preview_content);
        }, 100);

    });

    // --------------------------------------------------------------------------------------------
    // partie ajout d'autres contenus
    // --------------------------------------------------------------------------------------------
    function addContentConfigBock(new_content_index, content_type)
    {
        var new_content_type = $('.block-model-container').find('.template-content-config-model').clone();
        new_content_type.removeClass('template-content-config-model');
        var html_new_content_type = new_content_type.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_content_type = html_new_content_type.replace(/__name__/g, new_content_index);
        new_content_type = $($.parseHTML(html_new_content_type));
        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_content_type);
        new_content_type.show();
        new_content_type.find('input.content-type').val(content_type);
        new_content_type.find('input.content-order').val(new_content_index + 1); // 1-based index
    }

    $(document).on('click', '.add-other-content-container .add-image-link', function(e){
        e.preventDefault();
        var available_content_num = $('.template-config-block-container')
            .find('.template-config-block.template-content-block').length;
        var new_content_index = available_content_num;
        var new_image_content = $('.block-model-container').find('.template-content-image-model').clone();
        new_image_content.removeClass('template-content-image-model');
        var html_new_image_content = new_image_content.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_image_content = html_new_image_content.replace(/__name__/g, new_content_index);
        new_image_content = $($.parseHTML(html_new_image_content));

        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_image_content);
        new_image_content.show();

        addContentConfigBock(new_content_index, $('input[name=template_content_type_image]').val());
    });

    $(document).on('click', '.add-other-content-container .add-text-link', function(e){
        e.preventDefault();
        var available_content_num = $('.template-config-block-container')
            .find('.template-config-block.template-content-block').length;
        var new_content_index = available_content_num;
        var new_text_content = $('.block-model-container').find('.template-content-text-model').clone();
        new_text_content.removeClass('template-content-text-model');
        var html_new_text_content = new_text_content.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_text_content = html_new_text_content.replace(/__name__/g, new_content_index);
        new_text_content = $($.parseHTML(html_new_text_content));

        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_text_content);
        new_text_content.find('textarea').addClass('large-textarea');
        var ckeditor_config_light_path = $('input[name=ckeditor_config_light_path]').val();
        CKEDITOR.replace(new_text_content.find('textarea').attr('id'), {
            language: 'fr',
            uiColor: '#9AB8F3',
            height: 150,
            width: 600,
            customConfig: ckeditor_config_light_path
        });

        // fonctionnalité prévisualisation instantanée
        //  tr correspondant
        var new_text_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.text-content-tr').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_text_content_tr);
        // mise à jour de la prévisualisation
        CKEDITOR.instances[new_text_content.find('textarea').attr('id')].on('change', function(){
            var text_content_index = $('#'+this.name).parents('.text-content-block').index('.text-content-block');
            var corresponding_text_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.text-content-tr').eq(text_content_index);
            corresponding_text_content_tr.find('td').html(this.getData());
            $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
        });

        new_text_content.show();

        addContentConfigBock(new_content_index, $('input[name=template_content_type_text]').val());
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });

    $(document).on('click', '.add-other-content-container .add-button-link', function(e){
        e.preventDefault();
        var available_content_num = $('.template-config-block-container')
            .find('.template-config-block.template-content-block').length;
        var new_content_index = available_content_num;
        var new_button_content = $('.block-model-container').find('.template-content-button-model').clone();
        new_button_content.removeClass('template-content-buttton-model');
        var html_new_button_content = new_button_content.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_button_content = html_new_button_content.replace(/__name__/g, new_content_index);
        new_button_content =  $($.parseHTML(html_new_button_content));
        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_button_content);

        const DEFAULT_TEXT_COLOR = '#ffffff';
        const DEFAULT_BG_COLOR = '#ff0000';
        const DEFAULT_TEXT = 'mon bouton d\'action';
        new_button_content.find('.action-button-background-color').addClass('color-value');
        new_button_content.find('.action-button-background-color').val(DEFAULT_BG_COLOR);
        new_button_content.find('.action-button-text-color').addClass('color-value');
        new_button_content.find('.action-button-text-color').val(DEFAULT_TEXT_COLOR);
        installColorPicker();
        new_button_content.find('.action-button-preview').css({
            'background-color': DEFAULT_BG_COLOR,
            'color': DEFAULT_TEXT_COLOR
        });
        new_button_content.find('.action-button-text-input').val(DEFAULT_TEXT);
        new_button_content.find('.action-button-preview').text(DEFAULT_TEXT)

        addContentConfigBock(new_content_index, $('input[name=template_content_type_button]').val());
    });

    // --------------------------------------------------------------------------------------------
    // partie personnalisation
    // --------------------------------------------------------------------------------------------
    $(document).on('click', '.personalization-option', function(e){
        var personalization_value = $(this).attr('data-personalization-value');
        switch(personalization_value){
            case 'prénom':
                personalization_value = 'pr&eacute;nom';
                break;
            case 'civilité':
                personalization_value = 'civilit&eacute;';
                break;
        }

        var variable_personalization_value = '[[data:'+personalization_value+':]]';
        var main_text_content_id = $('#create-template-dialog .main-text-content').attr('id');
        if($(this).is(':checked')){
            if(CKEDITOR.instances[main_text_content_id]){
                CKEDITOR.instances[main_text_content_id].insertHtml(variable_personalization_value);
            }
        } else {
            if(CKEDITOR.instances[main_text_content_id]){
                var data = CKEDITOR.instances[main_text_content_id].getData();
                data = data.replace(new RegExp(/\[\[data\:/.source+personalization_value+/\:\]\]/.source, 'g'), '');
                CKEDITOR.instances[main_text_content_id].setData(data);
            }
        }
    });

    $('#create-template-dialog').on('shown.bs.modal', function(){
        setTimeout(function(){
            var main_text_content_id = $('#create-template-dialog .main-text-content').attr('id');
            if(CKEDITOR.instances[main_text_content_id]){
                var data = CKEDITOR.instances[main_text_content_id].getData();
                var personalization_option_list = $('#create-template-dialog').find('.personalization-option');
                personalization_option_list.each(function(){
                    var data_personalization_value = $(this).attr('data-personalization-value');
                    if(data.search(new RegExp(/\[\[data\:/.source+data_personalization_value+/\:\]\]/.source)) > -1){
                        $(this).prop('checked', true);
                    }
                });
            }
        }, 50);
    });

    // --------------------------------------------------------------------------------------------
    // partie alerte à la suppression de texte de footer obligatoire
    // --------------------------------------------------------------------------------------------
    $(document).on('click', '.delete-mandatory-input', function(e){
        e.preventDefault();
        $('#alert-footer-element-dialog').modal('show');
    });

    // --------------------------------------------------------------------------------------------
    // partie aspect bouton d'action
    // --------------------------------------------------------------------------------------------
    $(document).on('input', '#create-template-dialog .action-button-text-input', function(e){
        $(this).parents('.action-button-block').find('.action-button-preview').text($(this).val());
    });

    $(document).on('change', '#create-template-dialog .action-button-text-input', function(e){
        $(this).parents('.action-button-block').find('.action-button-preview').text($(this).val());
    });

    $(document).on('click', '#create-template-dialog .delete-action-button-text', function(e){
        $(this).parents('.action-button-block').find('.action-button-preview').text('');
    });

    $(document).on('change', '.action-button-background-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block').find('.action-button-preview').css("background-color", $(this).val());
    });

    $(document).on('change', '.action-button-text-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block').find('.action-button-preview').css("color", $(this).val());
    });

    // --------------------------------------------------------------------------------------------
    // partie suppression de contenu
    // --------------------------------------------------------------------------------------------
    $(document).on('click', '.delete-content', function(e){
        e.preventDefault();
        // fonctionnalité de prévisualisation instantanée
        var content_block = $(this).parents('.template-content-block');
        if(content_block.hasClass('image-content-block')){
            var content_block_index = content_block.index('.image-content-block');
            $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.img-content-tr').eq(content_block_index).remove();
        } else if (content_block.hasClass('text-content-block')) {
            var content_block_index = content_block.index('.text-content-block');
            $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.text-content-tr').eq(content_block_index).remove();
        } else if (content_block.hasClass('button-content-block')) {
            var content_block_index = content_block.index('.button-content-block');
            $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.button-content-tr').eq(content_block_index).remove();
        }

        // suppression des éléments du contenu
        $('#'+$(this).parents('.template-content-block').attr('data-form-field-id')).remove();
        $(this).parents('.template-config-block').next('.template-config-block').not('.template-content-block').remove();
        $(this).parents('.template-config-block').remove();

        $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
    });


    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Création de nouveau modèle, dans création campagne
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Liste campagnes archivées
     * *********************************************************************************************
     */
    //liste des archivées
    $(".add-to-archive").on("click", function (e) {
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        url = $(this).attr("data-target-link");
        $.ajax({
            type : "POST",
            url: url,
            success: function(html) {
                $('.row.list').html(html);
                $('.create-campaign-button').hide();
                $('.restore-campaign-button').show();
                $('.archive-campaign-button').parents('.campaign-archive').hide();
                resetCampaignCountBlock();
                $('.filtres a.dropdown-item.programmed-item').hide();
                $('.filtres').find('.dropdown-item').attr('data-archived-campaign-mode', true);
                $('.filtres').find('.delete-input').attr('data-archived-campaign-mode', true);
                $('.selected-campaign').find('.delete-campaign-button').attr('data-archived-campaign-mode', true);
                $('.chargementAjax').addClass('hidden');
            },
            statusCode: {
                404: function(){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Liste campagnes archivées
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Aperçu campagne
     * *********************************************************************************************
     */
	// aperçu campagne en popup
	$(document).on('click', 'a.campaign-preview', function(){
		var urlTpl = $(this).attr('data-url');
		var UrlApercuCampagne = $('input#preview_template_campagne').val();
		$('.chargementAjax').removeClass('hidden');
		/*
		$('#preview-template-dialog').find('.modal-body-container').html(' <iframe src="'+urlTpl+'"></iframe>');
		$('#preview-template-dialog').find('.modal-body-container').html(text);
		$('#preview-template-dialog').modal('show');
		$('.chargementAjax').addClass('hidden');
		*/
		
		$.ajax({
			type : "POST",
			url: UrlApercuCampagne,
			data : 'urlTpl='+urlTpl+'',
			dataType : 'html',
			success: function(text){
				$('#preview-template-dialog').find('.modal-body-container').html(text);
			},
            statusCode: {
			    404: function(){
                    $('#preview-template-dialog').find('.error-message-container.general-message').text('Aperçu non disponible')
                },
                500: function(){
                    $('#preview-template-dialog').find('.error-message-container.general-message').text('Aperçu non disponible')
                }
            },
            complete: function(){
                $('#preview-template-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
		});
		
		return false;
	});

    $('#preview-template-dialog').on('hidden.bs.modal', function(){
        $(this).find('.error-message-container.general-message').text('');
        $('#preview-template-dialog').find('.modal-body-container').html('');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Aperçu campagne
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Archive de campagnes
     * *********************************************************************************************
     */
    // fontionnalité "archiver campagne"
    $(document).on('click', '.archive-campaign-button', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var campaign_checked_ids = getChecked();
        campaign_checked_ids = campaign_checked_ids.join(',');
        var archive_campaign_url = $('input[name=archive_campaign_url]').val();
        $.ajax({
            type: 'POST',
            url: archive_campaign_url,
            data: {campaign_checked_ids: campaign_checked_ids},
            success: function(){
                window.location.replace($('input[name=campaign_list_url]').val());
            },
            statusCode:{
                404: function(){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    // fonctionnalité "restauration campagne archivée"
    $(document).on('click', '.restore-campaign-button', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var campaign_checked_ids = getChecked();
        campaign_checked_ids = campaign_checked_ids.join(',');
        var restore_campaign_url = $('input[name=restore_campaign_url]').val();
        $.ajax({
            type: 'POST',
            url: restore_campaign_url,
            data: {campaign_checked_ids: campaign_checked_ids},
            success: function(){
                window.location.replace($('input[name=campaign_list_url]').val());
            },
            statusCode:{
                404: function(){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    // fonctionnalité "archiver", dans "Actions"
    $(document).on('click', '.dropdown-item.campaign-archive', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var campaign_id = 'undefined' !== typeof $(this).attr('data-campaign-draft-id') ? $(this).attr('data-campaign-draft-id') : '';
        var archive_campaign_url = $('input[name=archive_campaign_url]').val();
        $.ajax({
            type: 'POST',
            url: archive_campaign_url,
            data: {campaign_checked_ids: campaign_id},
            success: function(){
                window.location.replace($('input[name=campaign_list_url]').val());
            },
            statusCode:{
                404: function(){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Archive de campagnes
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Suppression de campagne(s)
     * *********************************************************************************************
     */
    const confirm_delete_campaign = 'Êtes-vous sûr de vouloir supprimer définitivement cette campagne?';
    const confirm_delete_campaign_plural = 'Êtes-vous sûr de vouloir supprimer définitivement ces campagnes?';
    $(document).on('click', '.delete-campaign-button', function(e){
        e.preventDefault();
        var campaign_draft_ids = getChecked();
        campaign_draft_ids = campaign_draft_ids.join(',');
        $('#confirm-delete-campaign').find('input[name=campaign_draft_ids]').val(campaign_draft_ids);
        if(campaign_draft_ids.indexOf(',') > 0){
            $('#confirm-delete-campaign').find('.modal-body .message').text(confirm_delete_campaign_plural);
        } else {
            $('#confirm-delete-campaign').find('.modal-body .message').text(confirm_delete_campaign)
        }
        $('#confirm-delete-campaign').modal('show');
    });

    $(document).on('click', '#confirm-delete-campaign .confirm-delete', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var campaign_draft_ids = $('#confirm-delete-campaign').find('input[name=campaign_draft_ids]').val();
        var delete_campaign_url = $('input[name=delete_campaign_url]').val();
        $.ajax({
            type: 'POST',
            url: delete_campaign_url,
            data: {campaign_checked_ids: campaign_draft_ids},
            success: function(){
                if('undefined' !== typeof $('.selected-campaign .delete-campaign-button').attr('data-archived-campaign-mode')
                    && 'true' == $('.selected-campaign .delete-campaign-button').attr('data-archived-campaign-mode')){
                    $('.chargementAjax').addClass('hidden');
                    $('#confirm-delete-campaign').modal('hide');
                    $('.archived-campaign').trigger('click');
                } else {
                    window.location.replace($('input[name=campaign_list_url]').val());
                }
            },
            statusCode:{
                404: function(){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Suppression de campagne(s)
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Création de nouvelle liste, dans création campagne
     * *********************************************************************************************
     */
    // création de nouvelle liste (appel popup de création)
    $(document).on('click', '#new-campaign-modal .create-new-contact-list', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_contact_list_url = $('input[name=create_contact_list_url]').val();
        $.ajax({
            type: 'POST',
            url: create_contact_list_url,
            success: function(data){
                $('#create-contact-list-modal').find('.body-container').html('');
                $('#create-contact-list-modal').find('.body-container').html(data);
                $('#create-contact-list-modal').modal('show');

                var table1 = $('#ListUserContactMailjet').DataTable({
                    lengthChange: false,
                    "info":     false,
                    /*searching: false,*/
                    "columnDefs": [ {
                        "targets": 1,
                        "orderable": false
                    } ]
                });

                table1.buttons().container().appendTo( '#ListUserContactMailjet_wrapper .col-md-6:eq(0)' );

                //Search engine
                $('input.input-search-list').on('keyup', function(){
                    table1.search(this.value).draw();
                    //Modification libellé 'Previews && Next'
                    OverridePagination();

                    //Modifier conteneur pagination
                    UpdateWidthPagination();

                    //Décocher tous les contacts
                    $('input.form-field-published').each(function(i){
                        $(this).prop("checked", false);
                    });

                    //Ajouter separateur sur les boutons de pagination
                    AddSeparatorPaginate();
                });

                //Menu filter table (manager/commercial/participant)
                $('a.FiltreList').unbind().bind('click', function() {
                    var searchTerm = $.trim($(this).html().toLowerCase());

                    if (!searchTerm) {
                        table1.draw();
                        return;
                    }
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        for (var i=0;i<data.length;i++) {
                            if (data[i].toLowerCase() == searchTerm) return true;
                        }
                        return false;
                    });
                    table1.draw();
                    $.fn.dataTable.ext.search.pop();

                    //Modification libellé 'Previews && Next'
                    OverridePagination();

                    //Modifier conteneur pagination
                    UpdateWidthPagination();

                    //Décocher tous les contacts
                    $('input.form-field-published').each(function(i){
                        $(this).prop("checked", false);
                    });

                    //Ajouter separateur sur les boutons de pagination
                    AddSeparatorPaginate();

                    return false;
                });

                //Reset filtre
                $('span#id-delete-input-filtre-creer-liste-contact').on('click', function(){
                    table1.search('').columns().search('').draw();
                    $('button#dropdownMenuFiltreCreerListContact').html('FILTRER PAR LISTE');
                    $(this).hide();

                    //Modification libellé 'Previews && Next'
                    OverridePagination();

                    //Modifier conteneur pagination
                    UpdateWidthPagination();

                    //Décocher tous les contacts
                    $('input.form-field-published').each(function(i){
                        $(this).prop("checked", false);
                    });

                    //Ajouter separateur sur les boutons de pagination
                    AddSeparatorPaginate();

                    return false;
                });

                //Modification libellé 'Previews && Next'
                OverridePagination();

                //Modifier conteneur pagination
                UpdateWidthPagination();

                //Décocher tous les contacts
                $('input.form-field-published').each(function(i){
                    $(this).prop("checked", false);
                });

                //Ajouter separateur sur les boutons de pagination
                AddSeparatorPaginate();

            },
            statusCode: {
                404: function(){
                    $('#create-contact-list-modal').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#create-contact-list-modal').find('.modal-body-container').hide();
                    $('#create-contact-list-modal').modal('show');
                },
                500: function(){
                    $('#create-contact-list-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#create-contact-list-modal').find('.modal-body-container').hide();
                    $('#create-contact-list-modal').modal('show');
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    $('#create-contact-list-modal').on('hidden.bs.modal', function(){
        $(this).find('.body-container').html('');
        $(this).find('.error-message-container.general-message').html('');
        $('#create-contact-list-modal').find('.modal-body-container').show();
    });

    // click sur checkbox, selection multiple
    $(document).on('click', 'label.check-ligne', function(){
        var DataId = $(this).attr('data-id');
        $('input#cl1-'+DataId+'').click();
    });

    //Incremente/Decremente les nombres des contacts
    $(document).on('click', 'input.contact-ajout', function(){
        var NbreContactsEnCours = parseInt($('input#id_nbre_contacts_selectionner').val());
        if($(this).is(':checked')){
            NbreContactsEnCours++;
        }else{
            NbreContactsEnCours--;
        }
        $('input#id_nbre_contacts_selectionner').val(NbreContactsEnCours);
    });

    $(document).on('click', 'input#checkbox-publish-all', function(){
        if($(this).is(':checked')){
            $('input#id_nbre_contacts_selectionner').val(0);
        }

        if($(this).is(':checked')){
            $('input.contact-ajout').each(function(i){
                $(this).prop("checked", false);
                $(this).click();
            });
        }else{
            $('input.contact-ajout').each(function(i){
                $(this).prop("checked", true);
                $(this).click();
            });
        }
    });

    // click, trie dans dataTable
    $(document).on('click', 'table#ListUserContactMailjet th', function(){
        OverridePagination();
        AddSeparatorPaginate();
    });

    // création de nouvelle liste (validation de création)
    $(document).on('click', '#create-contact-list-modal #valider-creation-contact-list', function(e){
        e.preventDefault();
        //Verifier les champs obligatoires
        var id_nom_contact_list = $.trim($('input#id_nom_contact_list').val());

        if('' != id_nom_contact_list){
            var cpt = 0;
            var ListIdUser = [];
            $('input.contact-ajout:checked').each(function(e){
                ListIdUser.push($(this).val());
                cpt++;
            });

            if(cpt > 0){
                var ListIdUserAll = ListIdUser.join('##_##');
                var UrlAddContactList = $('input[name=submit_create_contact_list_url]').val();
                $('.chargementAjax').removeClass('hidden');
                $.ajax({
                    type: 'POST',
                    url: UrlAddContactList,
                    data : 'UserId='+ListIdUserAll+'&ListName='+id_nom_contact_list+'',
                    success: function(data){
                        if(data['error']){
                            $('#create-contact-list-modal').find('.error-message-container.general-message').text(data['error']);
                            $('#create-contact-list-modal').find('.modal-body-container').hide();
                        } else {
                            addNewContactListInChoiceList(data['id'], id_nom_contact_list);
                            $('#create-contact-list-modal').modal('hide');
                        }
                    },
                    complete: function(){
                        $('.chargementAjax').addClass('hidden');
                    }
                });

            } else {
                alert('Veuillez selectionner les contacts à ajouter à la liste');
            }
        } else {
            alert('Veuillez entrer la nom de la liste');
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Création de nouvelle liste, dans création campagne
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Suspension de création de campagne
     * *********************************************************************************************
     */
    $(document).on('click', '#abort-new-campaign-modal .btn-abort-creation', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_campaign_url = $('input[name=new_campaign_url]').val();
        var campaign_creation_by_halt_mode = $('input[name=campaign_draft_creation_mode_by_halt').val();
        var data = {'creation_mode': campaign_creation_by_halt_mode};
        $('#new-campaign-modal form').ajaxSubmit({
            type: 'POST',
            url: create_campaign_url,
            data: data,
            success: function(data){
                if(data['error']){
                    $('#abort-new-campaign-modal').find('.modal-body-container').hide();
                    $('#abort-new-campaign-modal').find('.error-message-container.general-message').text(data['error']);
                    $('#new-campaign-modal').modal('hide');
                    $('.chargementAjax').addClass('hidden');
                } else {
                    $('#abort-new-campaign-modal').modal('hide');
                    $('#new-campaign-modal').modal('hide');
                    sendFilter();
                }
            },
            statusCode: {
                404: function(){
                    $('#abort-new-campaign-modal').find('.modal-body-container').hide();
                    $('#abort-new-campaign-modal').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#new-campaign-modal').modal('hide');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('#abort-new-campaign-modal').find('.modal-body-container').hide();
                    $('#abort-new-campaign-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#new-campaign-modal').modal('hide');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    $('#abort-new-campaign-modal').on('show.bs.modal', function(){
        $(this).find('.modal-body-container').show();
    });

    $('#abort-new-campaign-modal').on('hidden.bs.modal', function(){
        $(this).find('.error-message-container.general-message').text('');
    });


    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * Suspension de création de campagne
     * *********************************************************************************************
     */
});

function getChecked() {
    var checked = [];
    $(".campagne-name .styled-checkbox").each(function() {
        if ($(this).is(':checked')) {
            checked.push($(this).attr('id'));
        }
    });
    return checked;
}

function sendFilter(source = null) {
    /*var a=$("#loading-image").clone();
    $('.row.list').html(a);*/
    $('.chargementAjax').removeClass('hidden');
    var sort_filter = $('.dropdown.filtres').find('button').hasClass('active'),
        data = {};

    if (sort_filter) {
        data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
    }

    if (null != source) {
        if ('undefined' !== typeof source.attr('data-archived-campaign-mode')
            && 'true' == source.attr('data-archived-campaign-mode')) {
            data.archived_campaign_mode = true;
        }
    }

    var url = $('input[name=filtered]').val();
    $.ajax({
        type: "POST",
        url : url,
        data: data,
        success: function(html) {
            $('.row.list').html(html);
            $('.chargementAjax').addClass('hidden');
        }
    });
}

function showPrevious() {
    if ($('#create-tabs a:first-child').hasClass('active')) {
        $('#new-campaign-modal .previous').css('display','none');
    } else {
        $('#new-campaign-modal .previous').css('display','initial');
    }
}

function initCalendar() {
    $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);//langue datepicker
    $('#calendar').datepicker({
        minDate: new Date(),
        altField: ".date_launch_campaign",
        altFormat: "dd/mm/yy"
    });
}

function initSelectChosen() {
    $(".chosen-select").chosen({//hour selectable
        disable_search: true,
        width: "70px"
    });
}

function addDone() {
    if (! $('#create-tabs a.activated.active').hasClass("done")) {
        $('#create-tabs a.activated.active').addClass("done");
    }
}

function removeDone() {
    if ($('#create-tabs a.activated.active').hasClass("done")) {
        $('#create-tabs a.activated.active').removeClass("done");
    }
}

function isDone() {
    if ($('#create-tabs a.activated.active').hasClass("step-1")) {
        if ($(".campaign_name_input").val() && $(".campaign_object_input").val()) {
            addDone();
            return true;
        } else {
            removeDone();
            alert("Veuillez completer les deux champs pour aller à l'étape suivante...");
            return false;
        }
    }
    if ($('#create-tabs a.activated.active').hasClass("step-2")) {
        if($('#dropdownMenuListe').html().trim() == $('#dropdownMenuListe').attr("data-default")) {
            removeDone();
            alert("Veuillez sélectionner une liste ou créez-en une...");
            return false;
        } else {
            addDone();
            return true;
        }
    }
    if ($('#create-tabs a.activated.active').hasClass("step-3")) {
        // if ($('input[name=template_choice_option]:checked').val() || "undefined" != typeof $('input[name=template_choice_option]:checked').val() ) {
        if ($('#new-campaign-modal .template-choice-container .template-choice-input:checked').length > 0) {
            addDone();
            return true;
        } else {
            removeDone();
            alert("Veuillez sélectionner un template ou créez-en un...");
            return false;
        }
    }
}

function setSelectedContactList()
{
    var selected_choice = $('#new-campaign-modal').find('.list-choice .list-choice-select option:selected');
    if(selected_choice.length == 1){
        $('#new-campaign-modal').find('.list-choice button.dropdown-toggle').text(selected_choice.text());
    }
}

function setLaunchDateInModal(launch_date)
{
    var fr_month_array = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    var arr_launch_date = launch_date.split('/');
    var str_launch_date = arr_launch_date[0] + ' ' + fr_month_array[arr_launch_date[1]-1] + ' ' + arr_launch_date[2];
    $('#done-campaign-modal').find('.date-envoi').text(str_launch_date);
}

function deactivateCampaignConfirmationOnClose(){
    $('#new-campaign-modal').find('.close-modal').removeAttr('data-toggle');
    $('#new-campaign-modal').find('.close-modal').attr('data-dismiss', 'modal');
}

function activateCampaignConfirmationOnClose(){
    $('#new-campaign-modal').find('.close-modal').attr('data-toggle', 'modal');
    $('#new-campaign-modal').find('.close-modal').removeAttr('data-dismiss');
    $('#new-campaign-modal').find('.close-modal').attr('data-target', '#abort-new-campaign-modal');
}

function installWysiwyg()
{
    var ckeditor_config_light_path = $('input[name=ckeditor_config_light_path]').val();
    var text_area_list = $('textarea.large-textarea');
    text_area_list.each(function(){
        CKEDITOR.replace( $(this).attr('id'), {
            language: 'fr',
            uiColor: '#9AB8F3',
            height: 150,
            width: 600,
            customConfig: ckeditor_config_light_path
        });

        // instantaneous preview feat
        CKEDITOR.instances[$(this).attr('id')].on('change', function(){
            var text_content_index = $('#'+this.name).parents('.text-content-block').index('.text-content-block');
            var corresponding_text_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.text-content-tr').eq(text_content_index);
            corresponding_text_content_tr.find('td').html(this.getData());
            $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
        });
    });
}

function installColorPicker()
{
    if ($('.color-value').length >0 ) {
        $('.color-value').each( function() {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'brightness',
                defaultValue: $(this).attr('data-defaultValue') || '',
                format: $(this).attr('data-format') || 'hex',
                keywords: $(this).attr('data-keywords') || '',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: $(this).attr('data-letterCase') || 'lowercase',
                opacity: $(this).attr('data-opacity'),
                position: $(this).attr('data-position') || 'bottom left',
                swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                change: function(value, opacity) {
                    if( !value ) return;
                    if( opacity ) value += ', ' + opacity;
                },
                theme: 'bootstrap'
            });
        });
    }
}

function clickNext() {
    var next = $('#create-tabs a.activated.active').next('a');
    if (!next.hasClass("activated")) {
        next.addClass("activated");
    }
    if (next.hasClass("step-4")) {
        next.addClass("done");
    }
    next.click();
    showPrevious();
}

//reset upload images
function resetUploadButton(current_delete_link)
{
    var wrapper = current_delete_link.parent().find('input[type=file]').wrap('<form></form>').parent();
    wrapper.trigger('reset');
    current_delete_link.parent().find('input[type=file]').unwrap();
    current_delete_link.parent().find('.upload-img-button').addClass('hidden-button');
    current_delete_link.parent().find('.upload-img-button-container').addClass('hidden-button');
    current_delete_link.parent().find('.choose-upload-img-button').removeClass('hidden-button');
    current_delete_link.hide();
}

function addContentConfigBock(new_content_index, content_type)
{
    var new_content_type = $('.block-model-container').find('.template-content-config-model').clone();
    new_content_type.removeClass('template-content-config-model');
    var html_new_content_type = new_content_type.wrap('<div class="model-wrapper"></div>').parent().html();
    html_new_content_type = html_new_content_type.replace(/__name__/g, new_content_index);
    new_content_type = $($.parseHTML(html_new_content_type));
    var add_content_link_block = $(".add-other-content-container");
    add_content_link_block.before(new_content_type);
    new_content_type.show();
    new_content_type.find('input.content-type').val(content_type);
    new_content_type.find('input.content-order').val(new_content_index + 1); // 1-based index
}

function resetCampaignCountBlock()
{
    $('.selected-count input').val('');
    $('.row.selected-campaign').hide();
}

/**
 * fonctions relatives à la création de liste
 */
function OverridePagination(){
    //Modification libellé pagination 'Next' => 'dernier'
    $('li#ListUserContactMailjet_next a').html('dernier');

    //Cacher le bouton 'Previews'
    $('li#ListUserContactMailjet_previous').hide();
}

function UpdateWidthPagination(){
    var LargeurTableData = $('div#ListUserContactMailjet_wrapper').width();
    $('div#ListUserContactMailjet_paginate').css('width', ''+LargeurTableData+'px');
}

function AddSeparatorPaginate(){
    $('li.paginate_button').each(function(i){
        if(i > 0){
            if(!$(this).next().hasClass('paginate_separator')){
                $('<li class = "paginate_separator"><span>&nbsp;</span></li>').insertAfter($(this));
            }
        }
    });
    $('li.paginate_separator').last().remove();
}

function addNewContactListInChoiceList(id, name){
    var new_contact_list_option = $('<option></option>');
    new_contact_list_option.attr('value', id);
    new_contact_list_option.text(name);
    $('#new-campaign-modal').find('.list-choice .list-choice-select').append(new_contact_list_option);
    new_contact_list_option.prop('selected', true);

    var new_contact_list_dropdown_item = $('<a></a>');
    new_contact_list_dropdown_item.addClass('dropdown-item')
    new_contact_list_dropdown_item.attr('href','#');
    new_contact_list_dropdown_item.attr('data-value', id);
    new_contact_list_dropdown_item.text(name);
    $('#new-campaign-modal').find('.list-choice .dropdown-menu').append(new_contact_list_dropdown_item);
    $('#new-campaign-modal').find('.list-choice button.dropdown-toggle').text(name);
}

/**
 * FIN - fonctions relatives à la création de liste
 */

function setInstantaneousPreview(current_create_template_dialog)
{
    var instantaneous_preview_content = current_create_template_dialog.find('.instantaneous-preview-container').html();
    $('#instantaneous-preview-template-dialog').find('.modal-body-container').html(instantaneous_preview_content);
}

function setMiniInstantaneousPreview(current_create_template_dialog)
{
    $('#instantaneous-preview-template-dialog').find('.modal-body-container').trigger('template-preview-modified');
}

function createImagePreview(input, preview_container) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview_container.attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}



