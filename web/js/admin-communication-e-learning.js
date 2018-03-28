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
                // installColorPicker();
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

        // closing opened edit
        closingAllOpenedEdit();

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
            setMediaReorderingFeatureStatus();
            removeFlagOnNewNotSavedImage(content_block_container);
            defineMediaOrder();
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
            content_block.find('.document-name-error-message').text('');
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
            deletingElementAndBlockWithToDeleteFlagOn(content_block_container);
            removeFlagOnNewNotSavedImage(content_block_container);
            content_block_container.hide();
        } else {
            content_block_container.find('.document-name-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    // annulation de la création OU édition, par fermeture de block
    $(document).on('click', '.delete-media-block', function(e){
        e.preventDefault();
        var content_block_container = $(this).parents('.content-block-container');
        if('edit' == content_block_container.attr('data-manipulation-type')){
            resetFormElementToOriginalData(content_block_container);
            setAssociatedFileButtonText(content_block_container);
            setDeleteNameInputVisibility(content_block_container);
            setFormElementVisibilityOnEdit(content_block_container);
            removingAllImageOpenedOnCreate(content_block_container);
            deleteNewNotSavedImage(content_block_container);
            content_block_container.hide();
            setGalleryImageReorderingFeatureStatus(content_block_container);
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
            var content_block = image_block.parents('.content-block-container');
            setGalleryImageReorderingFeatureStatus(content_block);
            defineImageOrder(content_block);
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
        setMediaReorderingFeatureStatus();
        defineMediaOrder();
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

        setGalleryImageReorderingFeatureStatus(content_block);
        defineImageOrder(content_block);
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Suppression image de galerie
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Création quiz
     * *********************************************************************************************
     */
    // appel formulaire
    $(document).on('click', '.add-quiz-content', function(e){
        e.preventDefault();
        // closing all previously opened edit
        closingAllOpenedQuizEdit();

        // removing all previously opened content block, in add
        removingAllOpenedQuizOnCreate();

        addQuizBlockTemporary();
    });

    // soumission ajout quiz
    $(document).on('click', '.btn-valider.add-quiz', function(e){
        e.preventDefault();
        var quiz_content_block = $(this).parents('.content-block-container.quiz-block');
        var quiz_select = quiz_content_block.find('.styled-choice-select.quiz-select').find('.hidden-select');
        if('' != quiz_select.find('option:selected').val()){
            quiz_content_block.find('.quiz-selection-error-message').text('');
            addQuizFormBlock(quiz_content_block);
            setQuizReorderingFeatureStatus();
            defineQuizOrder();
        } else {
            quiz_content_block.find('.quiz-selection-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    // annulation de la création OU édition, par fermeture de block
    $(document).on('click', '.delete-quiz-block', function(e){
        e.preventDefault();
        var content_block_container = $(this).parents('.content-block-container');
        if('edit' == content_block_container.attr('data-manipulation-type')){
            resetFormElementToOriginalData(content_block_container);
            content_block_container.hide();
        } else if ('create' == content_block_container.attr('data-manipulation-type')) {
            content_block_container.remove();
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Création quiz
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Edition quiz
     * *********************************************************************************************
     */
    // appel formulaire
    $(document).on('click', '.edit-quiz-content', function(e){
        e.preventDefault();

        // closing all previously opened edit
        closingAllOpenedQuizEdit();

        // removing all previously opened content block, in add
        removingAllOpenedQuizOnCreate();

        // continuing process
        var content_index = $(this).parents('.content-list-element').attr('data-element-index');
        var content_block = $('.content-block-list-container.quiz-container').find('.content-block-container[data-block-index='+content_index+']');
        if (!content_block.is(':visible')) {
            content_block.attr('data-manipulation-type', 'edit');
            content_block.find('.quiz-selection-error-message').text('');
            createOriginalDataHolder(content_block);
            setSelectedOptionInOriginalDataHolder(content_block);
            resetStyledChoiceSelectToOriginalData(content_block);
            content_block.find('.add-content-button-container .edit-quiz').show();
            content_block.find('.add-content-button-container .add-quiz').hide();
            content_block.show();
        }
    });

    // soumission d'édition
    $(document).on('click', '.btn-valider.edit-quiz', function(e){
        e.preventDefault();
        var quiz_content_block = $(this).parents('.content-block-container.quiz-block');
        var quiz_select = quiz_content_block.find('.styled-choice-select.quiz-select').find('.hidden-select');
        if('' != quiz_select.find('option:selected').val()){
            quiz_content_block.find('.quiz-selection-error-message').text('');
            updateQuizBlock(quiz_content_block);
        } else {
            quiz_content_block.find('.quiz-selection-error-message').text('Cette valeur ne doit pas être vide.');
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Edition quiz
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - suppression quiz
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-quiz-content', function(e){
        e.preventDefault();
        var quiz_element = $(this).parents('.content-list-element');
        var content_index = quiz_element.attr('data-element-index');
        var corresponding_content_block = $('.content-block-list-container.quiz-container').find('.content-block-container[data-block-index='+content_index+']');
        corresponding_content_block.remove();
        quiz_element.remove();
        setQuizReorderingFeatureStatus();
        defineQuizOrder();
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - suppression quiz
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - ajout bouton d'action
     * *********************************************************************************************
     */
    // appel formulaire
    $(document).on('click', '.add-button-content', function(e){
        e.preventDefault();
        addButtonBlockTemporary();
        installColorPicker();
        $(this).parents('.content-button-container').hide();
    });

    // soumission ajout
    $(document).on('click', '.btn-valider.add-button', function(e){
        e.preventDefault();
        var button_content_block = $(this).parents('.content-block-container.action-button-block-container');
        var button_text = button_content_block.find('.action-button-text-input');
        if ('' != button_text.val().trim()) {
            button_content_block.find('.button-text-error-message').text('');
            addActionButtonFormBlock(button_content_block);
        } else {
            button_content_block.find('.button-text-error-message').text('Cette valeur ne doit pas être vide.')
        }
    });

    // annulation de la création OU édition, par fermeture de block
    $(document).on('click', '.delete-button-block', function(e){
        e.preventDefault();
        var content_block = $(this).parents('.content-block-container');
        if ('edit' == content_block.attr('data-manipulation-type')) {
            resetFormElementToOriginalData(content_block);
            resetActionButtonElement(content_block);
            uninstallColorPicker();
            installColorPicker();
            content_block.hide();
        } else if ('create' == content_block.attr('data-manipulation-type')) {
            uninstallColorPicker();
            content_block.remove();
            $('.add-button-content').parents('.content-button-container').show();
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - ajout bouton d'action
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - edition bouton d'action
     * *********************************************************************************************
     */
    // appel formulaire
    $(document).on('click', '.edit-button-content', function(e){
        e.preventDefault();
        var content_index = 1;
        var content_block = $('.content-block-list-container.button-container').find('.content-block-container[data-block-index='+content_index+']');
        if (!content_block.is(':visible')) {
            content_block.attr('data-manipulation-type', 'edit');
            content_block.find('.button-text-error-message').text('');
            createOriginalDataHolder(content_block);
            setSelectedOptionInOriginalDataHolder(content_block);
            resetStyledChoiceSelectToOriginalData(content_block);
            setDeleteSelectVisibility(content_block);
            content_block.find('.add-content-button-container .edit-button').show();
            content_block.find('.add-content-button-container .add-button').hide();
            content_block.show();
        }
    });
    
    // soumission édition
    $(document).on('click', '.btn-valider.edit-button', function(e){
        e.preventDefault();
        var button_content_block = $(this).parents('.content-block-container.action-button-block-container');
        var button_text = button_content_block.find('.action-button-text-input');
        if ('' != button_text.val().trim()) {
            button_content_block.find('.button-text-error-message').text('');
            updateActionButtonFormBlock(button_content_block);
        } else {
            button_content_block.find('.button-text-error-message').text('Cette valeur ne doit pas être vide.')
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - edition bouton d'action
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Suppression bouton d'action
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-button-content', function(e){
        e.preventDefault();
        var button_element = $(this).parents('.content-list-element');
        button_element.find('.content-denomination-name div').text('');
        button_element.hide();

        var button_content_block = $('.content-block-list-container.button-container').find('.content-block-container.action-button-block-container').first();
        uninstallColorPicker(button_content_block);
        button_content_block.remove();

        $('.add-button-content').parents('.content-button-container').show();
    });
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Suppression bouton d'action
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning - Reordonnancement
     * *********************************************************************************************
     */
    $(document).on('reordering-content', '.reorder', function(){
        if ($(this).hasClass('media-reorder')) {
            defineMediaOrder();
        } else if ($(this).hasClass('quiz-reorder')) {
            defineQuizOrder();
        } else if ($(this).hasClass('image-reorder')) {
            var content_block = $(this).parents('.content-block-container');
            defineImageOrder(content_block);
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning - Reordonnancement
     * *********************************************************************************************
     */
});

function addMediaFormBlockBases()
{
    var form_content_model = $('.block-model-container').find('.content-block-container.media-content').clone();
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
    /*var media_content_list_container = $('.media-content-list-container');
    var current_order = media_content_list_container.find('.content-list-element').length + 1;
    content_block_container.find('.content-order-input').val(current_order);*/

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

        corresponding_original_data_holder.not('.hidden-input-file, .hidden-select, .hidden-input-text').show();

        if (corresponding_original_data_holder.hasClass('removable-content-input')) {
            if ('' != corresponding_original_data_holder.val().trim()) {
                corresponding_original_data_holder.next('.delete-input').show();
            } else {
                corresponding_original_data_holder.next('.delete-input').hide();
            }
        }
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
    setImageFileButtonText(image_block)
}

function setAssociatedFileButtonText(content_block_container)
{
    var associated_file_name = content_block_container.find('.hidden-input-file.associated-file').val();
    if ('' != associated_file_name) {
        content_block_container.find('.upload-img-button-container .img-name-container').text(associated_file_name.split('\\').pop());
    }
}

function setImageFileButtonText(image_block)
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
    /*var current_order = image_block_container.length + 1;
    image_block.find('.image-order-input').val(current_order);*/

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

    // adding new but not saved flag
    image_block.addClass('new-not-saved-image');
}

function createEquivalentImageListElement(image_block)
{
    var image_list_element = $('.block-model-container').find('.content-list-element.image-gallery').clone();
    image_list_element.find('.content-denomination-name').find('div').text(image_block.find('.document-name').find('input').val());
    image_list_element.attr('data-element-index', image_block.attr('data-block-index'));

    var image_element_list_container = image_block.parents('.content-block-container').find('.image-element-list-container');
    image_element_list_container.append(image_list_element);
    image_list_element.addClass('new-not-saved-image');
}

function addQuizBlockTemporary()
{
    removingAllOpenedQuizOnCreate();

    var quiz_block = $('.block-model-container').find('.content-block-container.quiz-block').clone();
    // var quiz_block_container = $('.content-block-list-container.quiz-container');
    var temporary_quiz_block_container = $('.temporary-content-block-list-container.quiz');
    temporary_quiz_block_container.append(quiz_block);
}

function addQuizFormBlock(content_block)
{
    var content_block_list_container = $('.content-block-list-container.quiz-container');
    var content_block_list = content_block_list_container.find('.content-block-container');

    var current_index = null;
    if (content_block_list.length <= 0) {
        current_index = 1;
    } else {
        var index_list = [];
        content_block_list.each(function(){
            index_list.push(parseInt($(this).attr('data-block-index')));
        });
        current_index = (Math.max.apply(null, index_list)) + 1;
    }

    // replacing default form element id and name by real ones (indexed instead of __name__-ed)
    var form_el_list = content_block.find('.form-el');
    form_el_list.each(function(){
        var id = $(this).attr('id');
        id = id.replace(/__name__/g, current_index);
        $(this).attr('id', id);
        var name = $(this).attr('name');
        name = name.replace(/__name__/g, current_index);
        $(this).attr('name', name);
    });

    // setting block index
    content_block.attr('data-block-index', current_index);

    // setting content order
    /*var current_order = content_block_list.length + 1;
    content_block.find('.content-order-input').val(current_order);*/

    // setting manipulation type
    content_block.attr('data-manipulation-type', 'edit');

    // adding media block
    content_block_list_container.append(content_block);
    content_block.hide();

    // create quiz list element (preview-like in list)
    createEquivalentQuizListElement(content_block);
}

function createEquivalentQuizListElement(content_block)
{
    var quiz_name = content_block.find('.styled-choice-select.quiz-select option:selected').text();

    var content_list_element = $('.block-model-container .content-list-element.quiz').clone();
    content_list_element.find('.content-denomination-name div').text(quiz_name);
    content_list_element.attr('data-element-index', content_block.attr('data-block-index'));

    var quiz_content_list_container = $('.quiz-content-list-container');
    quiz_content_list_container.append(content_list_element);
}

function closingAllOpenedQuizEdit()
{
    var quiz_block_list = $('.content-block-list-container.quiz-container').find('.content-block-container:visible');
    quiz_block_list.each(function(){
        resetFormElementToOriginalData($(this));
        $(this).hide();
    });
}

function removingAllOpenedQuizOnCreate()
{
    var temporary_quiz_block_container = $('.temporary-content-block-list-container.quiz');
    temporary_quiz_block_container.find('.content-block-container.quiz-block').remove();
}

function setSelectedOptionInOriginalDataHolder(content_block)
{
    var hidden_select_list = content_block.find('.form-el.hidden-select').not('.original-data-holder-el');
    hidden_select_list.each(function(){
        var id = $(this).attr('id');
        var searched_id = id + '__origin__';
        var selected_value = $(this).find('option:selected').val();
        var selected_in_original_data_holder = content_block.find('.original-data-holder-container').find('#'+searched_id)
            .find('option[value="'+selected_value+'"]');
        selected_in_original_data_holder.prop('selected', true);
    });
}

function setDeleteSelectVisibility(content_block)
{
    var hidden_select_list = content_block.find('.form-el.hidden-select').not('.original-data-holder-el');
    hidden_select_list.each(function(){
        var selected_value = $(this).find('option:selected').val();
        var styled_choice_select = $(this).parents('.styled-choice-select');
        if ('' == selected_value) {
            styled_choice_select.find('.dropdown .delete-select').css('visibility', 'hidden');
            styled_choice_select.find('.dropdown .delete-select').hide();
        } else {
            styled_choice_select.find('.dropdown .delete-select').css('visibility', 'visible');
            styled_choice_select.find('.dropdown .delete-select').show();
        }
    });
}

function resetStyledChoiceSelectToOriginalData(content_block)
{
    var styled_choice_select_list = content_block.find('.styled-choice-select');
    styled_choice_select_list.each(function(){
        var selected_choice = $(this).find('.hidden-select option:selected');
        var selected_choice_text = selected_choice.text();
        $(this).find('.dropdown-toggle').text(selected_choice_text);
        $(this).find('.delete-select').css({'visibility':'visible','display':'inline-block'});
    });
}

function updateQuizBlock(content_block)
{
    var block_index = content_block.attr('data-block-index');
    var selected_quiz_text = content_block.find('.styled-choice-select.quiz-select .hidden-select option:selected').text();
    var corresponding_quiz_element = $('.quiz-content-list-container .content-list-element.quiz[data-element-index='+block_index+']');
    corresponding_quiz_element.find('.content-denomination-name div').text(selected_quiz_text);
    content_block.find('.original-data-holder-container').html('');
    content_block.hide();
}

function addButtonBlockTemporary()
{
    var button_block = $('.block-model-container .content-block-container.action-button-block-container').clone();
    var temporary_button_block_container = $('.temporary-content-block-list-container.button');
    temporary_button_block_container.append(button_block);
    initActionButtonDatas(button_block);
}

function initActionButtonDatas(content_block)
{
    content_block.find('.action-button-color-option .bg-color').val('#ff0000');
    content_block.find('.action-button-color-option .text-color').val('#ffffff');
    var default_action_button_text = 'En savoir plus';
    content_block.find('.content-name-input').val(default_action_button_text);
    content_block.find('.action-button-text-input').val(default_action_button_text);
    content_block.find('.action-button-text-input').next('.delete-input').show();
    content_block.find('.action-button-preview').text(default_action_button_text);
}

function addActionButtonFormBlock(content_block)
{
    var content_block_list_container = $('.content-block-list-container.button-container');
    content_block_list_container.html('');
    var current_index = 1;

    // replacing default form element id and name by real ones (indexed instead of __name__-ed)
    var form_el_list = content_block.find('.form-el');
    form_el_list.each(function(){
        var id = $(this).attr('id');
        id = id.replace(/__name__/g, current_index);
        $(this).attr('id', id);
        var name = $(this).attr('name');
        name = name.replace(/__name__/g, current_index);
        $(this).attr('name', name);
    });

    // setting block index
    content_block.attr('data-block-index', current_index);

    // setting manipulation type
    content_block.attr('data-manipulation-type', 'edit');

    // adding media block
    content_block_list_container.append(content_block);
    content_block.hide();

    // copy button text to content name input
    var button_text = content_block.find('.action-button-text-input').not('.original-data-holder-el').val();
    var content_name_input = content_block.find('.content-name-input').not('.original-data-holder-el');
    content_name_input.val(button_text);

    // create button list element (preview-like in list)
    createEquivalentButtonListElement(content_block);
}

function createEquivalentButtonListElement(content_block) {
    var content_list_element = $('.button-content-list-container').find('.content-list-element.button').first();
    var button_text = content_block.find('.action-button-text-input').val();
    content_list_element.find('.content-denomination-name div').text(button_text);
    content_list_element.show();
}

// better called after resetFormElementToOriginalData()
// to get original data
function resetActionButtonElement(content_block)
{
    // button preview
    var button_preview = content_block.find('.action-button-preview');
    var button_text = content_block.find('.form-row .action-button-text-input').val();
    var button_bg_color = content_block.find('.form-row .action-button-background-color').val();
    var button_text_color = content_block.find('.form-row .action-button-text-color').val();
    button_preview.text(button_text);
    button_preview.css({
        'background-color': button_bg_color,
        'color': button_text_color
    });
}

function updateActionButtonFormBlock(content_block)
{
    var button_text = content_block.find('.action-button-text-input').val();
    var content_list_element = $('.button-content-list-container').find('.content-list-element.button').first();
    content_list_element.find('.content-denomination-name div').text(button_text);
    content_block.find('.original-data-holder-container').html('');

    // copy button text to content name input
    var button_text = content_block.find('.action-button-text-input').not('.original-data-holder-el').val();
    var content_name_input = content_block.find('.content-name-input').not('.original-data-holder-el');
    content_name_input.val(button_text);

    content_block.hide();
}

function setMediaReorderingFeatureStatus()
{
    var media_content_list_container = $('.media-content-list-container');
    setReorderingFeatureStatus(media_content_list_container);
}

function setQuizReorderingFeatureStatus()
{
    var media_content_list_container = $('.quiz-content-list-container');
    setReorderingFeatureStatus(media_content_list_container);
}

function setGalleryImageReorderingFeatureStatus(content_block)
{
    var media_content_list_container = content_block.find('.image-element-list-container');
    setReorderingFeatureStatus(media_content_list_container);
}

function setReorderingFeatureStatus(content_list_container)
{
    var content_list = content_list_container.find('.content-list-element').not('.to-delete-image-element');
    if (content_list.length > 1) {
        content_list.each(function(){
            $(this).find('.actions-container .reorder').removeClass('disabled');
        });
    } else {
        content_list.each(function(){
            $(this).find('.actions-container .reorder').addClass('disabled');
        });
    }
}

function removeFlagOnNewNotSavedImage(content_block)
{
    // content_block.find('.new-not-saved-image').removeClass('new-not-saved-image');
    var new_not_saved_image_list = content_block.find('.new-not-saved-image');
    new_not_saved_image_list.each(function(){
        $(this).removeClass('new-not-saved-image');
    });
}

function deleteNewNotSavedImage(content_block)
{
    content_block.find('.new-not-saved-image').remove();
}

function defineMediaOrder()
{
    var content_list_container = $('.media-content-list-container');
    var content_block_list_container = $('.content-block-list-container.media-container');
    defineContentOrder(content_list_container, content_block_list_container);
}

function defineQuizOrder()
{
    var content_list_container = $('.quiz-content-list-container');
    var content_block_list_container = $('.content-block-list-container.quiz-container');
    defineContentOrder(content_list_container, content_block_list_container);
}

function defineImageOrder(content_block)
{
    var content_list_container = content_block.find('.image-element-list-container');
    var content_block_list_container = content_block.find('.image-block-container');
    defineContentOrder(content_list_container, content_block_list_container, 'image');
}

function defineContentOrder(content_list_container, content_block_list_container, content_type = 'standard')
{
    var content_element_list = content_list_container.find('.content-list-element').not('.to-delete-image-element');
    var current_order = 1;
    content_element_list.each(function(){
        var content_element_index = $(this).attr('data-element-index');
        var corresponding_content_block = null;
        var content_order_input = null;
        if ('image' == content_type) {
            corresponding_content_block = content_block_list_container.find('.gallery-image-element[data-block-index='+content_element_index+']');
            content_order_input = corresponding_content_block.find('.image-order-input').not('.original-image-data-holder-el');
        } else {
            corresponding_content_block = content_block_list_container.find('.content-block-container[data-block-index='+content_element_index+']');
            content_order_input = corresponding_content_block.find('.content-order-input').not('.original-data-holder-el');
        }
        if (null != content_order_input){
            content_order_input.val(current_order);
            current_order++;
        }
    });
}