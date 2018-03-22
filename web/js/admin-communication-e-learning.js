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
        $('#create-edit-e-learning-modal').find('.original-data-holder-el').remove();

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
                    // window.location.replace(redirect_target);
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
        $('.content-block-list-container.media-container').find('.content-block-container').hide();
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

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Edition contenu de type media
     * *********************************************************************************************
     */
    // affichage formulaire
    $(document).on('click', '.edit.media-content', function(e){
        e.preventDefault();
        var content_index = $(this).parents('.content-list-element').attr('data-element-index');
        var content_block = $('.content-block-list-container.media-container').find('.content-block-container[data-block-index='+content_index+']');
        content_block.attr('data-manipulation-type', 'edit');
        createOriginalDataHolder(content_block);
        content_block.find('.add-content-button-container .edit-media').show();
        content_block.find('.add-content-button-container .add-media').hide();
        content_block.show();
    });

    // validation de l'édition
    $(document).on('click', '.btn-valider.edit-media', function(e){
        e.preventDefault();
        var content_block_container = $(this).parents('.content-block-container');
        content_block_container.find('.document-name-error-message').text('');
        var media_name = content_block_container.find('.document-name-container').find('input').not('.original-data-holder-el').val().trim();
        if ('' != media_name) {
            var content_block_index = content_block_container.attr('data-block-index');
            var corresponding_content_element = $('.media-content-list-container').find('.content-list-element[data-element-index='+content_block_index+']');
            corresponding_content_element.find('.content-denomination-name div').text(media_name);
            content_block_container.hide();
        } else {
            content_block_container.find('.document-name-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    // annulation de l'édition, par fermeture de block
    $(document).on('click', '.delete-media-block', function(e){
        e.preventDefault();
        var content_block_container = $(this).parents('.content-block-container');
        if('edit' == content_block_container.attr('data-manipulation-type')){
            resetFormElementToOriginalData(content_block_container);
            setAssociatedFileButtonText(content_block_container);
            setDeleteNameInputVisibility(content_block_container);
            setFormElementVisibilityOnEdit(content_block_container);
            content_block_container.hide();
        } else if ('create' == content_block_container.attr('data-manipulation-type')) {
            content_block_container.remove();
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Edition contenu de type media
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
        var content_block_list = $('.content-block-list-container.media-container').find('.content-block-container');
        var index_list = [];
        content_block_list.each(function(){
            index_list.push(parseInt($(this).attr('data-block-index')));
        });
        current_index = (Math.max.apply(null, index_list)) + 1;
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
    content_list_element.attr('data-element-index', content_block_container.attr('data-block-index'));

    var media_content_list_container = $('.media-content-list-container');
    media_content_list_container.append(content_list_element);
}

function createOriginalDataHolder(content_block_container)
{
    content_block_container.find('.original-data-holder-container').html('');
    var form_element_list = content_block_container.find('.form-el');
    form_element_list.each(function(){
        var original_data_holder = $(this).clone();

        var original_data_holder_id = original_data_holder.attr('id');
        original_data_holder_id = original_data_holder_id + '__origin__';
        original_data_holder.attr('id', original_data_holder_id);

        var original_data_holder_name = original_data_holder.attr('name');
        original_data_holder_name = original_data_holder_name + '__origin__';
        original_data_holder.attr('name', original_data_holder_name);

        original_data_holder.addClass('original-data-holder-el');
        var original_data_holder_container = content_block_container.find('.original-data-holder-container');
        original_data_holder_container.append(original_data_holder);
        // $(this).before(original_data_holder);
        original_data_holder.hide();
    });
}

function resetFormElementToOriginalData(content_block_container)
{
    var current_data_holder_list = content_block_container.find('.form-el').not('.original-data-holder-el');
    current_data_holder_list.each(function(){
        var current_data_holder_id = $(this).attr('id');
        var current_data_holder_name = $(this).attr('name');
        var corresponding_original_data_holder = content_block_container.find('.original-data-holder-container').find('#'+current_data_holder_id+'__origin__');
        $(this).after(corresponding_original_data_holder);
        $(this).remove();
        corresponding_original_data_holder.attr('id', current_data_holder_id);
        corresponding_original_data_holder.attr('name', current_data_holder_name);
        corresponding_original_data_holder.removeClass('original-data-holder-el');

        corresponding_original_data_holder.not('.hidden-input-file').show();
    });
}

function setAssociatedFileButtonText(content_block_container)
{
    var associated_file_name = content_block_container.find('.hidden-input-file.associated-file').val();
    if ('' != associated_file_name) {
        content_block_container.find('.upload-img-button-container .img-name-container').text(associated_file_name.split('\\').pop());
    }
}

function setDeleteNameInputVisibility(content_block_container)
{
    var document_name_block = content_block_container.find('.document-name-container .document-name')
    var content_name = document_name_block.find('input').val();
    if ('' !== content_name) {
        document_name_block.find('.delete-input').show();
    }
}

function setFormElementVisibilityOnEdit(content_block_container){
    var associated_file_name = content_block_container.find('.hidden-input-file.associated-file').val();
    if ('' != associated_file_name) {
        content_block_container.find('.btn-upload').addClass('hidden-button');
        content_block_container.find('.upload-img-button-container').removeClass('hidden-button');
        content_block_container.find('.associated-file-info').hide();
    }
    content_block_container.find('.document-name-container').show();
    content_block_container.find('.add-content-button-container').show();
}