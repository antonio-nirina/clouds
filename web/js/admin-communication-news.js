$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */
    $('#create-edit-news-modal').on('shown.bs.modal', function(){
        installWysiwyg();
        installColorPicker();
    });

    $('.create-news-button').on('click', function(){
        $('#create-edit-news-modal').modal('show');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
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