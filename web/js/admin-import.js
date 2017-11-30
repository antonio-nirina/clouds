$(document).ready(function(){
    // tableau - réseau
    $('.table-network .form-link input[type=text]').each(function(){
        if ($(this).val()) {
            $(this).next('.delete-input').css('display','inline-block');
        } else {
            $(this).next('.delete-input').css('display','none');
        }
        $(this).parent().next('div').css('display','none');   
    });
    $('.table-network .form-link input[type=text]').on('keyup', function(){
        if ($(this).val()) {
            $(this).next('.delete-input').css('display','inline-block');            
        } else {
            $(this).next('.delete-input').css('display','none');
        }
        $(this).parent().next('div').css('display','block');
    });
    function submit_table_network() 
    {        
        var url = $('input[name=redirect]').val();
        $('form[name=site_table_network_setting').ajaxSubmit({
            type: 'POST',
            dataType: 'json',
            url: url,
            success: function(){
            }
        });
    }
    $('.table-network input.mode').on('change', function() {
        submit_table_network();
    });
    $('.table-network .delete-input').on('click', function() {
        $(this).prev('.form-link input[type=text]').val('');
        submit_table_network();
        $(this).css('display','none');
        $(this).parent().next('div').css('display','none');
    });
    $('.table-network .btn-valider').on('click', function() {
        submit_table_network();
        $(this).parent().css('display','none');
    });

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
    $('.initialize').on('click',function(e){
        e.preventDefault();
        $('#site_design_setting_colors_couleur_1').val('#1d61d4');
        $('#site_design_setting_colors_couleur_1_bis').val('#598fea');
        $('#site_design_setting_colors_couleur_2').val('#7682da');
        $('#site_design_setting_colors_couleur_3').val('#505050');
        $('#site_design_setting_colors_couleur_4').val('#505050');
        $('#site_design_setting_colors_couleur_5').val('#807f81');
        $('#site_design_setting_colors_couleur_6').val('#ebeeef');
        $(this).parents('form').submit();
    });
    $('.upload-img-button').on('click', function(e) {
        e.preventDefault();        
        $(this).next('.btn-valider.btn-upload').click();
    });

    $('.delete-upload').on('click', function(e){
        var $el = $(this).parent().find('input[type=file]');
        $el.wrap('<form>').closest('form').get(0).reset();
        $el.unwrap();
        $(this).next('input[type=hidden]').val('');
        $(this).parent().find('.upload-img-button').addClass('hidden-button');
        $(this).addClass('hidden-button');
        $(this).parent().find('.btn-valider.btn-upload').removeClass('hidden-button');
    });

    $('.btn-valider.btn-upload').next("input[type=file]").on('change', function() {  
        var image_file_name = $(this).val().split('\\').pop();
        if (image_file_name.length >0) {
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').find('.img-name-container').html(image_file_name);
            $(this).parent().find('.upload-img-button').removeClass('hidden-button');
            $(this).parent().find('.delete-upload').removeClass('hidden-button');
            $(this).prev('.btn-valider.btn-upload').addClass('hidden-button'); 
        } else {
            $(this).parent().find('.upload-img-button').addClass('hidden-button');
            $(this).parent().find('.delete-upload').addClass('hidden-button');
            $(this).prev('.btn-valider.btn-upload').removeClass('hidden-button');
        }
              
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