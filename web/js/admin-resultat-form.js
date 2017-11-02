//supprimer champs
$('.delele-field-row-link').on('click', function(e){
    e.preventDefault();

    var form_field_row = $(this).parents('.form-field-row');
    var field_id = form_field_row.attr('data-field-id');
    var delete_field_action_input = $('#form_structure_declaration_delete-field-action-list');

    var current_delete_field_action_list = delete_field_action_input.val();
    var new_delete_field_action_list = '';
    
    if("" == current_delete_field_action_list.trim() || "undefined" == typeof current_delete_field_action_list)
    {
        new_delete_field_action_list = field_id;
    }
    else
    {
        new_delete_field_action_list = current_delete_field_action_list + ',' + field_id;
    }
    delete_field_action_input.val(new_delete_field_action_list);
    $(this).parents('.form-field-row').remove();
});

$(".btn-valider.valider").on('click',function(e){
    e.preventDefault();

    if($('input[name=validate]').is(':checked'))
        $("#form_structure_declaration_validation-required").val(1);
    else
        $("#form_structure_declaration_validation-required").val(0);

    if($('input[name=piece_request]').is(':checked'))
        $("#form_structure_declaration_pieces-required").val(1);
    else
        $("#form_structure_declaration_pieces-required").val(0);

    if($('input[name=header]').is(':checked')) {
        $("#form_structure_declaration_text-head-required").val(1);
        var data = CKEDITOR.instances.editor.getData();
        $("#form_structure_declaration_text-head").val(data);
    }
    else
        $("#form_structure_declaration_text-head-required").val(0);
    
    // Statut des champs : A publier ET obligatoire
    var form_field_row_list = $('.form-field-row');
    var form_structure_current_field = [];
    form_field_row_list.each(function(){
        var published_state = $(this).find('.form-field-published').is(":checked") ? true : false;
        var mandatory_state = $(this).find('.form-field-mandatory').is(":checked") ? true : false;
        var form_structure_el = {
            "id": $(this).attr('data-field-id'),
            "published": published_state,
            "mandatory": mandatory_state
        };
        form_structure_current_field.push(form_structure_el);
    });
    $('#form_structure_declaration_current-field-list').val(JSON.stringify(form_structure_current_field));

    // Données des nouveaux champs
    var new_field_datas_list = [];
    var empty_label_state = false;
    var field_type_with_choice = ['choice-radio'];

    if($('.add-field-form-block').find('.add-field-form').length > 0)
    {
        var add_field_form_list = $('.add-field-form-block').find('.add-field-form-container').find('.add-field-form');
        add_field_form_list.each(function(){
            var new_field_label = $(this).find('.input-field-label');
            if('' != new_field_label.val().trim())
            {
                var new_field_datas_el = {
                    "label": $(this).find('.input-field-label').val(),
                    "mandatory": $(this).find('.checkbox-mandatory').is(":checked") ? true : false,
                    "field_type": $(this).find('.select-field-type').val()
                };

                if(field_type_with_choice.indexOf($(this).find('.select-field-type').val()) >= 0)
                {
                    if(
                        $(this).find('.select-field-type-container')
                            .find('.select-field-option-container')
                            .find('.option-container')
                            .find('.add-option-field')
                            .length > 0
                    ) {
                        var option_list = $(this).find('.select-field-type-container')
                            .find('.select-field-option-container')
                            .find('.option-container')
                            .find('.add-option-field')
                            .find('.input-option');
                        var option_value = {};
                        option_list.each(function(){
                            if('' != $(this).val().trim())
                            {
                                option_value[$(this).val()] = $(this).val();
                            }
                        });
                        new_field_datas_el["choices"] = option_value;
                    }
                }

                new_field_datas_list.push(new_field_datas_el);
            }
            else
            {
                empty_label_state = true;
            }
        });
    }
    $('#form_structure_declaration_new-field-list').val(JSON.stringify(new_field_datas_list));
    
    // Ordonnancement des champs
    var field_list = $('.sortable-table').find('tbody').find('tr.form-field-row');
    var field_list_order = [];
    if(field_list.length > 0)
    {
        field_list.each(function(){
            field_list_order.push($(this).attr('data-field-id'));
        });
    }
    $('#form_structure_declaration_field-order').val(JSON.stringify(field_list_order));

     $(this).parents().find('form').submit();
    // console.log($(this).parents().find('form').serialize());
});

$('.btn-valider.btn-next-product').on('click',function(){
    $("#form_structure_declaration_next").val('ok');
    $(".btn-valider.valider").trigger('click');
});

// Ajout nouveau formulaire d'ajout de champ
var nb_added = 0;
$('.add-field-link-declaration').on('click', function(e){
    e.preventDefault();

    var custom_field_allowed = $("input[name=custom-field-allowed]").val();
    if($('.add-field-form-block').find('.add-field-form-container').find('.add-field-form').length < custom_field_allowed)
    {
        var new_add_field_form = $(this).parents('.add-field-form-block').find('.add-field-form.template').clone();
        new_add_field_form.removeClass('template');
        nb_added++;
        new_add_field_form.find('.cl3-row').attr('id','cl3-row'+'-'+nb_added);
        new_add_field_form.find('.cl3-row+label').attr('for','cl3-row'+'-'+nb_added);
        $(this).parents('.add-field-form-block').find('.add-field-form-container').append(new_add_field_form);
        new_add_field_form.show();
    }
    else
    {
        alert(custom_field_allowed+' nouveau(x) champ(s) maximum');
    }
});

var max_add = $('input[name=max-allowed]').val();
for(var i=0;i<max_add;i++){
    $('.add-field-link-declaration').click();
    if (i === 1) { break; }
}
