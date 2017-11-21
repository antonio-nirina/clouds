$(document).ready(function(){
    //declartion import
    $('.btn-valider.btn-download').on('click',  function(){
        $('form[name=result_setting]').submit();
    });
    $('.btn-valider.btn-upload').on('click',  function(){
        if ($(this).next('form').length > 0) {
            $(this).next('form').find("input[type=file]").click();
        } else {
            $(this).next("input[type=file]").click();
        }
    });
    $('.btn-valider.btn-upload').on('click',  function(){
        $(this).next('form').find("input[type=file]").click();
    });
    $("#result_setting_upload_uploaded_file").on('change',function() {
        $(this).parents('form').submit();
    });

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
                if( typeof console === 'object' ) {
                  console.log(value);
                }
              },
              theme: 'bootstrap'
            });
        });
    }    
});