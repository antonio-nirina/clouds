$(document).ready(function() {

    function sendFilter() {
        $('.row.list').html('');
        var folder_filter = $('.dropdown.dossiers').find('button').hasClass('active'),
            sort_filter = $('.dropdown.filtres').find('button').hasClass('active'),
            data = {};        
        
        if (sort_filter) {
            data.sort_field = $('.dropdown.filtres').find('button').find("span").html().trim();
        }

        if (folder_filter) {
            data.folder_id = $('.dropdown.dossiers').find('button').find("span.folder_id").html().trim();
        }

        var url = $('input[name=filtered]').val();
        $.ajax({
            type: "POST",
            url : url,
            data: data,
            success: function(html) {
                $('.row.list').html(html);
            }
        });             
    }

    $(document).on('click','.dropdown .delete-input', function(){//annuler filtre
        $(this).off('click');
        $(this).parent().find('button').html($(this).parent().find('button').removeClass('active').attr('data-default'));
        $(this).css({'visibility':'hidden','display':'inline-block'});
        setTimeout(sendFilter(), 0);
    });

    $(document).on('click','.filter .dropdown-item', function(e){//activer filtre
        e.preventDefault();
        $(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        $(this).parents('.dropdown').find('.delete-input').css({'visibility':'visible','display':'inline-block'});
        setTimeout(sendFilter(), 0);
    });

    $('.btn-valider.btn-new-campaign-folder').on('click', function(e) {//validation nouveau dossier
        e.preventDefault();
        var url = $('#new_folder_link').val();
        var data = {'name' :$('#campaign_new_folder').val()};
        $.ajax({
            type: "POST",
            url : url,
            data: data,
            dataType: "json",
            success: function(json){
                if (json.error) {
                    $('.add-campaign-folder-error').html(json.error);
                } else if (json.response) {
                    if(json.response.id) {
                        $("#new-folder-modal-campaign").modal('hide');
                        $(".dropdown.dossiers").find(".dropdown-menu").append(
                            '<a class="dropdown-item" href="#">'+json.response.name+'<span class="folder_count">'+json.response.count+'</span><span class="folder_id">'+json.response.id+'</span></a>'
                        );
                    }
                }
            }
        });
    });

    $(document).on('click', '.campaign-replicate', function(e) {// dupliquer compaigne
        e.preventDefault();
        var url = $('input[name=replicate]').val();
        var data = {'id': $(this).parent().find('input[name=campaign-id]').val()};
        $.ajax({
            type: "POST",
            url : url,
            data: data,
            dataType: "json",
            success: function() {
                console.log('success');
            }
        });
        setTimeout(sendFilter(), 250);
    });

    $(document).on('click', '.campaign-rename', function(e) {// renommer compaigne
        e.preventDefault();
        var current_name = $(this).parents(".list .row").find(".campagne-name-name").html().trim();

        $("#btn-modal-rename").click();  
        $("#btn-modal-rename").next('.modal').find('input[name=campaign-id]').val($(this).parent().find('input[name=campaign-id]').val());
        $("#btn-modal-rename").next('.modal').find('input[name=campaign_new_name]').val(current_name);
    });

    $('.btn-rename-campaign').on('click', function(e) {
        var name = $('#campaign_new_name').val().trim();
        var id = $(this).parents('.modal').find('input[name=campaign-id]').val();
        var current_name = $('.campaign-'+id).find(".campagne-name-name").html().trim();
        if (name != current_name) {
            var url = $('input[name=rename]').val();
            var data = {'id': id, 'name': name};
            $.ajax({
                type: "POST",
                url : url,
                data: data,
                dataType: "json",
                success: function(json) { 
                    if (json.id) {
                        $('.campaign-'+id).find(".campagne-name-name").html(name);                        
                    }                    
                }
            });
            setTimeout(sendFilter(), 250);
            $(".modal").modal('hide');
        }        
    })

    function getChecked() {
        var checked = [];
        $(".campagne-name .styled-checkbox").each(function() {
            if ($(this).is(':checked')) {
                checked.push($(this).attr('id'));
            }
        });
        return checked;
    }

    $(document).on('change', ".campagne-name .styled-checkbox", function() {//selection des campagnes
        checked = getChecked();        
        text = (checked.length == 1)?checked.length+" campagne sélectionnée":((checked.length > 1)?checked.length+" campagnes sélectionnées":"");
        $(".selected-count input").val(text);

        if (checked.length > 0) {
            $('.filter.selected-campaign').css('display',"flex");
            $('.selected-count .delete-input').css('display','block');
        } else {
            $('.filter.selected-campaign').css('display',"none");
        }
    });

    $('.btn-valider.btn-delete-campaign').on("click", function() { //suppression campagenes
        checked = getChecked();
        var data = {'ids' : checked.join(',')};
        var url = $("input[name=delete-campaign-link]").val();
        $.ajax({
            type: "POST",
            data: data,
            url: url,
            success: function() {
            }
        });
        setTimeout(sendFilter(), 250);
        $(".modal").modal('hide');
    });

    $('.selected-count .delete-input').on("click", function () {//désélection des campaignes
        var checked = getChecked();
        for (i in checked) {
            $("#"+checked[i]).click();
        }
        $('.filter.selected-campaign').css('display',"none");
    });

});
