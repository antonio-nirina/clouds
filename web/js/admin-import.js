$(document).ready(function(){
    //declartion import
    $('.btn-valider.btn-download').on('click',  function(){
        $('form[name=result_setting]').submit();
    });
    $('.btn-valider.btn-upload').on('click',  function(e){     
        e.preventDefault();
        if ($(this).next('form').length > 0) {
            $(this).next('form').find("input[type=file]").click();
        } else {
            $(this).next("input[type=file]").click();
        }
    });
    $('.upload-img-button').on('click', function(e) {
        e.preventDefault();        
        $(this).next('.btn-valider.btn-upload').click();
    });
    $('.btn-valider.btn-upload').next("input[type=file]").on('change', function() {  
        var image_file_name = $(this).val().split('\\').pop();
        $(this).parent().find('.upload-img-button').css('background-position', '15px');
        $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
        $(this).parent().find('.upload-img-button').removeClass('hidden-button');
        $(this).prev('.btn-valider.btn-upload').addClass('hidden-button');
    });
    $("#result_setting_upload_uploaded_file").on('change',function() {
        $(this).parents('form').submit();
    });
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
                if( typeof console === 'object' ) {
                  console.log(value);
                }
              },
              theme: 'bootstrap'
            });
        });
    } 

    $('.fieldset').on('mouseenter',function(){
        $('.fieldset.active').removeClass('active').addClass('inactive');        
        if(!($(this).hasClass('active'))) {
            $(this).addClass('active');
        }        
        if($(this).hasClass('inactive')) {
            $(this).removeClass('inactive');
        }
    });
});