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

    $('#confirm-delete-dialog  .confirm-delete').on('click', function(e){
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
                    500: function(data){
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

            // instantaneous preview feat
            CKEDITOR.instances[$(this).attr('id')].on('change', function(){
                var text_content_index = $(this).parents('.text-content-block').index('.text-content-block');
                var corresponding_text_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.text-content-tr').eq(text_content_index);
                corresponding_text_content_tr.find('td').html(this.getData());
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
        $('.chargementAjax').removeClass('hidden');
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
                   $('.chargementAjax').addClass('hidden');
               } else {
                   // $('#create-template-dialog').modal('hide');
                   $('.chargementAjax').addClass('hidden');
                   window.location.replace($('input[name=template_list_url]').val());
               }
            },
            statusCode: {
                404: function(data){
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(data){
                    $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
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

        // fonctionnalité prévisualisation instantanée
            //  tr correspondant
        var new_text_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.text-content-tr').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_text_content_tr);
            // mise à jour de la prévisualisation
        CKEDITOR.instances[new_text_content.find('textarea').attr('id')].on('change', function(){
            var text_content_index = new_text_content.find('textarea').parents('.text-content-block').index('.text-content-block');
            var corresponding_text_content_tr = $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.text-content-tr').eq(text_content_index);
            corresponding_text_content_tr.find('td').html(this.getData());
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
                },

                500: function(data){
                    $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
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
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Suprression de contenu
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication
     * Confirmation suppression de template
     * *********************************************************************************************
     */
    $('.delete-template').on('click', function(e){
        e.preventDefault();
    });

    $('#confirm-delete-template').on('show.bs.modal', function(e){
        var trigger = e.relatedTarget;
        var template_id = $(trigger).attr('data-template-id');
        $(this).find('input[name=template_id]').val(template_id);
    });

    $('#confirm-delete-template .confirm-delete').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var template_id = $(this).parents('.confirm-delete-dialog').find('input[name=template_id]').val();
        if(NaN !== parseInt(template_id)){
            var part_delete_template_url = $('input[name=delete_template_url]').val();
            var delete_template_url = part_delete_template_url.replace(/__id__/, template_id);
            $.ajax({
                type: 'GET',
                url: delete_template_url,
                success: function(data){
                    window.location.replace($('input[name=template_list_url]').val());
                },
                statusCode: {
                    404: function(data){
                        $('.chargementAjax').addClass('hidden');
                        $('#confirm-delete-template').modal('hide');
                    },
                    500: function(data){
                        $('.chargementAjax').addClass('hidden');
                        $('#confirm-delete-template').modal('hide');
                    }
                }
            });
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication
     * Confirmation suppression de template
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Tri
     * *********************************************************************************************
     */
    $('.template-sorting-element').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var partial_sort_template_url = $('input[name=sort_template_list_url]').val();
        var sort_template_url = partial_sort_template_url.replace(/__param__/, $(this).attr('data-sorting-parameter'));
        var current_sorting_element = $(this);
        $.ajax({
            type: 'GET',
            url: sort_template_url,
            success: function(data){
                $('div.row.template-list').html(data.content);
                $('.chargementAjax').addClass('hidden');
                $('#dropdownMenuFiltre').text(current_sorting_element.text());
            },
            statusCode: {
                404: function(data){
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(data){
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Tri
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Prévisualisation instantanée dans Popup
     * *********************************************************************************************
     */
    function setInstantaneousPreview(current_create_template_dialog)
    {
        var instantaneous_preview_content = current_create_template_dialog.find('.instantaneous-preview-container').html();
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').html(instantaneous_preview_content);
    }

    $('#create-template-dialog').on('shown.bs.modal', function(){
        setInstantaneousPreview($(this));
    });

    $('#create-template-dialog').on('hidden.bs.modal', function(){
        $('#instantaneous-preview-template-dialog').find('.modal-body-container').html('');
    });

    $(document).on('click', '.magnify-template-preview', function(e){
        e.preventDefault();
        $('#instantaneous-preview-template-dialog').modal('show');
    });

    function createImagePreview(input, preview_container) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview_container.attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // changement logo
    $(document).on('change', '.logo-image-input', function(){
        var logo_image_block_no_image = $('#instantaneous-preview-template-dialog .pseudo-body-table .logo-img-tr-no-image');
        if(logo_image_block_no_image.length > 0){
            var new_logo_image_block = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.logo-img-tr').clone();
            logo_image_block_no_image.replaceWith(new_logo_image_block);
        }
        var logo_image_block = $('#instantaneous-preview-template-dialog .pseudo-body-table .logo-img-tr');
        createImagePreview(this, logo_image_block.find('img'));
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
    });

    $(document).on('click', '.delete-action-button-text', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a span').text('');
    });

        // changement couleur de fond
    $(document).on('change', '.action-button-background-color', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a').css("background-color", $(this).val());
    });

        // changement couleur de couleur de texte
    $(document).on('change', '.action-button-text-color', function(){
        var corresponding_button_content_tr = getCorrespondingButtonContentTr($(this));
        corresponding_button_content_tr.find('a').css("color", $(this).val());
    });

    // ajout nouvel image
    $(document).on('click', '.add-image-link', function(){
        var new_image_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.img-content-tr.no-image').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_image_content_tr);
    });

    // ajout nouveau bouton
    $(document).on('click', '.add-button-link', function(){
        var new_button_content_tr = $('#instantaneous-preview-template-dialog').find('.email-template-block-model-container').find('.button-content-tr').clone();
        $('#instantaneous-preview-template-dialog .pseudo-body-table').find('.contents-container-table').append(new_button_content_tr);
    });

    // changement de couleur de fond
        // couleur d'email
    $(document).on('change', '.email-color', function(){
        var main_table = $('#instantaneous-preview-template-dialog').find('.main-table');
        main_table.css('background-color', $(this).val());
    });
        // couleur de fond
    $(document).on('change', '.template-background-color', function(){
        var pseudo_body_table = $('#instantaneous-preview-template-dialog').find('.pseudo-body-table');
        pseudo_body_table.css('background-color', $(this).val());
        var same_bg_color_as_background = $('#instantaneous-preview-template-dialog').find('.same-bg-color-as-background');
        same_bg_color_as_background.find('td').css('background-color', $(this).val());
    });

    // changements textes footer
    $(document).on('input', '.footer-text-option-input.company-info', function(){
        var footer_text_company_info = $('#instantaneous-preview-template-dialog').find('.footer-text-company-info');
        footer_text_company_info.text($(this).val());
    });

    $(document).on('input', '.footer-text-option-input.contact', function(){
        var footer_text_contact = $('#instantaneous-preview-template-dialog').find('.footer-text-contact');
        footer_text_contact.text($(this).val());
    });

    $(document).on('input', '.footer-text-option-input.unsubscribe', function(){
        var footer_text_unsubscribe = $('#instantaneous-preview-template-dialog').find('.footer-text-unsubscribe');
        footer_text_unsubscribe.text($(this).val());
    });

    $(document).on('input', '.footer-text-option-input.additional-info', function(){
        var footer_text_additional_info = $('#instantaneous-preview-template-dialog').find('.footer-text-additional-info');
        footer_text_additional_info.text($(this).val());
    });

    $(document).on('click','.delete-footer-text-additional-info', function(){
        var footer_text_additional_info = $('#instantaneous-preview-template-dialog').find('.footer-text-additional-info');
        footer_text_additional_info.text('');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Prévisualisation instantanée dans Popup
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Enregistrement de modèle à la création
     * *********************************************************************************************
     */
    function handleTemplateNameInputPlace()
    {
        var template_name_error_message = transferErrorMessage();
        if(template_name_error_message.length > 0){
            transferTemplateNameInput();
        } else {
            $('#save-template-on-close-dialog').modal('hide');
        }
    }

    function transferTemplateNameInput()
    {
        var template_name_input = $('#create-template-dialog').find('.template-name-input');
        if(template_name_input.length <= 0){
            $('#create-template-dialog').modal('hide');
            $('#save-template-on-close-dialog').modal('hide');
        }
        template_name_input.addClass('input-text');
        template_name_input.addClass('semi-large-input-text');
        $('#save-template-on-close-dialog').find('.input-container').find('.delete-input').before(template_name_input);
        if(template_name_input.val().trim() != ''){
            $('#save-template-on-close-dialog').find('.input-container').find('.delete-input').show();
        }
        template_name_input.removeAttr('required');
    }

    function transferBackTemplateNameInput()
    {
        var template_name_input = $('#save-template-on-close-dialog').find('.input-container').find('input');
        var to_transfer_template_name_input = template_name_input.clone();
        template_name_input.removeAttr('name');
        template_name_input.removeAttr('id');
        $('#create-template-dialog').find('.template-name-input-row-container').find('.delete-input').before(to_transfer_template_name_input);
    }

    function transferErrorMessage()
    {
        var error_message = $('#create-template-dialog').find('.template-name-error').children();
        if(error_message.length > 0){
            $('#save-template-on-close-dialog').find('.form-element-container').find('.error-message-container').html('');
            $('#save-template-on-close-dialog').find('.form-element-container').find('.error-message-container').append(error_message);
        }
        return error_message;
    }

    $(document).on('click', '#create-template-dialog .close-modal', function(e){
        e.preventDefault();
        transferTemplateNameInput();
        var manip_mode = $('#create-template-dialog').find('input[name=manip_mode]').val();
        if('edit' == manip_mode){
            $('#save-template-on-close-dialog').find('.save').addClass('save-edit');
            var template_id = $('input[name=template_id]').val();
            var data_target_url = $('input[name=edit_template_url]').val();
            data_target_url = data_target_url.replace(/__id__/, template_id);
            $('#save-template-on-close-dialog').find('.save').attr('data-target-url', data_target_url);
        } else if ('create' == manip_mode){
            $('#save-template-on-close-dialog').find('.save').addClass('save-create');
        }
        $('#save-template-on-close-dialog').modal('show');
    });

    $(document).on('click', '#save-template-on-close-dialog .cancel', function(){
        $('#create-template-dialog').modal('hide');
        $('#save-template-on-close-dialog').modal('hide');
        $('#save-template-on-close-dialog').find('.save').removeClass('save-create');
        $('#save-template-on-close-dialog').find('.save').removeClass('save-edit');
        $('#save-template-on-close-dialog').find('.form-element-container').find('.error-message-container').html('');
    });

    $('#save-template-on-close-dialog').on('hidden.bs.modal', function(){
        $(this).find('.input-container').find('input').remove();
    });

    $(document).on('click', '#save-template-on-close-dialog .save.save-create', function(e){
        e.preventDefault();
        var add_template_url = $('input[name=add_template_form_url]').val();
        $('.chargementAjax').removeClass('hidden');
        $('#create-template-dialog').find('.block-model-container').remove();
        transferBackTemplateNameInput();
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
        $('#create-template-dialog form').ajaxSubmit({
            type: 'POST',
            url: add_template_url,
            success: function(data){
                if(data['error']){
                    $('#create-template-dialog').find('.modal-body-container').html(data.content);
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    handleTemplateNameInputPlace();
                    installColorPicker();
                    installWysiwyg();
                    $('.chargementAjax').addClass('hidden');
                } else {
                    $('.chargementAjax').addClass('hidden');
                    window.location.replace($('input[name=template_list_url]').val());
                }
            },
            statusCode: {
                404: function(data){
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    $('#save-template-on-close-dialog').modal('hide');
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(data){
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    $('#save-template-on-close-dialog').modal('hide');
                    $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    $(document).on('click', '#save-template-on-close-dialog .save.save-edit', function(e){
        e.preventDefault();
        $('#create-template-dialog').find('.block-model-container').remove();
        transferBackTemplateNameInput();
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
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    handleTemplateNameInputPlace();
                    installColorPicker();
                    installWysiwyg();
                    $('.chargementAjax').addClass('hidden');
                } else {
                    window.location.replace($('input[name=template_list_url]').val());
                }
            },
            statusCode: {
                404: function(data){
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    $('#save-template-on-close-dialog').modal('hide');
                    $('#create-template-dialog').find('.error-message-container.general-message').text('Erreur');
                    $('#create-template-dialog').find('.modal-body-container').html('');
                    $('.chargementAjax').addClass('hidden');
                },

                500: function(data){
                    $('#save-template-on-close-dialog').find('.input-container').find('input').remove();
                    $('#save-template-on-close-dialog').modal('hide');
                    $('#create-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
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
     * Enregistrement de modèle à la création
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * Duplidation de template
     * *********************************************************************************************
     */
    $(document).on('click', '.duplicate-template', function(e){
        e.preventDefault();
        var target_url = $(this).attr('data-target-url');
        $('.chargementAjax').removeClass('hidden');
        $.ajax({
            type: 'GET',
            url: target_url,
            success: function(data){
                $('#duplicate-template-dialog').find('.modal-body-container').html(data.content);
                $('#duplicate-template-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            },
            statusCode: {
                404: function(data){
                    $('#duplicate-template-dialog').find('.modal-body-container').html('');
                    $('#duplicate-template-dialog').find('.error-message-container.general-message').text('Page non trouvée');
                    $('#duplicate-template-dialog').find('.close-modal').show();
                    $('#duplicate-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(data){
                    $('#duplicate-template-dialog').find('.modal-body-container').html('');
                    $('#duplicate-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                    $('#duplicate-template-dialog').find('.close-modal').show();
                    $('#duplicate-template-dialog').modal('show');
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });
    });

    $(document).on('click', '#duplicate-template-dialog .cancel', function(e){
        e.preventDefault();
        $('#duplicate-template-dialog').find('.modal-body-container').html('');
        $('#duplicate-template-dialog').modal('hide');
    });

    $('#duplicate-template-dialog').on('hidden.bs.modal', function(){
        $(this).find('.close-modal').hide();
        $('#duplicate-template-dialog').find('.error-message-container.general-message').text('');
    });

    $(document).on('click', '#duplicate-template-dialog .save.save-duplication', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var duplicate_template_url = $(this).attr('data-target-url');
        console.log(duplicate_template_url);
        $('#duplicate-template-dialog form').ajaxSubmit({
            type: 'POST',
            url: duplicate_template_url,
            success: function(data){
                if(data['error']){
                    $('#duplicate-template-dialog').find('.modal-body-container').html(data.content);
                    $('.chargementAjax').addClass('hidden');
                } else {
                    window.location.replace($('input[name=template_list_url]').val());
                }
            },
            statusCode:{
                404: function(data){
                    alert('404');
                    $('#duplicate-template-dialog').find('.modal-body-container').html('');
                    $('#duplicate-template-dialog').find('.error-message-container.general-message').text('Page non trouvée');
                    $('#duplicate-template-dialog').find('.close-modal').show();
                    $('.chargementAjax').addClass('hidden');
                },
                500: function(data){
                    alert('500');
                    $('#duplicate-template-dialog').find('.modal-body-container').html('');
                    $('#duplicate-template-dialog').find('.error-message-container.general-message').text(data.responseJSON.message);
                    $('#duplicate-template-dialog').find('.close-modal').show();
                    $('.chargementAjax').addClass('hidden');
                }
            }
        });

    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Duplidation de template
     * *********************************************************************************************
     */
});