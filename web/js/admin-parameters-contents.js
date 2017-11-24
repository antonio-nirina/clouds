$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Paramétrages - Contenus - Portail d'identification
     * Ajout de slide
     * *********************************************************************************************
     */
    var add_slide_url = $('input[name=add_slide_url]').val();
    $('.add-slide-link').on('click', function(e){
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: add_slide_url,
            success: function(html){
                if(-1 != html.indexOf('OK'))
                {
                    var new_tab_content = addNewTabContent();
                    var new_tab_nav = addNewTabNav(new_tab_content.attr('id'));
                    new_tab_nav.trigger('click');
                    renameExistentTab();

                    removeRadioOrder($('.tab-content .radio-order-form-element'));
                    radioOrder($('.tab-content .radio-order-form-element'));
                }
            }
        });
    });

    function addNewTabNav(target_id)
    {
        var tab_nav_model = $('.nav-tabs-container').find('.block-model.tab-model').clone();
        tab_nav_model.removeClass('block-model');
        tab_nav_model.removeClass('tab-model');
        tab_nav_model.attr('href', '#'+target_id);
        var last_nav_tab = $('.nav-tabs-container').find('ul').find('.nav-tab').last();
        last_nav_tab.after(tab_nav_model);
        tab_nav_model.show();
        return tab_nav_model;
    }

    function addNewTabContent()
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
        tab_content_container.append($($.parseHTML(html_tab_content_model)));
        return tab_content_model;
    }

    function renameExistentTab()
    {
        var tab_list = $('.nav-tabs-container').find('.nav-tab');
        var index = 1;
        tab_list.each(function(){
            $(this).find('.tab-name-container').text('slide '+index);
            index++;
        });

    }

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification
     * Ajout de slide
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
                $(this).parent('')
                    .find('.slide-unit-order-container[data-order='+$(this).val()+']')
                    .find('input[type=radio]').prop('checked', true);
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
});