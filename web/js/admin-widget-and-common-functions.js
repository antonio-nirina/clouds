$(document).ready(function(){
    /**
     * Choix multiples
     */
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
    /**
     * FIN
     * Choix multiples
     */

    /**
     * Mise à jour de données de formulaire - "qui verra?"
     */
    $(document).on('click', '.app-dialog .viewer-authorization-type-choice .dropdown .dropdown-item', function(){
        $(this).parents('.app-dialog').find('.authorized-viewer-role-input').val($(this).text());
    });

    $(document).on('click', '.app-dialog .viewer-authorization-type-choice .delete-select', function(){
        var modal = $(this).parents('.app-dialog');
        var toggle_button = modal.find('.viewer-authorization-type-choice button.toggle-button');
        modal.find('.authorized-viewer-role-input').val(toggle_button.attr('data-default-button-text'));
    });
    /**
     * FIIN
     * Mise à jour de données de formulaire - "qui verra?"
     */

    /**
     * Bouton d'upload de fichier
     */
    $(document).on('click', '.input-file-trigger', function(e){
        e.preventDefault();
        $(this).parent().children('.hidden-input-file').trigger('click');
    });

    $(document).on('change', '.hidden-input-file', function(){
        if('' == $(this).val().trim()){
            var initial_image = $(this).parent().find('input[name=initial_image]').val();
            var initial_image_name = $(this).parent().find('input[name=initial_image_name]').val();
            /*if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload').removeClass('hidden-button');
            } else {

            }*/
            $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
            $(this).parent().find('.btn-upload').removeClass('hidden-button');
            $(this).parent().find('.delete-link').hide();
        } else {
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload').addClass('hidden-button');
            $(this).parent().find('.delete-link').show();
        }
    });

    $(document).on('click', '.input-file-delete-link', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        var wrapper = current_delete_link.parent().children('input[type=file]').wrap('<form></form>').parent();
        wrapper.trigger('reset');
        current_delete_link.parent().find('input[type=file]').not('.original-data-holder-el, .original-image-data-holder-el').unwrap();
        current_delete_link.parent().children('.upload-img-button-container').addClass('hidden-button');
        current_delete_link.parent().children('.btn-upload').removeClass('hidden-button');
        current_delete_link.parent().find('input[type=file]').not('.original-data-holder-el, .original-image-data-holder-el').trigger('input-file-reset');
        current_delete_link.hide();
    });

    /**
     * FIN
     * Bouton d'upload de fichier
     */

    /**
     * Bouton d'action
     */
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
    /**
     * FIN
     * Bouton d'action
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
    if ($('.color-value:not(.original-data-holder-el)').length >0 ) {
        $('.color-value:not(.original-data-holder-el)').each( function() {
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

function uninstallColorPicker()
{
    if ($('.color-value:not(.original-data-holder-el)').length >0 ) {
        $('.color-value:not(.original-data-holder-el)').each(function(){
            $(this).minicolors('destroy');
        });
    }
}

function installJPlist()
{
    $('.chargementAjax').removeClass('hidden');
    $('.main-section').jplist({
        itemsBox: '.element-list',
        itemPath: '.element',
        panelPath: '.control-panel'
    });
    $('.jplist-no-results').removeClass('hidden-block');
    $('.chargementAjax').addClass('hidden');
}

$(document).ready(function(){
    $('.app-dialog').on('hidden.bs.modal', function(){
        $(this).find('.general-message.error-message-container').text('');
    });
});