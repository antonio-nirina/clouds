/**
 * Created by user on 09/04/2018.
 * @author Bocasay
 * Behaviour of from in banner e-learning
 *
 */

$(document).ready(function() {
    /**
     * *********************************************************************************************
     * Behaviour - preview
     * Image
     * *********************************************************************************************
     */
    $('.btn-upload.choose-upload-img-button').on('click', function(e){
        e.preventDefault();
        $('.header-image-input').trigger('click');
    });

    /*$('.upload-img-button').on('click', function(e){
     e.preventDefault();
     $('.header-image-input').trigger('click');
     });*/
    $('.upload-img-button-container').on('click', function(e){
        e.preventDefault();
        $('.header-image-input').trigger('click');
    });

    //  preview d'image après choix d'image
    function createImagePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.header-image-preview-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


    $('.header-image-input').on('change', function(){
        if('' == $(this).val().trim()){
            var initial_image = $(this).parent().find('input[name=initial_image]').val();
            var initial_image_name = $(this).parent().find('input[name=initial_image_name]').val();
            if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button').addClass('hidden-button');
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload.choose-upload-img-button').removeClass('hidden-button');
                $(this).parents('.fieldset').find('.header-image-preview-img').attr('src', '');
                $(this).parents('.fieldset').find('.header-preview-container').addClass('no-image');
                $(this).parent().find('.delete-link').hide();
            } else {
                $(this).parent().find('.img-name-container').text(initial_image_name);
                $(this).parents('.fieldset').find('.header-image-preview-img').attr('src', initial_image);
            }
        } else {
            createImagePreview(this);
            var image_file_name = $(this).val().split('\\').pop();
            $('.upload-img-button').css('background-position', '15px');
            $('.upload-img-button').find('.img-name-container').text(image_file_name);
            $('.upload-img-button').removeClass('hidden-button');
            $('.upload-img-button-container').removeClass('hidden-button');
            $('.btn-upload.choose-upload-img-button').addClass('hidden-button');
            $('.header-preview-container').removeClass('no-image');
            $(this).parent().find('.delete-link').show();
        }
        $(this).parents('form').find('.delete-command-input').val(false);
    });

    // preview du message
    $('.header-message-input').on('input', function(){
        $('.header-message-preview').text($(this).val());
    });

    /**
     * *********************************************************************************************
     * Behaviour - Delete
     * Image
     * *********************************************************************************************
     */

    $(document).on('click', '.delete-form-header-image', function(e){
        e.preventDefault();
        $(this).parents('form').find('.delete-command-input').val(true);
        var current_delete_link = $(this);

        var wrapper = current_delete_link.parent().find('input[type=file]').wrap('<form></form>').parent();
        wrapper.trigger('reset');
        current_delete_link.parent().find('input[type=file]').unwrap();

        current_delete_link.parent().find('.upload-img-button').addClass('hidden-button');
        current_delete_link.parent().find('.upload-img-button-container').addClass('hidden-button');
        current_delete_link.parent().find('.choose-upload-img-button').removeClass('hidden-button');
        current_delete_link.parents('.fieldset').find('.row-header-preview-container .header-image-preview-img')
            .attr('src', '');
        current_delete_link.parents('.fieldset').find('.header-preview-container').addClass('no-image');
        current_delete_link.hide();
    });


});
