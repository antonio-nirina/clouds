//supprimer champs
$('.tab-content').on('click', '.delele-field-row-link', function(e){
    e.preventDefault();

    var input_custom = $(this).parents('.tab-pane').find("input[name=custom-field-allowed]");
    var custom_field_allowed = input_custom.val();
    var btn_add = $(this).parents('.tab-pane').find('.add-field-link-declaration');
    var tr_perso = $(this).parents('.tab-pane').find('.head-personalized');

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

    //affichage bouton ou non   
    next_perso = tr_perso.next('tr');
    if (next_perso.length == 0 || 'undefined' == typeof next_perso)
        tr_perso.css('display','none');
    custom_field_allowed = parseInt(custom_field_allowed) + 1;
    input_custom.val(custom_field_allowed);
    if( custom_field_allowed > 0) {
        btn_add.html("ajouter un champ ("+custom_field_allowed+" maximum)");
        btn_add.parents('.add-field-form-block table').css('display','table');
    }
    else
        btn_add.parents('.add-field-form-block table').css('display','none');

});

//valider les modifications
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
                    "field_type": $(this).find('.select-field-type').val(),
                    "level": $(this).parents('.tab-pane').find('input[name=level]').val()
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
    
    // Ordonnancement des champs par produits
    var pane = $('.tab-pane');
    var field_list_order = [];
    pane.each(function(){
        var arr_id = [];
        var field_list = $(this).find('.sortable-table').find('tbody').find('tr.form-field-row');
        if(field_list.length > 0)
        {
            field_list.each(function(){
                arr_id.push($(this).attr('data-field-id'));
            });
        }
        field_list_order.push(arr_id);
    });

    $('#form_structure_declaration_field-order').val(JSON.stringify(field_list_order));

    // console.log($('#form_structure_declaration_field-order').val());
    // console.log($('#form_structure_declaration_new-field-list').val());
    // console.log($('#form_structure_declaration_delete-field-action-list').val());

    // console.log($('#form_structure_declaration_current-field-list').val());
    $(this).parents().find('form').submit();
    // console.log($(this).parents().find('form').serialize());
});

var config_editor = $('input[name=config_editor]').val();
var admin_new_resultat = $('input[name=admin_new_resultat]').val();
var admin_new_field_resultat = $('input[name=admin_new_field_resultat]').val();
CKEDITOR.replace( 'editor', {
        language: 'fr',
        uiColor: '#9AB8F3',
        height: 150,
        width: 600,
        customConfig: config_editor,
    });
$('#product-tabs a:first').tab('show');

//suppression formulaire
$('#product-tabs').on('click', '.delete-form-level', function(e){
    e.preventDefault();
    var a = $(this).parent(),
        next = a.parent().find('a[data-toggle=tab]');
    var href = a.attr('href');
    var level = href.replace('#tab-form-',"");
    var route = $('input[name=admin_delete_resultat]').val();
    $.ajax({
        type: "POST",
        url: route.replace("PLACEHOLDER",level),
        success: function(html){
            a.remove();
            $(href).remove();
            next.click();
        }
    })
})

//nouveau formulaire
$('.btn-next-product').on('click',function(e){
    e.preventDefault();
    var nb = $(this).parent().find('a').length;
    if (nb < 6) {
        $.ajax({
            type: 'POST',
            url: admin_new_resultat,
            success : function(html) {
                $('.new-add').html(html);                        
                $('.new-add').find('.tab-pane').detach().appendTo('.tab-content');
                $('.new-add').find('a[data-toggle=tab]').detach().insertBefore($('.btn-next-product')).tab('show');
            }
        });
    } else {
        alert('nombre de produits maximum déjà atteint');
    }
    
});

// Ajout nouveau champ
var nb_added = 0;
$(document).on('click','.add-field-link-declaration', function(e){
    e.preventDefault();
    var custom_field_allowed = $(this).next("input[name=custom-field-allowed]").val();
    custom_field_allowed = parseInt(custom_field_allowed);

    if(custom_field_allowed >0) {
        var level = $(this).parents('.tab-pane').find("input[name=level]").val();
        var data = {'level':level};
        $.ajax({
            type: 'POST',
            data: data,
            url: admin_new_field_resultat,
            success: function(html){
                $('.modal-content .content').html(html);
                $("input[name=type_field]:checked").val()
                $('#btn-modal').click();
            }
        });
    }  
    else
    {
        alert(custom_field_allowed+' nouveau(x) champ(s) maximum');
    }
});

$(document).on('click','.edit-field-row-link',function(e){
    e.preventDefault();
    var form_field_row = $(this).parents('.form-field-row');
    var field_id = form_field_row.attr('data-field-id');
    var level = $(this).parents('.tab-pane').find("input[name=level]").val();
    var data = {'level':level, 'field_id': field_id};

    $.ajax({
            type: 'POST',
            data: data,
            url: admin_new_field_resultat,
            success: function(html){
                $('.modal-content .content').html(html);
                $("input[name=type_field]:checked").val()
                $('#btn-modal').click();
            }
        });
});

$(document).on('click', '.moveup-field-row-link', function(e) {
    e.preventDefault();
    var form_field_row = $(this).parents('.form-field-row');
    var prev = form_field_row.prev('tr');
    console.log(prev);
    if ( prev.hasClass('head-personalized') || prev.length == 0) {
        alert("ne peut plus monter");
    } else {
        form_field_row.detach().insertBefore(prev);
    }
});

$(document).on('click', '.movedown-field-row-link', function(e) {
    e.preventDefault();
    var form_field_row = $(this).parents('.form-field-row');
    var next = form_field_row.next('tr');  
    console.log(next);
    if ("undefined" == typeof next || next.length ==0) {
        alert('ne peut plus descendre')
    } else {
        form_field_row.detach().insertAfter(next);
    }
});

$('.modal-content .content').on('click', '.update.btn-valider', function(e){
    var level = $(this).prev("input[name=level]").val();
    var type = $("input[name=type_field]:checked").val();
    var label = $("input[name=intitule]").val();
    var field_id = $("input[name=field_id]").val();

    if(label.trim() == "") {
        alert("ajouter un intitulé");
    }
    else {
        var data = {'level':level,'label': label,'type_field': type,'field_id': field_id,'update': true};
        $.ajax({
            type: 'POST',
            data: data,
            url: admin_new_field_resultat,
            success: function(html){
                $('#sortable-table-'+level).append(html);
                form_field_row = $('#sortable-table-'+level).find('.form-field-row[data-field-id='+field_id+']');
                // console.log(form_field_row[0]); console.log(form_field_row[1]);
                var new_content = $(form_field_row[1]).html();
                // console.log(new_content);
                $(form_field_row[0]).html(new_content);
                form_field_row[1].remove();

                $('.close-modal').click();
            }
        });
    }
})

//valider ajout champ
$('.modal-content .content').on('click', '.ajouter.btn-valider', function(){
    var level = $(this).prev("input[name=level]").val();
    var type = $("input[name=type_field]:checked").val();
    var label = $("input[name=intitule]").val();

    if(label.trim() == "") {
        alert("ajouter un intitulé");
    }
    else {
        var data = {'level':level, 'label': label, 'type_field': type, 'validate': true};
        $.ajax({
            type: 'POST',
            data: data,
            url: admin_new_field_resultat,
            success: function(html){
                $('#sortable-table-'+level).find('tbody').append(html);
                $('#sortable-table-'+level).find('.head-personalized').css('display','');
                var custom_field_allowed = $('#sortable-table-'+level).parents('.tab-pane').find("input[name=custom-field-allowed]").val();
                // console.log(custom_field_allowed);
                custom_field_allowed = parseInt(custom_field_allowed) - 1;
                // console.log(custom_field_allowed);
                $('#sortable-table-'+level).parents('.tab-pane').find("input[name=custom-field-allowed]").val(custom_field_allowed);
                btn_add = $('#sortable-table-'+level).parents('.tab-pane').find('.add-field-link-declaration');
                if( custom_field_allowed > 0) {
                    btn_add.html("ajouter un champ ("+custom_field_allowed+" maximum)");
                    btn_add.parents('.add-field-form-block table').css('display','table');
                }
                else
                    btn_add.parents('.add-field-form-block table').css('display','none');
                    
                $('.close-modal').click(); 
            }
        });
    }
});
