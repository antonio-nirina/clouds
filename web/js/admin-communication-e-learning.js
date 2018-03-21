$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */
    installJPlist();
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning
     * *********************************************************************************************
     */
    // appel de formulaire
    $('.create-e-learning-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_e_learning_url = $('input[name=create_e_learning_url]').val();
        $.ajax({
            type: 'GET',
            url: create_e_learning_url,
            success: function(data){
                $('#create-edit-e-learning-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();
                installColorPicker();
            },
            statusCode: {
                404: function(data){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-edit-e-learning-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    // soumission de formulaire
    $(document).on('click', '#create-edit-e-learning-modal .submit-block-container .btn-valider', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');

        // removing useless block for data saving (model, form prototype, etc)
        $('#create-edit-e-learning-modal').find('.block-model-container').remove();
        var div_list = $('#create-edit-e-learning-modal').find('div');
        div_list.each(function(){
            if ('undefined' !== typeof $(this).attr('data-prototype')) {
                $(this).remove();
            }
        });
        $('#create-edit-e-learning-modal').find('.not-yet-added').remove();

        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
        var submission_type = $(this).attr('data-submission-type');
        var data = {'submission_type': submission_type};
        var target_url = $('input[name=create_e_learning_url]').val();
        var redirect_target = $('input[name=e_learning_list]').val();
        $('#create-edit-e-learning-modal form').ajaxSubmit({
            type: 'POST',
            url: target_url,
            data: data,
            success: function(data){
                if(data['error']){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html(data.content);
                    installWysiwyg();
                    installColorPicker();
                } else {
                    window.location.replace(redirect_target);
                }
            },
            statusCode: {
                404: function(){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
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
     * Communication - E-learning
     * Création e-learning
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Suppression bloc
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-block', function(e){
        e.preventDefault();
        $(this).parents('.content-block-container').hide();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Suppression bloc
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Ajout contenu de type media
     * *********************************************************************************************
     */
    // appel choix
    $(document).on('click', '.add-media-content', function(e){
        e.preventDefault();
        $('.add-media-content-block').show();
    });

    // bouton upload image
    $(document).on('change', '.hidden-input-file.associated-file', function(){
        if('' == $(this).val().trim()){
            $(this).parents('.content-block-container').find('.associated-file-info').show();
            $(this).parents('.content-block-container').find('.document-name-container').hide();
            $(this).parents('.content-block-container').find('.add-content-button-container').hide();
            $(this).parents('.content-block-container').find('.document-name-container').trigger('block-hide');
        } else {
            $(this).parents('.content-block-container').find('.associated-file-info').hide();
            $(this).parents('.content-block-container').find('.document-name-container').show();
            $(this).parents('.content-block-container').find('.add-content-button-container').show();
        }
    });

    // reset bouton d'upload image
    $(document).on('input-file-reset', '.hidden-input-file.associated-file', function(){
        $(this).parents('.content-block-container').find('.associated-file-info').show();
        $(this).parents('.content-block-container').find('.document-name-container').hide();
        $(this).parents('.content-block-container').find('.add-content-button-container').hide();
        $(this).parents('.content-block-container').find('.document-name-container').trigger('block-hide');
    });

    // choix de type de document
    $(document).on('click', '.document-type-element', function(e){
        e.preventDefault();
        if ('undefined' !== typeof $(this).attr('data-media-type')) {
            addMediaFormBlockTemporary($(this).attr('data-media-type'));
            $(this).parents('.content-block-container').hide();
        }
    });

    // création de rendu
    $(document).on('click', '.btn-valider.add-media', function(e){
        e.preventDefault();
        var content_block_container = $(this).parents('.content-block-container');
        content_block_container.find('.document-name-error-message').text('');
        var media_name = content_block_container.find('.document-name-container').find('input').val().trim();
        if ('' != media_name) {
            addMediaFormBlock(content_block_container);
        } else {
            content_block_container.find('.document-name-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    // suppression erreur lors de suppression fichier à uploader
    $(document).on('block-hide', '.document-name-container', function(){
        var content_block_container = $(this).parents('.content-block-container');
        content_block_container.find('.document-name-error-message').text('');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Ajout contenu de type media
     * *********************************************************************************************
     */
});

function addMediaFormBlockBases()
{
    var form_content_model = $('.block-model-container').find('.content-block-container').clone();
    // form_content_model.attr('data-block-index', current_index);
    var html_form_content_model = form_content_model.wrap('<div class="wrapper"></div>').parent().html();
    // html_form_content_model = html_form_content_model.replace(/__name__/g, current_index);
    form_content_model = $(html_form_content_model);

    return form_content_model;
}

function addMediaDocumentFormBlock()
{
    var form_content = addMediaFormBlockBases();
    form_content.addClass('media-document-block');
    form_content.find('input.content-type-input').val($('input[name=e_learning_content_type_document]').val());

    return form_content;
}

function addMediaFormBlockTemporary(media_type)
{
    var form_content = null
    if ('document' == media_type) {
        form_content = addMediaDocumentFormBlock()
    }
    if (null !== form_content) {
        // var media_list_container = $('.content-block-list-container.media-container');
        var temp_media_list_container = $('.temporary-content-block-list-container');
        temp_media_list_container.append(form_content);
    }
}

function addMediaFormBlock(content_block_container)
{
    var media_list_container = $('.content-block-list-container.media-container');

    var current_index = null;
    if ($('.content-block-list-container.media-container').find('.content-block-container').length <= 0){
        current_index = 1;
    } else {
        current_index = parseInt($('.content-block-list-container.media-container').find('.content-block-container').last().attr('data-block-index')) + 1;
    }

    var form_el_list = content_block_container.find('.form-el');
    form_el_list.each(function(){
        var id = $(this).attr('id');
        id = id.replace(/__name__/g, current_index);
        $(this).attr('id', id);
        var name = $(this).attr('name');
        name = name.replace(/__name__/g, current_index);
        $(this).attr('name', name);
    });
    content_block_container.attr('data-block-index', current_index);

    var media_content_list_container = $('.media-content-list-container');
    var current_order = media_content_list_container.find('.content-list-element').length + 1;
    content_block_container.find('.content-order-input').val(current_order);

    media_list_container.append(content_block_container);
    content_block_container.hide();
    createEquivalentMediaListElement(content_block_container);
}

function createEquivalentMediaListElement(content_block_container)
{
    var content_list_element = $('.block-model-container').find('.content-list-element').clone();
    content_list_element.find('.content-denomination-name').find('div').text(content_block_container.find('.document-name').find('input').val());

    var media_content_list_container = $('.media-content-list-container');
    media_content_list_container.append(content_list_element);
}