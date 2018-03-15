$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */
    $('.chargementAjax').removeClass('hidden');
    $('.main-section').jplist({
        itemsBox: '.element-list',
        itemPath: '.element',
        panelPath: '.control-panel'
    });
    $('.jplist-no-results').removeClass('hidden-block');
    $('.chargementAjax').addClass('hidden');
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
    $('.create-e-learning-button').on('click', function(e){
        e.preventDefault();
        $('#create-edit-e-learning-modal').modal('show');
        installWysiwyg();
        installColorPicker()
    });

    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning
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
