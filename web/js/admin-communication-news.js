$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */
    // création actu, appel formulaire
    $('.create-news-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_news_post_url = $('input[name=create_news_post_url]').val();
        $.ajax({
            type: 'GET',
            url: create_news_post_url,
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
                    window.location.replace($('input[name=news_post_list_url]').val());
                }
            },
            statusCode: {
                404: function(){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Contenu non trouvé');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur interne');
                    $('#create-template-dialog').find('.modal-body-container').html('');
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
     * Paramétrages - Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
     * *********************************************************************************************
     */
    $('#create-edit-news-modal').on('shown.bs.modal', function(){
        $(document).off('focusin.modal');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Actualités
     * Activation de pagination au chargement de la page de liste de post
     * *********************************************************************************************
     */
    setPagination();
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Actualités
     * Activation de pagination au chargement de la page de liste de post
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
    $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);//langue datepicker
    $('#calendar').datepicker({
        minDate: new Date(),
        altField: ".post-launch-date",
        altFormat: "dd/mm/yy"
    });
}

function initSelectChosen() {
    $(".chosen-select").chosen({//hour selectable
        disable_search: true,
        width: "70px"
    });
}

function setPagination(){
    $('#news-post-list').paginate({
        limit: 5,
        childrenSelector: '.row.news-post-element',
        previous: false,
        first: false,
        next: false,
        lastText: 'dernier',
        navigationWrapper: $('.pagination-container')
    });
}