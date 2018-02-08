$(document).ready(function() {
	$('#preview-template-dialog').modal('hide');

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * Selection multiple dans liste campagne
     * *********************************************************************************************
     */
    function getChecked() {
        var checked = [];
        $(".campagne-name .styled-checkbox").each(function() {
            if ($(this).is(':checked')) {
                checked.push($(this).attr('id'));
            }
        });
        return checked;
    }

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
            altFormat: "d MM yy"
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
        if ($(this).val() =="0") {
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

    $(document).on('click', '.add.free-add.add-list', function(e){//création nouveau template
        e.preventDefault();
        var template_model = "text-and-image";
        // var template_model = "TEXT_AND_IMAGE";

        if(null !== template_model){
            var add_template_url = $('input[name=add_template_form_url]').val();
            add_template_url = add_template_url+'/'+template_model;

            $.ajax({
                type: 'GET',
                url: add_template_url,
                success: function(data){
                    $('#new-campaign-modal').find('.modal-step-3.first').css('display','none');
                    $('#new-campaign-modal').find('.modal-step-3.second').css('display','block').html(data.content);
                    $('#new-campaign-modal .previous').addClass('keep');
                    var text = "Créer votre email, il sera sauvegardé dans l'onglet \"modèle d'e-mail\", vous pourrez le réutiliser et/ou modifier ultérieurement.";
                    $('#new-campaign-modal').find('.modal-step-3 .dialog-title').html(text);
                    $('#new-campaign-modal').find('.modal-step-3 .dialog-title').removeClass('dialog-title');
                    $(".btn-valider.modify").addClass("hidden");
                    $(".btn-valider.modify").addClass("hidden");
                    $(".btn-valider.validate.validate-add").addClass("hidden");
                    $(".btn-end-step-3").addClass("hidden");
                    installColorPicker();
                    installWysiwyg();
                },
                statusCode: {
                    404: function(data){
                        $('#new-campaign-modal').find('.modal-step-3.first > .error-message-container').text(data.responseJSON.message);
                    }
                }
            });
        }
    });

    $(document).on("click", ".btn-end-step.btn-end-step-4", function() {//envoie campagne
        $('.chargementAjax').removeClass('hidden');
        var new_campaign_url = $('input[name=new_campaign_url]').val();
        $('#new-campaign-modal form').ajaxSubmit({
            type: 'POST',
            url: new_campaign_url,
            success: function(){
                $("#new-campaign-modal").modal("hide");
                setTimeout(function(){
                    $("#sent-campaign-modal").modal("show");
                },0);
            },
            statusCode:{
                404: function(){

                },
                500: function(){

                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });


        /*$("#new-campaign-modal").modal("hide");
        $("span.date-envoi").html($('.date_launch_campaign').val());
        setTimeout(function(){
            $("#done-campaign-modal").modal("show");
        },0);*/
    });

    $(document).on("click", ".btn-end-step.btn-program-step-4", function() {//envoie campagne programmée
        $("#new-campaign-modal").modal("hide");
        $("span.date-envoi").html($('.date_launch_campaign').val());
        setTimeout(function(){
            $("#done-campaign-modal").modal("show");
        },0);
    });

    $('.create-campaign-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_campaign_url = $("input[name=new_campaign_url").val();
        $.ajax({
            type: 'POST',
            url: create_campaign_url,
            success: function(data){
                $('#new-campaign-modal').find(".modal-content .content").html(data.content);
                $('#create-tabs a.activated:last').tab('show');
                showPrevious();
                initCalendar();
                initSelectChosen();
            },
            statusCode: {
                404: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Page non trouvée');
                    $('#new-campaign-modal').find('.previous').hide();
                },
                500: function(){
                    $('#new-campaign-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#new-campaign-modal').find('.previous').hide();
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

    // annulation selection de liste de diffusion
    const DEFAUTL_LIST_CHOICE_LABEL = 'CHOISIR UNE LISTE';
    $(document).on('click', '.list-choice .delete-input.delete-selection', function(){
        $(this).parents('.dropdown').find('button.dropdown-toggle').text(DEFAUTL_LIST_CHOICE_LABEL);
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
     * modal création template - enregistrement et validation
     * *********************************************************************************************
     */
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

    $(document).on('click', '.modal-step-3 .btn-valider.save', function(e){
        e.preventDefault();
        $('.options-wrapper').addClass('active');
        $('.modal-step-3').find('.btn-valider.modify').removeClass('hidden');
        $('.modal-step-3').find('.btn-valider.validate').removeClass('hidden');
        $('.modal-step-3').find('.template-name-container').removeClass('hidden');
        $(this).addClass('hidden');
    });

    $(document).on('click', '.modal-step-3 .btn-valider.modify', function(e){
        e.preventDefault();
        $('.options-wrapper').removeClass('active');
        $('.modal-step-3').find('.btn-valider.validate').addClass('hidden');
        $('.modal-step-3').find('.template-name-container').addClass('hidden');
        $('.modal-step-3').find('.btn-valider.save').removeClass('hidden');
        $(this).addClass('hidden');
    });


    //validation de template
    $(document).on('click', '.modal-step-3 button.btn-valider.validate.validate-add', function(e){
        var add_template_url = $('input[name=add_template_form_url]').val();
        e.preventDefault();
        $('.modal-step-3').find('.block-model-container').remove();

        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }

        $('.modal-step-3 form').ajaxSubmit({
            type: 'POST',
            url: add_template_url,
            success: function(data){
               if(data['error']){                    
                   $('#new-campaign-modal').find('.modal-step-3.second').html(data.content);
                   $('#new-campaign-modal').find('.modal-step-3.second').find('.btn-valider.save').trigger('click');
                   var text = "Créer votre email, il sera sauvegardé dans l'onglet \"modèle d'e-mail\", vous pourrez le réutiliser et/ou modifier ultérieurement.";
                   $('#new-campaign-modal').find('.modal-step-3 .dialog-title').html(text);
                   $('#new-campaign-modal').find('.modal-step-3 .dialog-title').removeClass('dialog-title');
                   installColorPicker();
                   installWysiwyg();
               } else {
                    //set new template selected
                    // console.log(data);
                    $('.step-3').addClass('done');
                    clickNext();//next step
               }
            },
            statusCode: {
                404: function(data){
                    $('#new-campaign-modal').find('.modal-step-3').find('.error-message-container.general-message').text('Erreur');                    
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

    $(document).on('click', '.delete-logo-image', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        resetUploadButton(current_delete_link);
        $('#create-template-dialog').find('input.delete-logo-image-command-hidden').val(true);
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * modal création template - enregistrement et validation
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Campagnes
     * ajout d'autres contenus, dans création template
     * *********************************************************************************************
     */
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
        new_text_content.show();

        addContentConfigBock(new_content_index, $('input[name=template_content_type_text]').val());
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Campagnes
     * ajout d'autres contenus, dans création template
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

    function resetCampaignCountBlock()
    {
        $('.selected-count input').val('');
        $('.row.selected-campaign').hide();
    }

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
				$('#preview-template-dialog').modal('show');
				$('.chargementAjax').addClass('hidden');
			}
		});
		
		return false;
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

});
