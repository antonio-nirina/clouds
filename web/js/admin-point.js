$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Paramétrages - Points - Produits
     * checkbox option
     * *********************************************************************************************
     */
    $(document).on('click', '.point-attribution-option', function(e){
        $(this).parents('.tab-pane').find('.point-attribution-option').not($(this)).prop('checked', false);
        if(!$(this).is(':checked')){
            $(this).prop('checked', true);
        }
    });

    /**
     * *********************************************************************************************
     * FIn
     * Paramétrages - Points - Produits
     * checkbox option
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Paramétrages - Points - Produits
     * Ajout produit
     * *********************************************************************************************
     */
    $('.add-product-link').on('click', function(e){
        e.preventDefault();
        if($(this).parent().find('.nav-tab').length < 5){
            var add_product_url = $('input[name=add_product_url]').val();
            $.ajax({
                type: 'GET',
                url: add_product_url,
                success: function(html){
                    if(html.match(/\d+/)){
                        var new_tab_content = addNewTabContent();
                        var new_tab_nav = addNewTabNav(new_tab_content.attr('id'));
                        new_tab_nav.find('.delete-tab').attr('data-product-group', parseInt($(html).text()));
                        new_tab_nav.trigger('click');
                        nameTab(new_tab_nav, parseInt($(html).text()));
                        new_tab_content.find('.product-group-input').val(parseInt($(html).text()));
                    }
                }
            });
        } else {
            alert('nombre de produits maximum déjà atteint');
        }
    });

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
        tab_content_model = $($.parseHTML(html_tab_content_model));
        tab_content_container.append(tab_content_model);
        tab_content_model.find('.point-attribution-option').first().prop('checked', true);
        return tab_content_model;
    }

    function addNewTabNav(target_id)
    {
        var tab_nav_model = $('.nav-tabs-container').find('.block-model.tab-model').clone();
        tab_nav_model.removeClass('block-model');
        tab_nav_model.removeClass('tab-model');
        tab_nav_model.attr('href', '#'+target_id);
        $('.nav-tabs-container').find('ul').find('.tab.add-option-tab').before(tab_nav_model);
        tab_nav_model.show();
        return tab_nav_model;
    }

    function nameTab(tab, index)
    {
        if(index == 1){
            tab.find('.tab-name-container').text('CA ou produit '+index);
        } else {
            tab.find('.tab-name-container').text('produit '+index);
        }
    }
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Points - Produits
     * Ajout produit
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
     * Suppression produit
     * *********************************************************************************************
     */
    function renameExistentTab()
    {
        var tab_list = $('.nav-tabs-container').find('.nav-tab');
        var index = 1;
        tab_list.each(function(){
            if(1 == index){
                $(this).find('.tab-name-container').text('CA ou produit '+index);
            } else {
                $(this).find('.tab-name-container').text('produit '+index);
            }
            index++;
        });
    }

    var part_delete_product_url = $('input[name=delete_product_url]').val();
    $(document).on('click', '.delete-tab', function(e){
        e.preventDefault();
        var product_group = $(this).attr('data-product-group');
        var delete_product_url = part_delete_product_url.replace(/___pg___/, product_group);
        var current_delete_link = $(this);
        $.ajax({
            type: 'GET',
            url: delete_product_url,
            success: function(html){
                if(-1 != html.indexOf('OK')){
                    var tab_content_id = current_delete_link.parents('.nav-tab').attr('href');
                }

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

                // redefine product group
                var product_group_input_list = $('.tab-content').find('.product-group-input');
                var product_group_value = 1;
                product_group_input_list.each(function(){
                    $(this).val(product_group_value);
                    product_group_value++;
                });

            }
        });
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Contenus - Portail d'identification
     * Suppression produit
     * *********************************************************************************************
     */
});