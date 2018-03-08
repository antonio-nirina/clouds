$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */
    $(document).ready(function(){
        $('.chargementAjax').removeClass('hidden');
        $('.main-section').jplist({
            itemsBox: '.news-post-list',
            itemPath: '.news-post-element',
            panelPath: '.control-panel'
        });
        $('.jplist-no-results').removeClass('hidden-block');
        $('.chargementAjax').addClass('hidden');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */
    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */
    // création actu, appel formulaire
    $('.create-news-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_news_post_url = $('input[name=create_news_post_url]').val();
        var data = addPostTypeLabelInAjaxData({});
        $.ajax({
            type: 'GET',
            url: create_news_post_url,
            data: data,
            success: function(data){
                $('#create-edit-news-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();
                installColorPicker();
                initCalendar();
                initSelectChosen();
            },
            statusCode: {
                404: function(data){
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-edit-news-modal').modal('show');
                setTimeout(function(){
                    $('.chargementAjax').addClass('hidden');
                }, 1050);
            }
        });
    });

    $('#create-edit-news-modal').on('hidden.bs.modal', function(){
        $(this).find('.modal-body-container').html('');
        $(this).find('.error-message-container.general-message').text('');
    });

    // création actu, soumission de création
    $(document).on('click', '#create-edit-news-modal .submit-block-container .btn-valider', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
        var submission_type = $(this).attr('data-submission-type');
        var data = {'submission_type': submission_type};
        var target_url = '';
        if ('create' == $(this).attr('data-manip-type')) {
            target_url = $('input[name=create_news_post_url]').val();
        } else if ('edit' == $(this).attr('data-manip-type')) {
            target_url = $(this).attr('data-target-url');
        }
        data = addPostTypeLabelInAjaxData(data);
        if ($('input[name=welcoming_news_post_type]').length > 0 && 'true' == $('input[name=welcoming_news_post_type]').val()){
            var redirect_target = $('input[name=welcoming_news_post_list_url]').val();
        } else {
            var redirect_target = $('input[name=news_post_list_url]').val();
        }

        $('#create-edit-news-modal form').ajaxSubmit({
            type: 'POST',
            url: target_url,
            data: data,
            success: function(data){
                if(data['error']){
                    $('#create-edit-news-modal').find('.modal-body-container').html(data.content);
                    installWysiwyg();
                    installColorPicker();
                    initCalendar();
                    initSelectChosen();
                } else {
                    window.location.replace(redirect_target);
                }
            },
            statusCode: {
                404: function(){
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text('Erreur interne');
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    // ------------------------------------------------------------------------------------------------------------------
    // partie bouton d'action
    // ------------------------------------------------------------------------------------------------------------------
    // selection (choix multiple select)
    $(document).on('click', '.styled-choice-select .dropdown-menu .dropdown-item', function(e){
        e.preventDefault();
        var data_value = $(this).attr('data-value');
        $(this).parents('.styled-choice-select').find('select').find('option[value="'+data_value+'"]').prop('selected', true);
        $(this).parents('.dropdown').find('button.dropdown-toggle').text($(this).text());
        $(this).parents('.dropdown').find('.delete-select').css({'visibility':'visible','display':'inline-block'});
    });

    // suppression du choix dans choix multiple select
    $(document).on('click', '.delete-select', function(e){
        e.preventDefault();
        var placeholder_option = $(this).parents('.styled-choice-select').find('select').find('option[value=""]');
        if(placeholder_option.length > 0){
            placeholder_option.prop('selected', true);
        } else {
            $(this).parents('.styled-choice-select').find('select').find('option').first().prop('selected', true);
        }

        var button_toggle = $(this).parents('.styled-choice-select').find('button.dropdown-toggle');
        button_toggle.text(button_toggle.attr('data-default-button-text'));
        $(this).hide();
    });

    //boutton de programmation de publication
    $(document).on('click', 'input.programmed-state-input', function() {
        var data_programmed_value = $(this).attr('data-programmed-value');
        console.log(data_programmed_value);
        if('true' == data_programmed_value){
            $('#create-edit-news-modal div.select-date').show();
        } else if ('false' == data_programmed_value){
            $('#create-edit-news-modal div.select-date').hide();
        }
    });

    // aperçu bouton d'action, texte
    $(document).on('input', '.action-button-text-input', function(){
        $(this).parents('.action-button-block-container').find('.action-button-preview').text($(this).val());
    });

    $(document).on('click', '.delete-action-button-text', function(){
        $(this).parents('.action-button-block-container').find('.action-button-preview').text('');
    });

    // aperçu bouton, couleur de bouton (couleur de fond)
    $(document).on('change', '.action-button-background-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block-container').find('.action-button-preview').css("background-color", $(this).val());
    });

    // aperçu bouton, couleur texte
    $(document).on('change', '.action-button-text-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block-container').find('.action-button-preview').css("color", $(this).val());
    });

    // ajout de bouton d'action
    $(document).on('click', '.action-button-container .add', function(e){
        e.preventDefault();
        $(this).parents('.action-button-container').hide();
        $('#create-edit-news-modal .action-button-block-container').show();
        $('#create-edit-news-modal').find('input.action-button-state-input').prop('checked', true);
    });

    // suppression de bouton d'action
    $(document).on('click', '.action-button-block-container .delete-block', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block-container').hide();
        $('#create-edit-news-modal').find('.action-button-container').show();
        $('#create-edit-news-modal').find('input.action-button-state-input').prop('checked', false);
    });

    // ------------------------------------------------------------------------------------------------------------------
    // partie "qui verra ce post?"
    // ------------------------------------------------------------------------------------------------------------------
    // mise à jour de donnée de formulaire : authorization_viewer_role
    $(document).on('click', '#create-edit-news-modal .viewer-authorization-type-choice .dropdown .dropdown-item', function(){
        $('#create-edit-news-modal').find('.authorized-viewer-role-input').val($(this).text());
    });

    $(document).on('click', '#create-edit-news-modal .viewer-authorization-type-choice .delete-select', function(){
        var toggle_button = $('#create-edit-news-modal').find('.viewer-authorization-type-choice button.toggle-button');
        console.log(toggle_button);
        $('#create-edit-news-modal').find('.authorized-viewer-role-input').val(toggle_button.attr('data-default-button-text'));
    });

    // ------------------------------------------------------------------------------------------------------------------
    // PARTIE EDITION POST/ACTU
    // ------------------------------------------------------------------------------------------------------------------
    // édition actu, appel de formulaire
    $(document).on('click', '.edit-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var edit_news_post_url = $(this).attr('data-target-url');
        $.ajax({
            type: 'GET',
            url: edit_news_post_url,
            success: function(data){
                $('#create-edit-news-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();
                installColorPicker();
                initCalendar();
                initSelectChosen();
            },
            statusCode: {
                404: function(data){
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-edit-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-edit-news-modal').modal('show');
                setTimeout(function(){
                    $('.chargementAjax').addClass('hidden');
                }, 1050);
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
     * *********************************************************************************************
     */
    $('#create-edit-news-modal').on('shown.bs.modal', function(){
        $(document).off('focusin.modal');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Duplication de publication
     * *********************************************************************************************
     */
    // duplication, appel de formulaire
    $(document).on('click', '.duplicate-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(data){
                $('#duplicate-news-modal').find('.modal-body-container').html(data.content);
                $('#duplicate-news-modal').find('.general-message').html('');
            },
            statusCode: {
                404: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#duplicate-news-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }

        });
    });

    // duplication, annulation
    $(document).on('click', '#duplicate-news-modal .cancel', function(e){
        e.preventDefault();
        $('#duplicate-news-modal').find('.modal-body-container').html('');
        $('#duplicate-news-modal').find('.error-message-container.general-message').text('');
        $('#duplicate-news-modal').modal('hide');
    });

    // duplication, soumission
    $(document).on('click', '#duplicate-news-modal .save.save-duplication', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var duplicate_template_url = $(this).attr('data-target-url');
        $('#duplicate-news-modal form').ajaxSubmit({
            type: 'POST',
            url: duplicate_template_url,
            success: function(data){
                if(data['error']){
                    $('#duplicate-news-modal').find('.modal-body-container').html(data.content);
                    $('#duplicate-news-modal').find('.general-message').html('');
                } else {
                    window.location.replace($('input[name=news_post_list_url]').val());
                }
            },
            error: function(){
                $('#duplicate-template-dialog').find('.close-modal').show();
            },
            statusCode: {
                404: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);

                },
                500: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }

        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Duplication de publication
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Publication / Dépublication
     * *********************************************************************************************
     */
    $(document).on('click', '.publish-unpublish-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(){
                window.location.replace($('input[name=news_post_list_url]').val());
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Publication / Dépublication
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Archivage, dans Actions
     * *********************************************************************************************
     */
    $(document).on('click', '.archive-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(){
                window.location.replace($('input[name=news_post_list_url]').val());
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Archivage, dans Actions
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Suppression, avec confirmation
     * *********************************************************************************************
     */
    // appel de popup de confirmation
    $(document).on('click', '.delete-news-post', function(e){
        e.preventDefault();
        var target_url = $(this).attr('data-target-url');
        $('#confirm-delete-news-modal').find('.confirm-delete').attr('data-target-url', target_url);
        $('#confirm-delete-news-modal').modal('show');
    });

    // suppression
    $(document).on('click', '#confirm-delete-news-modal .confirm-delete', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        if ($('input[name=archived_state]').length > 0 && 'true' == $('input[name=archived_state]').val()) {
            var redirection_url = $('input[name=archived_news_post_list_url]').val();
        } else {
            var redirection_url = $('input[name=news_post_list_url]').val();
        }
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(){
                window.location.replace(redirection_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Suppression, avec confirmation
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Restauration
     * *********************************************************************************************
     */
    $(document).on('click', '.restore-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        var redirection_url = $('input[name=archived_news_post_list_url]').val();
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(){
                window.location.replace(redirection_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    // restauration par groupe par bouton
    // soumission action de groupe
    $(document).on('click', '.restore-news-button', function(e){
        e.preventDefault();
        var arr_checked = getChecked();
        if (arr_checked.length > 0) {
            $('.chargementAjax').removeClass('hidden');
            var str_checked = arr_checked.join(',');
            var data = {'news_post_id_list': str_checked, 'grouped_action_type': 'restore'};
            var redirection_url = $('input[name=archived_news_post_list_url]').val();
            $.ajax({
                type: 'POST',
                url: $(this).attr('data-target-url'),
                data: data,
                success: function(){
                    window.location.replace(redirection_url);
                },
                complete: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Restauration
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Prévisualisation
     * *********************************************************************************************
     */
    $(document).on('click', '.preview-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-target-url');
        $.ajax({
            type: 'GET',
            url: target_url,
            success: function(data){
                $('#preview-news-modal').find('.modal-body-container').html(data.content);
            },
            statusCode: {
                404: function(data){
                    $('#preview-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#preview-news-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#preview-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#preview-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#preview-news-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    $('#preview-news-modal').on('hidden.bs.modal', function(){
        $(this).find('.error-message-container.general-message').text('');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Prévisualisation
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Prévisualisation, dans création/édition
     * *********************************************************************************************
     */
    $(document).on('input', '#create-edit-news-modal .news-post-title-input', function(){
        if ('' != $(this).val()) {
            $('#create-edit-news-modal').find('.preview-news-button').addClass('active');
        } else {
            $('#create-edit-news-modal').find('.preview-news-button').removeClass('active');
        }
    });

    $(document).on('click', '#create-edit-news-modal .delete-news-post-title-input', function(){
        $('#create-edit-news-modal').find('.preview-news-button').removeClass('active');
    });

    $(document).on('click', '#create-edit-news-modal .preview-news-button.active', function(e){
        e.preventDefault();
        updateTitle();
        updateContent();
        updateActionButton();
        updateDate();$('#instantaneous-preview-news-modal').modal('show');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Prévisualisation, dans création/édition
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Actions de groupe
     * *********************************************************************************************
     */
    $(document).on('change', '.post-data-container .styled-checkbox', function(){
        var checked = getChecked();
        var text = (checked.length == 1)?checked.length+" publication sélectionnée":((checked.length > 1)?checked.length+" publications sélectionnées":"");
        $(".selected-elements .selected-count input").val(text);
        if (checked.length > 0) {
            $('.selected-elements').css('display',"flex");
            $('.selected-elements .selected-count .delete-input').css('display','block');
        } else {
            $('.selected-elements').css('display',"none");
            $('.selected-elements').trigger('hide-block');
        }
    });

    $(document).on('click', '.selected-elements .selected-count .delete-input', function(){
        $('.post-data-container .styled-checkbox').each(function(){
            $(this).prop('checked', false);
        });
        $('.selected-elements').css('display',"none");
        $('.selected-elements').trigger('hide-block');
    });

    // Selection de type d'action de groupe
    $(document).on('click', '.grouped-action-choice', function(e){
        e.preventDefault();
        var selected_element_container = $(this).parents('.selected-elements-button-container');
        selected_element_container.find('button.dropdown-toggle').text($(this).text());
        selected_element_container.find('.button-container .btn-valider').attr('data-grouped-action', $(this).attr('data-grouped-action'));
    });

    $(document).on('hide-block', '.selected-elements', function(){
        var dropdown_toggle_button = $(this).find('.selected-elements-button-container').find('.dropdown').find('button.dropdown-toggle');
        dropdown_toggle_button.text(dropdown_toggle_button.attr('data-default-text'));
        $(this).find('.selected-elements-button-container').find('.button-container .btn-valider').attr('data-grouped-action', '');
    });

    // soumission action de groupe
    $(document).on('click', '.selected-elements .selected-elements-button-container .btn-valider', function(e){
        e.preventDefault();
        if ('undefined' !== typeof $(this).attr('data-grouped-action') && '' != $(this).attr('data-grouped-action')){
            $('.chargementAjax').removeClass('hidden');
            var arr_checked = getChecked();
            var str_checked = arr_checked.join(',');
            var data = {'news_post_id_list': str_checked, 'grouped_action_type': $(this).attr('data-grouped-action')};
            if ($('input[name=archived_state]').length > 0 && 'true' == $('input[name=archived_state]').val()) {
                var redirection_url = $('input[name=archived_news_post_list_url]').val();
            } else {
                var redirection_url = $('input[name=news_post_list_url]').val();
            }
            $.ajax({
                type: 'POST',
                url: $(this).attr('data-target-url'),
                data: data,
                success: function(){
                    window.location.replace(redirection_url);
                },
                complete: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Actions de groupe
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - Actualités
     * Incrémentation - Décrémentation nombre éléments de liste selectionnés
     * *********************************************************************************************
     */
    $(document).on('click', '.news-post-list .post-data-container .styled-checkbox', function(){
        if($(this).is(':checked')){
            checked.push($(this).attr('id'));
        } else {
            checked.splice(checked.indexOf($(this).attr('id')), 1);
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - Actualités
     * Incrémentation - Décrémentation nombre éléments de liste selectionnés
     * *********************************************************************************************
     */


});

function installWysiwyg()
{
    var ckeditor_config_general_path = $('input[name=ckeditor_config_general_path]').val();
    var text_area_list = $('textarea.large-textarea');
    text_area_list.each(function(){
        CKEDITOR.replace( $(this).attr('id'), {
            language: 'fr',
            uiColor: '#9AB8F3',
            height: 150,
            width: 600,
            customConfig: ckeditor_config_general_path
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

function initCalendar() {
    if ('undefined' != typeof $('#calendar')) {
        $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);//langue datepicker
        var date = $('.post-launch-date').val();
        $('#calendar').datepicker({
            minDate: new Date(),
            altField: ".post-launch-date",
            altFormat: "dd/mm/yy"
        });
        if ('undefined' != typeof date) {
            if ('' != date.trim()) {
                $('#calendar').datepicker('setDate', date);
            }
        }
    }
}

function initSelectChosen() {
    $(".chosen-select").chosen({//hour selectable
        disable_search: true,
        width: "70px"
    });
}

var checked = [];
function getChecked() {
    /*var checked = [];
    $(".post-data-container .styled-checkbox").each(function() {
        if ($(this).is(':checked')) {
            checked.push($(this).attr('id'));
        }
    });*/
    return checked;
}

// instantaneous preview functions
function updateTitle()
{
    var title = $('#create-edit-news-modal').find('form .news-post-title-input').val();
    $('#instantaneous-preview-news-modal').find('.lib-titre-block-centre').text(title);
}

function updateDate()
{
    var date = $('#create-edit-news-modal').find('input[name=instantaneous_preview_date]').val();
    $('#instantaneous-preview-news-modal').find('.lib-date-block-centre').text(date);
}

function updateContent()
{
    var content_textarea = $('#create-edit-news-modal').find('.news-post-content-textarea');
    var content_textarea_id = content_textarea.attr('id');
    var content = CKEDITOR.instances[content_textarea_id].getData();
    $('#instantaneous-preview-news-modal').find('.descr-block-centre').html(content);
}

function updateActionButton()
{
    var action_button_block_container = $('#create-edit-news-modal').find('.action-button-block-container');
    var button_container = $('#instantaneous-preview-news-modal').find('.button-container');
    if (action_button_block_container.is(':visible')) {
        var action_button_text = action_button_block_container.find('.action-button-text-input').val();
        var action_button_bg_color = action_button_block_container.find('.color-value.action-button-background-color').val();
        var action_button_text_color = action_button_block_container.find('.color-value.action-button-text-color').val();
        var action_button_target_url = action_button_block_container.find('.action-button-target-url-input').val();
        var action_button_target_page = action_button_block_container.find('.action-button-target-page-select')
            .find('option:selected').val();

        var button = button_container.find('.action-button-preview');
        button.text(action_button_text);
        button.css({
            'background-color': action_button_bg_color,
            'color': action_button_text_color
        });
        if('' != action_button_target_page.trim()){
            button.attr('href', action_button_target_page);
            button.attr('target', '_blank');
        } else if ('' != action_button_target_url){
            button.attr('href', action_button_target_url);
            button.attr('target', '_blank');
        } else{
            button.attr('href', '#');
            button.attr('target', '');
        }
        button_container.show();
    } else {
        button_container.hide();
    }
}
// fin- instantaneous preview functions


function addPostTypeLabelInAjaxData(existing_data)
{
    var news_post_type_label_data = {};
    if ($('input[name=welcoming_news_post_type]').length > 0 && 'true' == $('input[name=welcoming_news_post_type]').val()){
        news_post_type_label_data = {'news_post_type_label': $('input[name=welcoming_post_type_label]').val()}
    } else {
        news_post_type_label_data = {'news_post_type_label': $('input[name=standard_post_type_label]').val()}
    }
    var merged_data = $.extend({}, existing_data, news_post_type_label_data);
    return merged_data;
}