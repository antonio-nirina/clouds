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
