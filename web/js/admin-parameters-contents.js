$(document).ready(function(){
	
    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Ajout de slide
     * *********************************************************************************************
     */
    $('.add-slide-link').on('click', function(e){
        e.preventDefault();
        var add_slide_url = $('input[name=add_slide_url]').val();
        if(add_slide_url.match(/__slide_type__/)){
            add_slide_url = add_slide_url.replace(/__slide_type__/, $(this).attr('data-slide-type'));
        }

        var slide_type = null;
        var current_add_slide_link = $(this);
        if("undefined" != typeof current_add_slide_link.attr('data-slide-type')){
            slide_type = current_add_slide_link.attr('data-slide-type');
        }
        $.ajax({
            type: 'GET',
            url: add_slide_url,
            success: function(html){
                if(html.match(/\d+/))
                {
                    var new_tab_content = addNewTabContent(slide_type);
                    var new_tab_nav = addNewTabNav(new_tab_content.attr('id'), slide_type);
                    new_tab_nav.find('.delete-tab').attr('data-slide-id', html);
                    new_tab_nav.trigger('click');
                    if(null != slide_type){
                        new_tab_content.find('.slide-type-input').val(slide_type);
                    }

                    renameExistentTab();

                    removeRadioOrder($('.tab-content .radio-order-form-element'));
                    radioOrder($('.tab-content .radio-order-form-element'));
                }
            }
        });
    });

    function addNewTabNav(target_id, slide_type=null)
    {
        var tab_nav_model = $('.nav-tabs-container').find('.block-model.tab-model').clone();
        tab_nav_model.removeClass('block-model');
        tab_nav_model.removeClass('tab-model');
        tab_nav_model.attr('href', '#'+target_id);

        if(null == slide_type){
            $('.nav-tabs-container').find('ul').find('.tab.add-option-tab').before(tab_nav_model);
        } else {
            if($('input[name=image_slide_type]').val() == slide_type){
                $('.nav-tabs-container').find('ul').find('.tab.add-image-slide-link').before(tab_nav_model);
            } else if($('input[name=video_slide_type]').val() == slide_type) {
                tab_nav_model.addClass('video-slide-nav-tab');
                $('.nav-tabs-container').find('ul').find('.tab.add-video-slide-link').before(tab_nav_model);
            }
        }
        tab_nav_model.show();

        /*var last_nav_tab = $('.nav-tabs-container').find('ul').find('.nav-tab').last();
        if(last_nav_tab.length > 0){
            last_nav_tab.after(tab_nav_model);
        } else {
            $('.nav-tabs-container').find('ul').find('.tab.add-option-tab').before(tab_nav_model);
        }
        tab_nav_model.show();*/
        return tab_nav_model;
    }

    function addNewTabContent(slide_type=null)
    {
        var new_tab_content_id = $('.tab-content').find('.tab-pane').length + 1;
        var tab_content_model = $('.block-model-container').find('.block-model.tab-content-model').clone();
        tab_content_model.removeClass('block-model');
        tab_content_model.removeClass('tab-content-model');
        tab_content_model.attr('id', 'tab-form-'+new_tab_content_id);
        tab_content_model.attr('data-tab-index', new_tab_content_id);
        var html_tab_content_model = tab_content_model.wrap('<div class="model-wrapper"></div>').parent().html();
        html_tab_content_model = html_tab_content_model.replace(/__name__/g, new_tab_content_id);
        var tab_content_container = $('.tab-content');
        tab_content_model = $($.parseHTML(html_tab_content_model));
        tab_content_container.append(tab_content_model);

        if(null == slide_type || $('input[name=image_slide_type]').val() == slide_type){
            tab_content_model.find('.video-slide-block').remove();
            tab_content_model.find('.image-slide-block').removeClass('image-slide-block');
        }

        if($('input[name=video_slide_type]').val() == slide_type){
            tab_content_model.find('.image-slide-block').remove();
            tab_content_model.find('.video-slide-block').removeClass('image-slide-block');
        }

        return tab_content_model;
    }

    function renameExistentTab()
    {
        var tab_list = $('.nav-tabs-container').find('.nav-tab').not('.video-slide-nav-tab');
        var index = 1;
        tab_list.each(function(){
            $(this).find('.tab-name-container').text('slide '+index);
            index++;
        });

        var video_slide_tab_list = $('.nav-tabs-container').find('.video-slide-nav-tab');
        var index = 1;
        video_slide_tab_list.each(function(){
            $(this).find('.tab-name-container').text('slide vidéo '+index);
            index++;
        });
    }

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Ajout de slide
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Suppression de slide
     * *********************************************************************************************
     */
    var part_delete_slide_url = $('input[name=delete_slide_url]').val();
    $(document).on('click', '.delete-tab', function(e){
        e.preventDefault();
        var slide_id = $(this).attr('data-slide-id');
        var delete_slide_url = part_delete_slide_url.replace(/__id__/, slide_id);
        var current_delete_link = $(this);
        $.ajax({
            type: 'GET',
            url: delete_slide_url,
            success: function(html){
                if(-1 != html.indexOf('OK'))
                {
                    var tab_content_id = current_delete_link.parents('.nav-tab').attr('href');

                    if (current_delete_link.parents('.nav-tab').prev('.nav-tab').length > 0) {
                        current_delete_link.parents('.nav-tab').prev('.nav-tab').trigger('click');
                    } else if (current_delete_link.parents('.nav-tab').next('.nav-tab').length > 0){
                        current_delete_link.parents('.nav-tab').next('.nav-tab').trigger('click');
                    } else {
                        if(current_delete_link.parents('.nav-tabs').find('.nav-tab').not(current_delete_link.parents('.nav-tab')).length > 0){
                            current_delete_link.parents('.nav-tabs').find('.nav-tab').not(current_delete_link.parents('.nav-tab')).first().trigger('click');
                        }
                    }
                    $(tab_content_id).remove();
                    current_delete_link.parents('.nav-tab').remove();

                    renameExistentTab();

                    removeRadioOrder($('.tab-content .radio-order-form-element'));
                    radioOrder($('.tab-content .radio-order-form-element'));
                }
            }
        });
    })

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Suppression de slide
     * *********************************************************************************************
     */

    /**
    * *********************************************************************************************
    * Paramétrages - Contenus - Portail d'identification
    * Validation
    * *********************************************************************************************
    */
    $('.btn-valider.submit-form').on('click', function(e){
        $('.tab-content-model').remove(); // to avoid the model to be considered as new slide form
        $('.block-model-container').remove(); // to avoid the model to be considered as new slide form
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification
     * Validation
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification
     * mini plugin ordre d'apparition slide
     * *********************************************************************************************
     */
    radioOrder($('.tab-content .radio-order-form-element'));

    function radioOrder(form_element)
    {
        var slide_number = $('.tab-content').find('.tab-pane').length;
        form_element.each(function(){
            var current_slide_index = $(this).parents('.tab-pane').attr('data-tab-index');
            for(i = 1; i <= slide_number; i++)
            {
                var slide_unit_order_container = $('.block-model-container .slide-unit-order-container-model').clone();
                slide_unit_order_container.find('.slide-unit-order-label-container').text(i);
                var slide_unit_order_input_id = 'slide-'+current_slide_index+'-order-'+i;
                slide_unit_order_container.find('label').attr('for', slide_unit_order_input_id);
                slide_unit_order_container.find('input[type=radio]').attr('id', slide_unit_order_input_id);
                slide_unit_order_container.find('input[type=radio]').val(i);
                slide_unit_order_container.find('input[type=radio]').attr('name', 'slide-'+current_slide_index+'-order');
                slide_unit_order_container.attr('data-order', i);
                slide_unit_order_container.removeClass('block-model');
                slide_unit_order_container.removeClass('slide-unit-order-container-model');
                $(this).parent().append(slide_unit_order_container);
                slide_unit_order_container.show();
                $(this).hide();
            }

            if('' !== $(this).val().trim())
            {
                if($(this).val() > slide_number)
                {
                    $(this).parent('')
                        .find('.slide-unit-order-container[data-order='+slide_number+']')
                        .find('input[type=radio]').prop('checked', true);
                }
                else
                {
                    $(this).parent('')
                        .find('.slide-unit-order-container[data-order='+$(this).val()+']')
                        .find('input[type=radio]').prop('checked', true);
                }
            }
            else
            {
                $(this).parent('')
                    .find('.slide-unit-order-container[data-order='+current_slide_index+']')
                    .find('input[type=radio]').prop('checked', true);
                $(this).val(current_slide_index);
            }
        });
    }

    function removeRadioOrder(form_element)
    {
        form_element.each(function(){
            $(this).parent().find('.slide-unit-order-container').remove();
        });
    }

    $(document).on('click', '.slide-unit-order-container input[type=radio]', function(e){
        $(this).parents('.slide-unit-order-container').parent().find('.radio-order-form-element').val($(this).val());
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification
     * mini plugin ordre d'apparition slide
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Upload image
     * *********************************************************************************************
     */
    $(document).on('click', '.btn-upload.choose-upload-img-button', function(e){
        e.preventDefault();
        $(this).parent().find('.slide-image-input').trigger('click');
    });

    /*$(document).on('click', '.upload-img-button', function(e){
        e.preventDefault();
        $(this).parent().find('.slide-image-input').trigger('click');
    });*/

    $(document).on('click', '.upload-img-button-container', function(e){
        e.preventDefault();
        $(this).parent().find('.slide-image-input').trigger('click');
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Upload image
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Gestion bouton et preview
     * *********************************************************************************************
     */
    //  preview d'image après choix d'image
    function createImagePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                // $(input).parents('.tab-pane').find('.login-portal-image-preview').attr('src', e.target.result);
                $(input).parents('.tab-pane').find('.slide-image-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).on('change', '.slide-image-input', function(){
        if('' == $(this).val().trim()){
            var initial_image = $(this).parent().find('input[name=initial_image]').val();
            var initial_image_name = $(this).parent().find('input[name=initial_image_name]').val();
            if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button').addClass('hidden-button');
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload.choose-upload-img-button').removeClass('hidden-button');
                $(this).parents('.tab-pane').find('.slide-image-preview-container').addClass('no-image');
                $(this).parents('.tab-pane').find('.slide-image-preview-container .slide-image-preview').attr('src', '');
            } else {
                $(this).parent().find('.upload-img-button').find('.img-name-container').text(initial_image_name);
                $(this).parents('.tab-pane').find('.slide-image-preview-container .slide-image-preview').attr('src', initial_image);
            }
        } else {
            createImagePreview(this);
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
            $(this).parent().find('.upload-img-button').removeClass('hidden-button');
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload.choose-upload-img-button').addClass('hidden-button');
            $(this).parents('.tab-pane').find('.slide-image-preview-container').removeClass('no-image');
            $(this).parent().find('.delete-link').show();
        }
        $(this).parents('.tab-pane').find('.delete-command-input').val(false);
    })

    $(document).on('input', '.slide-message-input', function(){
        $(this).parents('.tab-pane').find('.slide-message-preview').text($(this).val());
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification, Page d'accueil
     * Gestion bouton et preview
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Page d'accueil
     * Section active
     * *********************************************************************************************
     */
    $('.fieldset').on('mouseenter', function(e){
        $('.fieldset').removeClass('active');
        $(this).addClass('active');

        $('.cke_wysiwyg_frame').contents().find('body').css(
            'background-color', '#F5F5F5'
        )
        $(this).find('iframe.cke_wysiwyg_frame').contents().find('body').css(
            'background-color', '#FFFFFF'
        );
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Page d'accueil
     * Section active
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Page d'accueil
     * Preview d'image de header - sans image
     * *********************************************************************************************
     */
    /*var image_preview_img = $('img.slide-image-preview');
    if('undefined' != typeof image_preview_img)
    {
        if(image_preview_img.attr('src').trim().length <= 0)
        {
            image_preview_img.parents('.slide-image-preview-container').addClass('no-image');
        }
    }*/

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Page d'accueil
     * Preview d'image de header - sans image
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Inscriptions - Portail d'identification
     * Suppression image header uploadé
     * *********************************************************************************************
     */
    /*
     * AJAX METHOD
     *
    $('.delete-slide-image').on('click', function(e){
        e.preventDefault();
        var delete_slide_image_url = $(this).parent().find('input[name=delete_slide_image_url]').val();
        var current_delete_link = $(this);
        $.ajax({
            type: 'GET',
            url: delete_slide_image_url,
            success: function(html){
                if(-1 != html.indexOf('OK'))
                {
                    var wrapper = current_delete_link.parent().find('input[type=file]').wrap('<form></form>').parent();
                    wrapper.trigger('reset');
                    current_delete_link.parent().find('input[type=file]').unwrap();

                    current_delete_link.parent().find('.upload-img-button').addClass('hidden-button');
                    current_delete_link.parent().find('.choose-upload-img-button').removeClass('hidden-button');
                    current_delete_link.parents('.tab-pane').find('.slide-image-preview-container .slide-image-preview')
                        .attr('src', '');
                    current_delete_link.parents('.tab-pane').find('.slide-image-preview-container').addClass('no-image');
                    current_delete_link.hide();
                }
            }
        });
    });*/
    $(document).on('click', '.delete-slide-image', function(e){
        e.preventDefault();
        $(this).parents('.tab-pane').find('.delete-command-input').val(true);
        var current_delete_link = $(this);

        var wrapper = current_delete_link.parent().find('input[type=file]').wrap('<form></form>').parent();
        wrapper.trigger('reset');
        current_delete_link.parent().find('input[type=file]').unwrap();

        current_delete_link.parent().find('.upload-img-button').addClass('hidden-button');
        current_delete_link.parent().find('.upload-img-button-container').addClass('hidden-button');
        current_delete_link.parent().find('.choose-upload-img-button').removeClass('hidden-button');
        current_delete_link.parents('.tab-pane').find('.slide-image-preview-container .slide-image-preview')
            .attr('src', '');
        current_delete_link.parents('.tab-pane').find('.slide-image-preview-container').addClass('no-image');
        current_delete_link.hide();
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions - Portail d'identification
     * Suppression image header uploadé
     * *********************************************************************************************
     */
	$(document).on('click', 'span#nom-domaine-choix-ok' ,function(){
		if($('ul.list-choix-nom-domaine').is(':visible')){
			$('ul.list-choix-nom-domaine').hide();
			$('span#nom-domaine-choix-ok').removeClass('select-liste-nom-domaine-select');
			$('span#nom-domaine-choix-ok').addClass('select-liste-nom-domaine');
			$('img.img-select-bas').show();
		}else{
			$('ul.list-choix-nom-domaine').show();
			$('span#nom-domaine-choix-ok').addClass('select-liste-nom-domaine-select');
			$('span#nom-domaine-choix-ok').removeClass('select-liste-nom-domaine');
			$('img.img-select-bas').hide();
		}
	});
	
	$(document).on('click', 'ul.list-choix-nom-domaine li', function(){
		var IdLigne = $(this).attr('id');
		var ArrayIdLigne = new Array;
		ArrayIdLigne = IdLigne.split('-');
		var Id = ArrayIdLigne[3];
		
		var Valeur = $('li#nom-domaine-choix-'+Id+'').html();
		$('span#nom-domaine-choix-ok a').html('');
		$('span#nom-domaine-choix-ok a').html(Valeur);
		AfficheUrlRecompense();
	});
});

$(document).ready(function(){
	AfficheUrlRecompense();
	$(document).on('keyup', 'input#login_portal_data_form_http', function(){
		AfficheUrlRecompense();
	});
});

function AfficheUrlRecompense(){
	var NomSousDomaine = $('input#login_portal_data_form_http').val();
	var NomDomaine = $('span#nom-domaine-choix-ok a').html();
	var UrlRecompense = 'http://'+NomSousDomaine+NomDomaine+'';
	$('span#adresse-site-recompense').html(UrlRecompense);
}