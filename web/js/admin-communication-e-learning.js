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
        /*$('.chargementAjax').removeClass('hidden');

        // removing useless block for data saving (model, form prototype, etc)
        $('#create-edit-e-learning-modal').find('.block-model-container').remove();
        var div_list = $('#create-edit-e-learning-modal').find('div');
        div_list.each(function(){
            if ('undefined' !== typeof $(this).attr('data-prototype')) {
                $(this).remove();
            }
        });
        $('#create-edit-e-learning-modal').find('.original-data-holder-el').remove();

        // closing all opened edit block (not saved) to restore to original datas
        closingAllOpenedEdit();

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
        });*/
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
        // removing all previously opened content block, in add
        removingAllOpenedOnCreate();

        // closing all edit in progress, restoring original datas
        var content_block_list = $('.content-block-list-container.media-container').find('.content-block-container:visible');
        content_block_list.each(function(){
            resetFormElementToOriginalData($(this));
            $(this).hide();
        });

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

    // choix de type de document à ajouter
    $(document).on('click', '.document-type-element', function(e){
        e.preventDefault();
        // closing all previously opened content block, in edit
        closingAllOpenedEdit();

        // removing all previously opened content block, in add
        removingAllOpenedOnCreate();

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
            closingAllImageOpenedEdit(content_block_container);
            removingAllImageOpenedOnCreate(content_block_container);
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
        // closing all previously opened content block, edit
        closingAllOpenedEdit();

        // removing all previously opened content block, in add
        removingAllOpenedOnCreate();

        // continuing process
        var content_index = $(this).parents('.content-list-element').attr('data-element-index');
        var content_block = $('.content-block-list-container.media-container').find('.content-block-container[data-block-index='+content_index+']');
        if (!content_block.is(':visible')) {
            content_block.attr('data-manipulation-type', 'edit');
            createOriginalDataHolder(content_block);
            content_block.find('.add-content-button-container .edit-media').show();
            content_block.find('.add-content-button-container .add-media').hide();
            content_block.show();

            // for gallery image media type
            // adding new temporary image block if there is not yet one
            if(content_block.hasClass('media-image-gallery-block')){
                var temporary_image_block_container = content_block.find('.temporary-image-block-container');
                if (temporary_image_block_container.find('.gallery-image-element').length <= 0){
                    addGalleryImageTemporary(content_block);
                }
            }
        }
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
            closingAllImageOpenedEdit(content_block_container);
            deletingElementAndBlockWithToDeleteFlagOn(content_block_container)
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
            removingAllImageOpenedOnCreate(content_block_container);
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

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Manipulation image de galerie
     * *********************************************************************************************
     */
    // bouton upload
    $(document).on('change', '.hidden-input-file.image-file', function(){
        if('' == $(this).val().trim()){
            $(this).parents('.gallery-image-element').find('.image-file-info').show();
            $(this).parents('.gallery-image-element').find('.image-file-name-container').hide();
            $(this).parents('.gallery-image-element').find('.add-image-button-container').hide();
            $(this).parents('.gallery-image-element').find('.image-file-name-container').trigger('block-hide');
        } else {
            $(this).parents('.gallery-image-element').find('.image-file-info').hide();
            $(this).parents('.gallery-image-element').find('.image-file-name-container').show();
            $(this).parents('.gallery-image-element').find('.add-image-button-container').show();
        }
    });

    // reset bouton d'upload image
    $(document).on('input-file-reset', '.hidden-input-file.image-file', function(){
        $(this).parents('.gallery-image-element').find('.image-file-info').show();
        $(this).parents('.gallery-image-element').find('.image-file-name-container').hide();
        $(this).parents('.gallery-image-element').find('.add-image-button-container').hide();
        $(this).parents('.gallery-image-element').find('.image-file-name-container').trigger('block-hide');
    });

    // --------------------------------------------------------------------------------------------
    // validation ajout image
    // --------------------------------------------------------------------------------------------
    $(document).on('click', '.btn-valider.add-gallery-image-element', function(e){
        e.preventDefault();
        var image_block = $(this).parents('.gallery-image-element');
        image_block.find('.image-name-error-message').text('');
        var image_name = image_block.find('.image-file-name-container').find('input').val().trim();
        if ('' != image_name) {
            addImageBlock(image_block);
        } else {
            image_block.find('.image-name-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    // suppression erreur lors de suppression fichier à uploader
    $(document).on('block-hide', '.image-file-name-container', function(){
        var image_block = $(this).parents('.gallery-image-element');
        image_block.find('.image-name-error-message').text('');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Manipulation image de galerie
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Suppression media
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-media', function(e){
        e.preventDefault();
        var content_element = $(this).parents('.content-list-element');
        var content_index = content_element.attr('data-element-index');
        $('.content-block-list-container.media-container').find('.content-block-container[data-block-index='+content_index+']').remove();
        content_element.remove();
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Suppression media
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Edition image de galerie
     * *********************************************************************************************
     */
    // ouverture formulaire
    $(document).on('click', '.edit.gallery-image', function(e){
        e.preventDefault();
        var image_block_index = $(this).parents('.content-list-element.image-gallery').attr('data-element-index');
        var image_block = $(this).parents('.content-list-element.image-gallery').parents('.content-block-container')
            .find('.image-block-container').find('.gallery-image-element[data-block-index='+image_block_index+']');
        var content_block = image_block.parents('.content-block-container');

        // setting form element visibility
        setImageFormElementVisibilityOnEdit(image_block)

        // closing all previously opened image block, edit
        closingAllImageOpenedEdit(content_block);

        // removing all previously opened image block, add
        removingAllImageOpenedOnCreate(content_block);

        // processing edit
        image_block.attr('data-manipulation-type', 'edit');
        createOriginalImageDataHolder(image_block);
        image_block.find('.add-image-button-container .edit-gallery-image-element').show();
        image_block.find('.add-image-button-container .add-gallery-image-element').hide();
        image_block.show();
    });

    // soumission édition
    $(document).on('click', '.edit-gallery-image-element', function(e){
        e.preventDefault();
        var image_block = $(this).parents('.gallery-image-element');
        image_block.find('.image-name-error-message').text('');
        var image_name = image_block.find('.image-file-name-container').find('input').not('.original-image-data-holder-el').val().trim();
        if ('' != image_name) {
            var image_block_index = image_block.attr('data-block-index');
            var corresponding_image_element = image_block.parents('.content-block-container')
                .find('.image-element-list-container')
                .find('.content-list-element.image-gallery[data-element-index='+image_block_index+']');
            corresponding_image_element.find('.content-denomination-name div').text(image_name);
            image_block.hide();
            addGalleryImageTemporary(image_block.parents('.content-block-container'));
        } else {
            image_block.find('.image-name-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Edition image de galerie
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Suppression image de galerie
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-gallery-image', function(e){
        e.preventDefault();
        var image_element = $(this).parents('.content-list-element.image-gallery');
        var data_element_index = image_element.attr('data-element-index');
        var content_block = $(this).parents('.content-block-container');
        var corresponding_image_block = content_block.find('.image-block-container .gallery-image-element[data-block-index='+data_element_index+']');

        // simulate deleting by adding "to-delete-image-element" and "to-delete-image-block"
        // and processing real removing when validating edit
        image_element.addClass('to-delete-image-element');
        image_element.hide();
        corresponding_image_block.addClass('to-delete-image-block');
        corresponding_image_block.hide();

        // adding new temporary image block if there is not yet one
        var temporary_image_block_container = content_block.find('.temporary-image-block-container');
        if (temporary_image_block_container.find('.gallery-image-element').length <= 0){
            addGalleryImageTemporary(content_block);
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Suppression image de galerie
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

function addMediaVideoBlock()
{
    var form_content = addMediaFormBlockBases();
    form_content.addClass('media-video-block');
    form_content.addClass('video');
    form_content.find('input.content-type-input').val($('input[name=e_learning_content_type_video]').val());
    form_content.find('.document-name-container').show();
    form_content.find('.document-name-container label').text('nom de la vidéo');
    form_content.find('.add-content-button-container').show();

    return form_content;
}

function addImageGalleryBlock()
{
    var form_content = addMediaFormBlockBases();
    form_content.addClass('media-image-gallery-block');
    form_content.find('input.content-type-input').val($('input[name=e_learning_content_type_image_gallery]').val());
    form_content.find('.document-name-container').show();
    form_content.find('.document-name-container label').text('nom de la galerie');
    addGalleryImageTemporary(form_content);

    return form_content;
}

function addGalleryImageTemporary(content_block_container)
{
    var gallery_image_block = $('.block-model-container').find('.gallery-image-element').clone();
    content_block_container.find('.temporary-image-block-container').append(gallery_image_block);
}

function addMediaFormBlockTemporary(media_type)
{
    var form_content = null
    if ('document' == media_type) {
        form_content = addMediaDocumentFormBlock()
    } else if ('video' == media_type){
        form_content = addMediaVideoBlock();
    } else if ('image-gallery' == media_type) {
        form_content = addImageGalleryBlock();
    }
    if (null !== form_content) {
        // var media_list_container = $('.content-block-list-container.media-container');
        var temp_media_list_container = $('.temporary-content-block-list-container.media');
        temp_media_list_container.append(form_content);
    }
}

function addMediaFormBlock(content_block_container)
{
    var media_list_container = $('.content-block-list-container.media-container');

    // getting current index
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

    // replacing default form element id and name by real ones (indexed instead of __name__-ed)
    var form_el_list = content_block_container.find('.form-el');
    form_el_list.each(function(){
        var id = $(this).attr('id');
        id = id.replace(/__name__/g, current_index);
        $(this).attr('id', id);
        var name = $(this).attr('name');
        name = name.replace(/__name__/g, current_index);
        $(this).attr('name', name);
    });

    // setting block index
    content_block_container.attr('data-block-index', current_index);

    // setting content order
    var media_content_list_container = $('.media-content-list-container');
    var current_order = media_content_list_container.find('.content-list-element').length + 1;
    content_block_container.find('.content-order-input').val(current_order);

    // setting manipulation type
    content_block_container.attr('data-manipulation-type', 'edit');

    // adding media block
    media_list_container.append(content_block_container);
    content_block_container.hide();

    // create media list element (preview-like in list)
    createEquivalentMediaListElement(content_block_container);
}

function createEquivalentMediaListElement(content_block_container)
{
    var content_list_element = $('.block-model-container').find('.content-list-element.media').clone();
    content_list_element.find('.content-denomination-name').find('div').text(content_block_container.find('.document-name').find('input').val());
    content_list_element.attr('data-element-index', content_block_container.attr('data-block-index'));

    // changing content element icon
    if (content_block_container.hasClass('media-document-block')) {
        content_list_element.find('.content-denomination .content-denomination-image span').attr('class', 'document-icon');
    } else if (content_block_container.hasClass('media-video-block')) {
        content_list_element.find('.content-denomination .content-denomination-image span').attr('class', 'video-icon');
    } else if (content_block_container.hasClass('media-image-gallery-block')) {
        content_list_element.find('.content-denomination .content-denomination-image span').attr('class', 'gallery-icon');
    }

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

function createOriginalImageDataHolder(image_block)
{
    image_block.find('.original-image-data-holder-container').html('');
    var form_element_list = image_block.find('.form-el');
    form_element_list.each(function(){
        var original_data_holder = $(this).clone();

        var original_data_holder_id = original_data_holder.attr('id');
        original_data_holder_id = original_data_holder_id + '__origin__';
        original_data_holder.attr('id', original_data_holder_id);

        var original_data_holder_name = original_data_holder.attr('name');
        original_data_holder_name = original_data_holder_name + '__origin__';
        original_data_holder.attr('name', original_data_holder_name);

        original_data_holder.addClass('original-image-data-holder-el');
        var original_image_data_holder_container = image_block.find('.original-image-data-holder-container');
        original_image_data_holder_container.append(original_data_holder);
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

    if (content_block_container.hasClass('media-image-gallery-block')) {
        var image_block_list = content_block_container.find('.image-block-container').find('.gallery-image-element');
        image_block_list.each(function(){
            var image_block_index = $(this).attr('data-block-index');
            var image_name = $(this).find('.image-file-name-container .document-name input').val();
            var corresponding_element = content_block_container.find('.image-element-list-container .content-list-element.image-gallery[data-element-index='+image_block_index+']');
            corresponding_element.find('.content-denomination-name div').text(image_name);
            $(this).hide();
        });
    }

    removeToDeleteFlagOnImageElementAndBlock(content_block_container);
}

function removeToDeleteFlagOnImageElementAndBlock(content_block_container)
{
    var to_delete_element_list = content_block_container.find('.image-element-list-container .content-list-element.image-gallery.to-delete-image-element');
    to_delete_element_list.each(function(){
        $(this).removeClass('to-delete-image-element');
        $(this).show();
    });

    var to_delete_block_list = content_block_container.find('.image-block-container .gallery-image-element.to-delete-image-block');
    to_delete_block_list.each(function(){
        $(this).removeClass('to-delete-image-block');
    });
}

function deletingElementAndBlockWithToDeleteFlagOn(content_block_container)
{
    var to_delete_element_list = content_block_container.find('.image-element-list-container .content-list-element.image-gallery.to-delete-image-element');
    to_delete_element_list.remove();
    var to_delete_block_list = content_block_container.find('.image-block-container .gallery-image-element.to-delete-image-block');
    to_delete_block_list.remove();
}

function resetImageFormElementToOriginalData(image_block)
{
    var current_data_holder_list = image_block.find('.form-el').not('.original-image-data-holder-el');
    current_data_holder_list.each(function(){
        var current_data_holder_id = $(this).attr('id');
        var current_data_holder_name = $(this).attr('name');
        var corresponding_original_data_holder = image_block.find('.original-image-data-holder-container')
            .find('#'+current_data_holder_id+'__origin__');
        $(this).after(corresponding_original_data_holder);
        $(this).remove();
        corresponding_original_data_holder.attr('id', current_data_holder_id);
        corresponding_original_data_holder.attr('name', current_data_holder_name);
        corresponding_original_data_holder.removeClass('original-image-data-holder-el');
        corresponding_original_data_holder.not('.hidden-input-file').show();
    });
    setImagFileButtonText(image_block)
}

function setAssociatedFileButtonText(content_block_container)
{
    var associated_file_name = content_block_container.find('.hidden-input-file.associated-file').val();
    if ('' != associated_file_name) {
        content_block_container.find('.upload-img-button-container .img-name-container').text(associated_file_name.split('\\').pop());
    }
}

function setImagFileButtonText(image_block)
{
    var file_name = image_block.find('.hidden-input-file.image-file').val();
    if ('' != file_name) {
        image_block.find('.upload-img-button-container .img-name-container').text(file_name.split('\\').pop());
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
        content_block_container.find('.upload-img-button-container').next('.input-file-delete-link').show();
    }

    var video_url = content_block_container.find('.video-url-input').val();
    if ('' != video_url) {
        content_block_container.find('.video-url-input').next('.delete-input').show();
    }
    content_block_container.find('.document-name-container').show();
    content_block_container.find('.add-content-button-container').show();
}

function setImageFormElementVisibilityOnEdit(image_block)
{
    var image_name = image_block.find('.hidden-input-file.image-file').val();
    console.log(image_name);
    if ('' != image_name) {
        image_block.find('.btn-upload').addClass('hidden-button');
        image_block.find('.upload-img-button-container').removeClass('hidden-button');
        image_block.find('.associated-image-file-info').hide();
        image_block.find('.upload-img-button-container').next('.input-file-delete-link').show();
    }

    image_block.find('.image-file-name-container').show();
    if ('' != image_block.find('.image-file-name-container').find('.document-name input').val()) {
        image_block.find('.image-file-name-container').find('.document-name input').next('.delete-input').show();
    }
    image_block.find('.add-image-button-container').show();
}

function closingAllOpenedEdit()
{
    $('.content-block-list-container.media-container').find('.content-block-container:visible').find('.delete-block').click();
}

function removingAllOpenedOnCreate()
{
    $('.temporary-content-block-list-container.media').find('.content-block-container').remove();
}

function removingAllImageOpenedOnCreate(content_block)
{
    content_block.find('.temporary-image-block-container').find('.gallery-image-element').remove();
}

function closingAllImageOpenedEdit(content_block)
{
    var visible_image_block = content_block.find('.image-block-container').find('.gallery-image-element:visible');
    visible_image_block.each(function(){
        resetImageFormElementToOriginalData($(this));
        $(this).hide();
    });
}

function addImageBlock(image_block)
{
    var image_block_container =  image_block.parents('.content-block-container').find('.image-block-container');

    // getting current index
    var current_index = null;
    if (image_block_container.find('.gallery-image-element').length <= 0) {
        current_index = 1;
    } else {
        var image_block_list = image_block_container.find('.gallery-image-element');
        var index_list = [];
        image_block_list.each(function(){
            index_list.push(parseInt($(this).attr('data-block-index')));
        });
        current_index = (Math.max.apply(null, index_list)) + 1;
    }

    // replacing default form element id and name by real ones (indexed instead of __name__-ed)
    var form_el_list = image_block.find('.form-el');
    form_el_list.each(function(){
        var id = $(this).attr('id');
        id = id.replace(/__image_name__/g, current_index);
        $(this).attr('id', id);
        var name = $(this).attr('name');
        name = name.replace(/__image_name__/g, current_index);
        $(this).attr('name', name);
    });

    // setting block index
    image_block.attr('data-block-index', current_index);

    // setting content order
    var current_order = image_block_container.length + 1;
    image_block.find('.image-order-input').val(current_order);

    // setting manipulation type
    image_block.attr('data-manipulation-type', 'edit');

    // adding media block
    image_block_container.append(image_block);
    image_block.hide();

    // create media list element (preview-like in list)
    createEquivalentImageListElement(image_block);

    // adding new temporary image block
    addGalleryImageTemporary(image_block.parents('.content-block-container'));


    // showing "add gallery" button
    image_block.parents('.content-block-container').find('.add-content-button-container').show();
}

function createEquivalentImageListElement(image_block)
{
    var image_list_element = $('.block-model-container').find('.content-list-element.image-gallery').clone();
    image_list_element.find('.content-denomination-name').find('div').text(image_block.find('.document-name').find('input').val());
    image_list_element.attr('data-element-index', image_block.attr('data-block-index'));

    var image_element_list_container = image_block.parents('.content-block-container').find('.image-element-list-container');
    image_element_list_container.append(image_list_element);
}