$(document).ready(function(){
    /**
     * Choix multiples
     */
    // selection (choix multiple select)
    $(document).on('click', '.styled-choice-select .dropdown-menu .dropdown-item', function(e){
        e.preventDefault();
        var data_value = $(this).attr('data-value');
        $(this).parents('.styled-choice-select').find('select').find('option[value="'+data_value+'"]').prop('selected', true);
        $(this).parents('.dropdown').find('button.dropdown-toggle').text($(this).text());
        $(this).parents('.dropdown').find('.delete-select').css({'visibility':'visible','display':'inline-block'});
    });

    // suppression du choix dans choix multiple select
    $(document).on('click', '.delete-select', function(e){
        e.preventDefault();
        var placeholder_option = $(this).parents('.styled-choice-select').find('select').find('option[value=""]');
        if(placeholder_option.length > 0){
            placeholder_option.prop('selected', true);
        } else {
            $(this).parents('.styled-choice-select').find('select').find('option').first().prop('selected', true);
        }

        var button_toggle = $(this).parents('.styled-choice-select').find('button.dropdown-toggle');
        button_toggle.text(button_toggle.attr('data-default-button-text'));
        $(this).hide();
    });
    /**
     * FIN
     * Choix multiples
     */

    /**
     * Mise à jour de données de formulaire - "qui verra?"
     */
    $(document).on('click', '.app-dialog .viewer-authorization-type-choice .dropdown .dropdown-item', function(){
        $(this).parents('.app-dialog').find('.authorized-viewer-role-input').val($(this).text());
    });

    $(document).on('click', '.app-dialog .viewer-authorization-type-choice .delete-select', function(){
        var modal = $(this).parents('.app-dialog');
        var toggle_button = modal.find('.viewer-authorization-type-choice button.toggle-button');
        modal.find('.authorized-viewer-role-input').val(toggle_button.attr('data-default-button-text'));
    });
    /**
     * FIIN
     * Mise à jour de données de formulaire - "qui verra?"
     */

    /**
     * Bouton d'upload de fichier
     */
    $(document).on('click', '.input-file-trigger', function(e){
        e.preventDefault();
        $(this).parent().children('.hidden-input-file').trigger('click');
    });

    $(document).on('change', '.hidden-input-file', function(){
        if('' == $(this).val().trim()){
            var initial_image = $(this).parent().find('input[name=initial_image]').val();
            var initial_image_name = $(this).parent().find('input[name=initial_image_name]').val();
            /*if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload').removeClass('hidden-button');
            } else {

            }*/
            $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
            $(this).parent().find('.btn-upload').removeClass('hidden-button');
            $(this).parent().find('.delete-link').hide();
        } else {
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload').addClass('hidden-button');
            $(this).parent().find('.delete-link').show();
        }
    });

    $(document).on('click', '.input-file-delete-link', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        var wrapper = current_delete_link.parent().children('input[type=file]').wrap('<form></form>').parent();
        wrapper.trigger('reset');
        current_delete_link.parent().find('input[type=file]').not('.original-data-holder-el, .original-image-data-holder-el').unwrap();
        current_delete_link.parent().children('.upload-img-button-container').addClass('hidden-button');
        current_delete_link.parent().children('.btn-upload').removeClass('hidden-button');
        current_delete_link.parent().find('input[type=file]').not('.original-data-holder-el, .original-image-data-holder-el').trigger('input-file-reset');
        current_delete_link.hide();
    });

    /**
     * FIN
     * Bouton d'upload de fichier
     */

    /**
     * Bouton d'action
     */
    // aperçu bouton d'action, texte
    $(document).on('input', '.action-button-text-input', function(){
        $(this).parents('.action-button-block-container').find('.action-button-preview').text($(this).val());
    });

    $(document).on('click', '.delete-action-button-text', function(){
        $(this).parents('.action-button-block-container').find('.action-button-preview').text('');
    });

    // aperçu bouton, couleur de bouton (couleur de fond)
    $(document).on('change', '.action-button-background-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block-container').find('.action-button-preview').css("background-color", $(this).val());
    });

    // aperçu bouton, couleur texte
    $(document).on('change', '.action-button-text-color', function(e){
        e.preventDefault();
        $(this).parents('.action-button-block-container').find('.action-button-preview').css("color", $(this).val());
    });
    /**
     * FIN
     * Bouton d'action
     */

    /**
     * Reordering content element
     */
    $(document).on('click', '.reorder-up', function(e){
        e.preventDefault();
        var reorder_container = $(this).parents('.reorder');
        if (!reorder_container.hasClass('disabled')) {
            var current_element = $(this).parents('.content-list-element');
            var upper_element = current_element.prev('.content-list-element');
            if (upper_element.length > 0) {
                upper_element.before(current_element);
                reorder_container.trigger('reordering-content');
            }
        }
    });

    $(document).on('click', '.reorder-down', function(e){
        e.preventDefault();
        var reorder_container = $(this).parents('.reorder');
        if (!reorder_container.hasClass('disabled')) {
            var current_element = $(this).parents('.content-list-element');
            var lower_element = current_element.next('.content-list-element');
            if (lower_element.length > 0) {
                lower_element.after(current_element);
                reorder_container.trigger('reordering-content');
            }
        }
    });
    /**
     * FIN
     * Reordering content element
     */

    /**
     * Dans liste d'éléments, suppression recherche, selection
     */
    // suppression recherche
    $('.element-name-search-container .delete-input').on('click', function(){
        var associated_input = $(this).prev('.element-name-search-input');
        associated_input.val('');
        associated_input.trigger('keyup');
    });

    // suppression filtre par statut (publié, en attente, etc)
    // affichage bouton de suppression
    $(document).on('click', '.send-state-filter-container .status-filter-drop-down ul li', function(){
        $(this).parents('.send-state-filter-container').find('.delete-input').show();
    });
    // gestion de suppression de selection
    $(document).on('click', '.send-state-filter-container .delete-input', function(){
        $(this).parents('.send-state-filter-container').find('.status-filter-drop-down ul li span[data-path=default]').parent('li').click();
        $(this).hide();
    });

    // suppression selection action de groupe
    // affichage bouton de suppression
    $(document).on('click', '.selected-elements-button-container .dropdown .dropdown-menu a', function(){
        $(this).parents('.dropdown').next('.delete-input').show();
    });
    // gestion de suppression de selection
    $(document).on('click', '.selected-elements-button-container .delete-input', function(){
        var selected_element_container = $(this).parents('.selected-elements-button-container');
        var dropdown_toggle = selected_element_container.find('.dropdown button.dropdown-toggle');
        dropdown_toggle.text(dropdown_toggle.attr('data-default-text'));
        selected_element_container.find('.button-container .btn-valider').attr('data-grouped-action', '');
    });
    /**
     * FIN
     * Dans liste d'éléments, suppression recherche, selection
     */

    /**
     * Actions de groupe
     */
    $(document).on('change', '.element-data-container .styled-checkbox', function(){
        var checked = getChecked();
        var one_selected_element_text = $('input[name=multiple_selection_one_selected_element_text]').length > 0 ? $('input[name=multiple_selection_one_selected_element_text]').val() : ' élément sélectionné';
        var many_selected_elements_text = $('input[name=multiple_selection_many_selected_elements_text]').length > 0 ? $('input[name=multiple_selection_many_selected_elements_text]').val() : ' éléments sélectionnés';
        var text = (checked.length == 1)?checked.length+one_selected_element_text:((checked.length > 1)?checked.length+many_selected_elements_text:"");
        $(".selected-elements .selected-count input").val(text);
        if (checked.length > 0) {
            $('.selected-elements').css('display',"flex");
            $('.selected-elements .selected-count .delete-input').css('display','block');
        } else {
            $('.selected-elements').css('display',"none");
            $('.selected-elements').trigger('hide-block');
        }
    });

    // suppression des selections effectuées
    $(document).on('click', '.selected-elements .selected-count .delete-input', function(){
        $('.element-data-container .styled-checkbox').each(function(){
            $(this).prop('checked', false);
        });
        $('.selected-elements').css('display',"none");
        $('.selected-elements').trigger('hide-block');
    });

    // Selection de type d'action de groupe
    $(document).on('click', '.grouped-action-choice', function(e){
        e.preventDefault();
        var selected_element_container = $(this).parents('.selected-elements-button-container');
        selected_element_container.find('button.dropdown-toggle').text($(this).text());
        selected_element_container.find('.button-container .btn-valider').attr('data-grouped-action', $(this).attr('data-grouped-action'));
    });

    // action à la fermeture du block d'action de groupe
    $(document).on('hide-block', '.selected-elements', function(){
        var dropdown_toggle_button = $(this).find('.selected-elements-button-container').find('.dropdown').find('button.dropdown-toggle');
        dropdown_toggle_button.text(dropdown_toggle_button.attr('data-default-text'));
        $(this).find('.selected-elements-button-container').find('.button-container .btn-valider').attr('data-grouped-action', '');
        $(this).find('.selected-elements-button-container').find('.dropdown-container .delete-input').hide();
        checked = [];
    });

    // TODO : implementing general logic
    // soumission action de groupe
    $(document).on('click', '.selected-elements .selected-elements-button-container .btn-valider', function(e){
        e.preventDefault();
        /*if ('undefined' !== typeof $(this).attr('data-grouped-action') && '' != $(this).attr('data-grouped-action')){
            $('.chargementAjax').removeClass('hidden');
            var arr_checked = getChecked();
            var str_checked = arr_checked.join(',');
            var data = {'element_id_list': str_checked, 'grouped_action_type': $(this).attr('data-grouped-action')};
            if ($('input[name=welcoming_news_post_type]').length > 0 && 'true' == $('input[name=welcoming_news_post_type]').val()){
                if ($('input[name=archived_state]').length > 0 && 'true' == $('input[name=archived_state]').val()) {
                    var redirection_url = $('input[name=archived_welcoming_news_post_list_url]').val();
                } else {
                    var redirection_url = $('input[name=welcoming_news_post_list_url]').val();
                }
            } else {
                if ($('input[name=archived_state]').length > 0 && 'true' == $('input[name=archived_state]').val()) {
                    var redirection_url = $('input[name=archived_news_post_list_url]').val();
                } else {
                    var redirection_url = $('input[name=news_post_list_url]').val();
                }
            }
            $.ajax({
                type: 'POST',
                url: $(this).attr('data-target-url'),
                data: data,
                success: function(){
                    window.location.replace(redirection_url);
                },
                complete: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }*/
    });

    /**
     * FIN
     * Actions de groupe
     */

    /**
     * Incrémentation - Décrémentation nombre éléments de liste selectionnés
     */
    $(document).on('click', '.element-list .element-data-container .styled-checkbox', function(){
        if($(this).is(':checked')){
            checked.push($(this).attr('id'));
        } else {
            checked.splice(checked.indexOf($(this).attr('id')), 1);
        }
    });
    /**
     * FIN
     * Incrémentation - Décrémentation nombre éléments de liste selectionnés
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
    if ($('.color-value:not(.original-data-holder-el)').length >0 ) {
        $('.color-value:not(.original-data-holder-el)').each( function() {
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

function uninstallColorPicker()
{
    if ($('.color-value:not(.original-data-holder-el)').length >0 ) {
        $('.color-value:not(.original-data-holder-el)').each(function(){
            $(this).minicolors('destroy');
        });
    }
}

function installJPlist()
{
    $('.chargementAjax').removeClass('hidden');
    $('.main-section').jplist({
        itemsBox: '.element-list',
        itemPath: '.element',
        panelPath: '.control-panel'
    });
    $('.jplist-no-results').removeClass('hidden-block');
    $('.chargementAjax').addClass('hidden');
}

$(document).ready(function(){
    $('.app-dialog').on('hidden.bs.modal', function(){
        $(this).find('.general-message.error-message-container').text('');
    });
});

var checked = [];
function getChecked() {
    return checked;
}
