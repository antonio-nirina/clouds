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
        initCalendar();
        initSelectChosen();
    });

    // création actu, appel formulaire
    $('.create-news-button').on('click', function(){
        $('#create-edit-news-modal').modal('show');
    });

    //boutton de programmation
    $(document).on('click', 'input.programmed-state-input', function() {
        var data_programmed_value = $(this).attr('data-programmed-value');
        console.log(data_programmed_value);
        if('true' == data_programmed_value){
            $('#create-edit-news-modal div.select-date').show();
        } else if ('false' == data_programmed_value){
            $('#create-edit-news-modal div.select-date').hide();
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Actualités
     * Création - Edition actu
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
     * *********************************************************************************************
     */
    $('#create-edit-news-modal').on('shown.bs.modal', function(){
        $(document).off('focusin.modal');
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Communication - Actualités
     * Activation des champs du plugin de création de lien dans wysiwyg ckeditor
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

function initCalendar() {
    $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);//langue datepicker
    $('#calendar').datepicker({
        minDate: new Date(),
        altField: ".post-launch-date",
        altFormat: "dd/mm/yy"
    });
}

function initSelectChosen() {
    $(".chosen-select").chosen({//hour selectable
        disable_search: true,
        width: "70px"
    });
}