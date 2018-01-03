$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Paramétrages - Communication
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
     * Paramétrages - Communication
     * Section active
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication
     * fenêtre d'édition de post
     * *********************************************************************************************
     */
    $('.edit-post').on('click', function(e){
        e.preventDefault();
        $(this).parents('.fieldset').find('.row.edit-form-container').show();
        $(this).parents('.fieldset').find('.option-container').addClass('edit-mode');
    });

    $('.close-edit-post-form').on('click', function(e){
        e.preventDefault();
        $(this).parents('.fieldset').find('.row.edit-form-container').hide();
        $(this).parents('.fieldset').find('.option-container').removeClass('edit-mode');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication
     * fenêtre d'édition de post
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication
     * Confirmation suppression de post
     * *********************************************************************************************
     */
    $('.delete-post').on('click', function(e){
        e.preventDefault();
    });

    $('#confirm-delete-dialog').on('show.bs.modal', function(e){
        var trigger = e.relatedTarget
        var edito_post_id = $(trigger).attr('data-edito-post-id');
        var modal = $(this);
        modal.find('input[name=edito_post_id]').val(edito_post_id);
    });

    $('.confirm-delete-dialog  .confirm-delete').on('click', function(e){
        e.preventDefault();
        var edito_post_id = $(this).parents('.confirm-delete-dialog').find('input[name=edito_post_id]').val();
        if(NaN !== parseInt(edito_post_id)){
            var part_delete_edito_url = $('input[name=delete_edito_url]').val();
            var delete_edito_url = part_delete_edito_url.replace(/___id___/, edito_post_id);
            $.ajax({
                type: 'GET',
                url: delete_edito_url,
                success: function(html){
                    if(-1 != html.indexOf('OK')) {
                        var to_delete_form_el = $('.fieldset[data-edito-post-id='+edito_post_id+']').parent('form');
                        to_delete_form_el.remove();
                        $('#confirm-delete-dialog').modal('hide');
                    }
                }
            });
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication
     * Confirmation suppression de post
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication
     * Color picker
     * *********************************************************************************************
     */
    if ($('.color-value').length >0 ) {//plugin color
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
                    /*if( typeof console === 'object' ) {
                        console.log(value);
                    }*/
                },
                theme: 'bootstrap'
            });
        });
    }

    // $('#action-button-background-color').on('change', function(e){
    $(document).on('change', '.action-button-background-color', function(e){
        e.preventDefault();
        $('.action-button-preview').css("background-color", $(this).val());
    });


    // $('#action-button-text-color').on('change', function(e){
    $(document).on('change', '.action-button-text-color', function(e){
        e.preventDefault();
        $('.action-button-preview').css("color", $(this).val());
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication
     * Color picker
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Création template
     * *********************************************************************************************
     */

    $('.create-template-button').on('click', function(e){
        e.preventDefault();
        $('#choose-model-dialog').modal('show');
    });

    $('.btn-valider.continue').on('click', function(e){
        e.preventDefault();
        var template_model = null;
        if($('input#text-image-option-radio').is(':checked')){
            template_model = $('input[name=template_model_text_and_image]').val();
        } else if($('input#simple-text-option-radio').is(':checked')) {
            template_model = $('input[name=template_model_text_only]').val();
        }

        if(null !== template_model){
            var add_template_url = $('input[name=add_template_form_url]').val();
            add_template_url = add_template_url+'/'+template_model;
            $.ajaxSetup({async: false});
            $.ajax({
                type: 'GET',
                url: add_template_url,
                success: function(data){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                },
                statusCode: {
                    404: function(data){
                        $('#create-template-dialog').find('.error-message-container').text(data.responseJSON.message);
                        $('#create-template-dialog').find('.modal-body-container').html('');
                    }
                }
            });
            $('#choose-model-dialog').modal('hide');
            setTimeout(function(){
                $('#create-template-dialog').modal({
                    show: true,
                });
            }, 0);
            $.ajaxSetup({async: true});
        }
    });

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
                customConfig: ckeditor_config_light_path,
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


    $('#create-template-dialog').on('shown.bs.modal', function(){
        // installer color picker
        installColorPicker();

        // installer wysiwyg
        installWysiwyg();
    });

    $('#create-template-dialog').on('hide.bs.modal', function(e){
        $('#choose-model-dialog').modal('hide');
    });

    $(document).on('click', '#create-template-dialog button.btn-valider.validate', function(e){
        var add_template_url = $('input[name=add_template_form_url]').val();
        e.preventDefault();
        $('#create-template-dialog form').ajaxSubmit({
            type: 'POST',
            url: add_template_url,
            success: function(data){
               if(data['error']){
                   $('#create-template-dialog').find('.modal-body-container').html(data.content);
                   $('#create-template-dialog').find('.btn-valider.save').trigger('click');
                   installColorPicker();
                   installWysiwyg();
               } else {
                   $('#create-template-dialog').modal('hide');
                   window.location.replace($('input[name=template_list_url]').val());
               }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Création template
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Upload image
     * *********************************************************************************************
     */
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
            $(this).parent().find('.upload-img-button').addClass('hidden-button');
            $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
            $(this).parent().find('.btn-upload.choose-upload-img-button').removeClass('hidden-button');
        } else {
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').removeClass('hidden-button');
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload.choose-upload-img-button').addClass('hidden-button');
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Upload image
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * aspect bouton d'action
     * *********************************************************************************************
     */
    $(document).on('input', '#create-template-dialog .action-button-text-input', function(e){
        $(this).parents('#create-template-dialog').find('.action-button-preview').text($(this).val());
    });

    $(document).on('change', '#create-template-dialog .action-button-text-input', function(e){
        $(this).parents('#create-template-dialog').find('.action-button-preview').text($(this).val());
    });

    $(document).on('click', '#create-template-dialog .delete-action-button-text', function(e){
        $(this).parents('#create-template-dialog').find('.action-button-preview').text('');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * aspect bouton d'action
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * modal création template - enregistrement et validation
     * *********************************************************************************************
     */
    $(document).on('click', '#create-template-dialog .btn-valider.save', function(e){
        e.preventDefault();
        $('.options-wrapper').addClass('active');
        $('#create-template-dialog').find('.btn-valider.modify').removeClass('hidden');
        $('#create-template-dialog').find('.btn-valider.validate').removeClass('hidden');
        $('#create-template-dialog').find('.template-name-container').removeClass('hidden');
        $(this).addClass('hidden');
    });

    $(document).on('click', '#create-template-dialog .btn-valider.modify', function(e){
        e.preventDefault();
        $('.options-wrapper').removeClass('active');
        $('#create-template-dialog').find('.btn-valider.validate').addClass('hidden');
        $('#create-template-dialog').find('.template-name-container').addClass('hidden');
        $('#create-template-dialog').find('.btn-valider.save').removeClass('hidden');
        $(this).addClass('hidden');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * modal création template - enregistrement et validation
     * *********************************************************************************************
     */



});