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