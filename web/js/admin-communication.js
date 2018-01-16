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
        $(this).parents('.action-button-block').find('.action-button-preview').css("background-color", $(this).val());
    });


    // $('#action-button-text-color').on('change', function(e){
    $(document).on('change', '.action-button-text-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block').find('.action-button-preview').css("color", $(this).val());
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

        $('#create-template-dialog').find('.error-message-container.general-message').text('');

        var template_model = null;
        if($('input#text-image-option-radio').is(':checked')){
            template_model = $('input[name=template_model_text_and_image]').val();
        } else if($('input#simple-text-option-radio').is(':checked')) {
            template_model = $('input[name=template_model_text_only]').val();
        }

        if(null !== template_model){
            var add_template_url = $('input[name=add_template_form_url]').val();
            add_template_url = add_template_url+'/'+template_model;
            $('.chargementAjax').removeClass('hidden');
            // $.ajaxSetup({async: false});
            $.ajax({
                type: 'GET',
                url: add_template_url,
                success: function(data){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                    $('#choose-model-dialog').modal('hide');
                    setTimeout(function(){
                        $('#create-template-dialog').find('a.previous').show();
                        $('#create-template-dialog').modal({
                            show: true,
                        });
                    }, 0);
                    $('.chargementAjax').addClass('hidden');
                },
                statusCode: {
                    404: function(data){
                        $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                        $('#create-template-dialog').find('.modal-body-container').html('');
                        $('#choose-model-dialog').modal('hide');
                        setTimeout(function(){
                            $('#create-template-dialog').find('a.previous').show();
                            $('#create-template-dialog').modal({
                                show: true,
                            });
                        }, 0);
                        $('.chargementAjax').addClass('hidden');
                    },
                    500: function(){
                        $('#preview-template-dialog').find('.error-message-container.general-message').text('Erreur interne');
                        $('#preview-template-dialog').find('.modal-body-container').html('');
                        $('#preview-template-dialog').modal('show');
                        $('.chargementAjax').addClass('hidden');
                    }
                }
            });
            // $('#choose-model-dialog').modal('hide');
            // setTimeout(function(){
            //     $('#create-template-dialog').find('a.previous').show();
            //     $('#create-template-dialog').modal({
            //         show: true,
            //     });
            // }, 0);
            // $.ajaxSetup({async: true});
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


    $('#create-template-dialog').on('shown.bs.modal', function(){
        // installer color picker
        installColorPicker();

        // installer wysiwyg
        installWysiwyg();
    });

    $('#create-template-dialog').on('hide.bs.modal', function(e){
        $('#choose-model-dialog').modal('hide');
    });

    $(document).on('click', '#create-template-dialog button.btn-valider.validate.validate-add', function(e){
        var add_template_url = $('input[name=add_template_form_url]').val();
        e.preventDefault();
        $('#create-template-dialog').find('.block-model-container').remove();

        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }

        $('.chargementAjax').removeClass('hidden');
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
                   // $('#create-template-dialog').modal('hide');
                   window.location.replace($('input[name=template_list_url]').val());
               }
            },
            statusCode: {
                404: function(data){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                }
            }
        });
        $('.chargementAjax').addClass('hidden');
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
        $(this).parents('.action-button-block').find('.action-button-preview').text($(this).val());
    });

    $(document).on('change', '#create-template-dialog .action-button-text-input', function(e){
        $(this).parents('.action-button-block').find('.action-button-preview').text($(this).val());
    });

    $(document).on('click', '#create-template-dialog .delete-action-button-text', function(e){
        $(this).parents('.action-button-block').find('.action-button-preview').text('');
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

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * création template - lien "précédent"
     * *********************************************************************************************
     */
    $('#create-template-dialog .previous').on('click', function(e){
        e.preventDefault();
        $('#create-template-dialog').modal('hide');
        setTimeout(function(){
            $('#choose-model-dialog').modal('show');
        }, 0);
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * création template - lien "précédent"
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * ajout d'autres contenus
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

    /**
     * *********************************************************************************************
     * FINF
     * Paramétrages - Communication - Emailing - Templates
     * ajout d'autres contenus
     * *********************************************************************************************
     */


    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * édition template
     * *********************************************************************************************
     */
    $(document).on('click', '.edit-template', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-target-url'),
            success: function(data){
                $('#create-template-dialog').find('.modal-body-container').html(data.content);
                $('#create-template-dialog').find('a.previous').hide();
                $('#create-template-dialog').find('.error-message-container.general-message').text('');
                $('#create-template-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            },
            statusCode: {
                404: function(data){
                    $('')
                    $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('#create-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('#preview-template-dialog').find('.error-message-container.general-message').text('Erreur interne');
                    $('#preview-template-dialog').find('.modal-body-container').html('');
                    $('#preview-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    $(document).on('click', '#create-template-dialog button.btn-valider.validate.validate-edit', function(e){
        e.preventDefault();
        $('#create-template-dialog').find('.block-model-container').remove();
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
        $('#create-template-dialog').find('.block-model-container').remove();

        $('.chargementAjax').removeClass('hidden');
        $('#create-template-dialog form').ajaxSubmit({
            type: 'POST',
            url: $(this).attr('data-target-url'),
            success: function(data){
                if(data['error']){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                    $('#create-template-dialog').find('.btn-valider.save').trigger('click');
                    installColorPicker();
                    installWysiwyg();
                    $('.chargementAjax').addClass('hidden');
                } else {
                    window.location.replace($('input[name=template_list_url]').val());
                }
            },
            statusCode: {
                404: function(data){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * édition template
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Suppression logo et image de contenu
     * *********************************************************************************************
     */
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
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Suppression logo et image de contenu
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Prévisualisation template
     * *********************************************************************************************
     */
    $(document).on('click', '.preview-template', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-target-url'),
            success: function(data){
                $('#preview-template-dialog').find('.modal-body-container').html(data.content);
                $('#preview-template-dialog').find('.error-message-container.general-message').text('');
                $('#preview-template-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            },
            statusCode:{
                404: function(data){
                    $('#preview-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                    $('#preview-template-dialog').find('.modal-body-container').html('');
                    $('#preview-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(){
                    $('#preview-template-dialog').find('.error-message-container.general-message').text('Erreur interne');
                    $('#preview-template-dialog').find('.modal-body-container').html('');
                    $('#preview-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Prévisualisation template
     * *********************************************************************************************
     */

    /**
     * * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Alerte à la suppression de texte de footer obligatoire
     * * *********************************************************************************************
     */
    $(document).on('click', '.delete-mandatory-input', function(e){
        e.preventDefault();
        $('#alert-footer-element-dialog').modal('show');
    });

    /**
     * * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Alerte à la suppression de texte de footer obligatoire
     * * *********************************************************************************************
     */

    /**
     * **********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Personnalisation
     * **********************************************************************************************
     */
    $(document).on('click', '.personalization-option', function(e){
        var personalization_value = $(this).attr('data-personalization-value');
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
    /**
     * **********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Personnalisation
     * **********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Suprression de contenu
     * *********************************************************************************************
     */
    $(document).on('click', '.delete-content', function(e){
        e.preventDefault();
        $('#'+$(this).parents('.template-content-block').attr('data-form-field-id')).remove();
        $(this).parents('.template-config-block').next('.template-config-block').not('.template-content-block').remove();
        $(this).parents('.template-config-block').remove();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Suprression de contenu
     * *********************************************************************************************
     */
});