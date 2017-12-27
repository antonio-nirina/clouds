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

    $('#action-button-background-color').on('change', function(e){
        e.preventDefault();
        $('.action-button-preview').css("background-color", $(this).val());
    });

    $('#action-button-text-color').on('change', function(e){
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

    /*$('.continue').on('click', function(e){
        $('#choose-model-dialog').modal('hide');
        console.log('here');
        // $('#create-template-dialog').modal('show');
        $('#create-template-button').trigger('click');
        console.log('and here');
    });*/

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Emailing - Templates
     * Création template
     * *********************************************************************************************
     */



});