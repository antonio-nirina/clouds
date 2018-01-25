$(document).ready(function() {
	$('#preview-template-dialog').modal('hide');
    function sendFilter() {
        var a=$("#loading-image").clone();
        $('.row.list').html(a);
        var sort_filter = $('.dropdown.filtres').find('button').hasClass('active'),
            data = {};        
        
        if (sort_filter) {
            data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
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

    function getChecked() {
        var checked = [];
        $(".campagne-name .styled-checkbox").each(function() {
            if ($(this).is(':checked')) {
                checked.push($(this).attr('id'));
            }
        });
        return checked;
    }

    function showPrevious() {
        if ($('#create-tabs a:first-child').hasClass('active')) {
            $('#new-campaign-modal .previous').css('display','none');
        } else {
            $('#new-campaign-modal .previous').css('display','initial');
        }
    }

    function initCalendar() {
        $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);//langue datepicker
        $('#calendar').datepicker({
            minDate: new Date(),
            altField: ".date_launch_campaign",
            altFormat: "d MM yy"
        });
    }
    
    function initSelectChosen() {
        $(".chosen-select").chosen({//hour selectable
            disable_search: true,
            width: "70px"
        });
    }

    function addDone() {
        if (! $('#create-tabs a.activated.active').hasClass("done")) {
            $('#create-tabs a.activated.active').addClass("done");
        }
    }

    function removeDone() {
        if ($('#create-tabs a.activated.active').hasClass("done")) {
            $('#create-tabs a.activated.active').removeClass("done");
        }
    }

    function isDone() {
        if ($('#create-tabs a.activated.active').hasClass("step-1")) {
            if ($(".campaign_name_input").val() && $(".campaign_object_input").val()) {
                addDone();
                return true;
            } else {
                removeDone();
                alert("Veuillez completer les deux champs pour aller à l'étape suivante...");
                return false;
            }
        }
        if ($('#create-tabs a.activated.active').hasClass("step-2")) {
            if($('#dropdownMenuListe').html().trim() == $('#dropdownMenuListe').attr("data-default")) {
                removeDone();
                alert("Veuillez sélectionner une liste ou créez-en une...");
                return false;
            } else {
                addDone();
                return true;
            }
        } 
        if ($('#create-tabs a.activated.active').hasClass("step-3")) {
            console.log($('input[name=template_choice_option]:checked').val());
            if ($('input[name=template_choice_option]:checked').val() || "undefined" != typeof $('input[name=template_choice_option]:checked').val() ) {
                addDone();
                return true;
            } else {
                removeDone();
                alert("Veuillez sélectionner un template ou créez-en un...");
                return false;
            }
        }
    }

    function clickNext() {
        var next = $('#create-tabs a.activated.active').next('a');
        if (!next.hasClass("activated")) {
            next.addClass("activated");
        }     
        if (next.hasClass("step-4")) {
            next.addClass("done");
        }       
        next.click();
        showPrevious();
    }

    $(document).on('click','.dropdown .delete-input', function(){//annuler filtre
        $(this).off('click');
        $(this).parent().find('button').html($(this).parent().find('button').removeClass('active').attr('data-default'));
        $(this).css({'visibility':'hidden','display':'inline-block'});
        setTimeout(sendFilter(), 0);
    });

    $(document).on('click','.clearable .dropdown-item', function(e){//activer filtre
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

    $(document).on('mouseleave', '.dropdown-menu', function() {// sortir des dropdown
        $(document).click();
    });

    $(document).on('keyup', '.form-line input', function() {
        if ($(this).val()) {
            $(this).next('span').css('display', 'inline-block');
        } else {
            $(this).next('span').css('display', 'none');            
        }
    })

    $('#new-campaign-modal').on('shown.bs.modal', function() {//affichage de l'onglet création campaigne
        var url = $("input[name=new_campaign_url").val();
        $.ajax({
            type: "GET",
            url: url,
            success: function (html) {
                $('#new-campaign-modal').find(".modal-content .content").html(html);
                $('#create-tabs a.activated:last').tab('show');
                showPrevious();
                initCalendar();
                initSelectChosen();
            }
        })               
    });

    $(document).on('click', "#new-campaign-modal .previous", function() {//affichage de l'onglet precedent
        if ($(this).hasClass('keep') && $('#create-tabs a.activated.active').hasClass("step-3")) {
            $(this).removeClass('keep');
            $('#new-campaign-modal').find('.modal-step-3.first').css('display','block');
            $('#new-campaign-modal').find('.modal-step-3.second').css('display','none');
        } else {
            if ($('#create-tabs a.activated.active').hasClass("done")) {
                $('#create-tabs a.activated.active').removeClass("done");
            }
            $('#create-tabs a.activated.active').removeClass("activated").prev('a').click();
        }
        showPrevious();
    });

    $(document).on("change", "input[name=program-campaign]", function() {//boutton de programmation
        if ($(this).val() =="now") {
            $(".btn-end-step-4").css("display", "initial");
            $(".btn-program-step-4").css("display", "none");
            $(".select-date").css("display", "none");
        } else {
            $(".btn-end-step-4").css("display", "none");
            $(".btn-program-step-4").css("display", "initial");
            $(".select-date").css("display", "block");
        }
    });

    $(document).on('click', '.btn-end-step', function() { // terminer une étape pour aller à l'autre
        if (isDone()) {
            clickNext();
        }        
    });     

    function installWysiwyg()
    {
        var ckeditor_config_light_path = $('input[name=ckeditor_config_light_path]').val();
        var text_area_list = $('textarea.large-textarea');
        text_area_list.each(function(){
            CKEDITOR.replace( $(this).attr('id'), {
                language: 'fr',
                uiColor: '#9AB8F3',
                height: 150,
                width: 600,
                customConfig: ckeditor_config_light_path
            });
        });
    }

    function installColorPicker()
    {
        if ($('.color-value').length >0 ) {
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
                    },
                    theme: 'bootstrap'
                });
            });
        }
    }

    $(document).on('click', '.add.free-add.add-list', function(e){//création nouveau template
        e.preventDefault();
        var template_model = "text-and-image";
        // var template_model = "TEXT_AND_IMAGE";

        if(null !== template_model){
            var add_template_url = $('input[name=add_template_form_url]').val();
            add_template_url = add_template_url+'/'+template_model;

            $.ajax({
                type: 'GET',
                url: add_template_url,
                success: function(data){
                    $('#new-campaign-modal').find('.modal-step-3.first').css('display','none');
                    $('#new-campaign-modal').find('.modal-step-3.second').css('display','block').html(data.content);
                    $('#new-campaign-modal .previous').addClass('keep');
                    var text = "Créer votre email, il sera sauvegardé dans l'onglet \"modèle d'e-mail\", vous pourrez le réutiliser et/ou modifier ultérieurement.";
                    $('#new-campaign-modal').find('.modal-step-3 .dialog-title').html(text);
                    $('#new-campaign-modal').find('.modal-step-3 .dialog-title').removeClass('dialog-title');
                    $(".btn-valider.modify").addClass("hidden");
                    $(".btn-valider.modify").addClass("hidden");
                    $(".btn-valider.validate.validate-add").addClass("hidden");
                    $(".btn-end-step-3").addClass("hidden");
                    installColorPicker();
                    installWysiwyg();
                },
                statusCode: {
                    404: function(data){
                        $('#new-campaign-modal').find('.modal-step-3.first > .error-message-container').text(data.responseJSON.message);                        
                    }
                }
            });
        }
    });

    $(document).on("click", ".btn-end-step.btn-end-step-4", function() {//envoie campagne
        $("#new-campaign-modal").modal("hide");
        $("span.date-envoi").html($('.date_launch_campaign').val());
        setTimeout(function(){
            $("#done-campaign-modal").modal("show");
        },0);
    });   
    $(document).on("click", ".btn-end-step.btn-program-step-4", function() {//envoie campagne programmée
        $("#new-campaign-modal").modal("hide");
        $("span.date-envoi").html($('.date_launch_campaign').val());        
        setTimeout(function(){
            $("#done-campaign-modal").modal("show");
        },0);
    });

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * modal création template - enregistrement et validation
     * *********************************************************************************************
     */
    $(document).on('click', '.modal-step-3 .btn-valider.save', function(e){
        e.preventDefault();
        $('.options-wrapper').addClass('active');
        $('.modal-step-3').find('.btn-valider.modify').removeClass('hidden');
        $('.modal-step-3').find('.btn-valider.validate').removeClass('hidden');
        $('.modal-step-3').find('.template-name-container').removeClass('hidden');
        $(this).addClass('hidden');
    });

    $(document).on('click', '.modal-step-3 .btn-valider.modify', function(e){
        e.preventDefault();
        $('.options-wrapper').removeClass('active');
        $('.modal-step-3').find('.btn-valider.validate').addClass('hidden');
        $('.modal-step-3').find('.template-name-container').addClass('hidden');
        $('.modal-step-3').find('.btn-valider.save').removeClass('hidden');
        $(this).addClass('hidden');
    });


    //validation de template
    $(document).on('click', '.modal-step-3 button.btn-valider.validate.validate-add', function(e){
        var add_template_url = $('input[name=add_template_form_url]').val();
        e.preventDefault();
        $('.modal-step-3').find('.block-model-container').remove();

        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }

        $('.modal-step-3 form').ajaxSubmit({
            type: 'POST',
            url: add_template_url,
            success: function(data){
               if(data['error']){                    
                   $('#new-campaign-modal').find('.modal-step-3.second').html(data.content);
                   $('#new-campaign-modal').find('.modal-step-3.second').find('.btn-valider.save').trigger('click');
                   var text = "Créer votre email, il sera sauvegardé dans l'onglet \"modèle d'e-mail\", vous pourrez le réutiliser et/ou modifier ultérieurement.";
                   $('#new-campaign-modal').find('.modal-step-3 .dialog-title').html(text);
                   $('#new-campaign-modal').find('.modal-step-3 .dialog-title').removeClass('dialog-title');
                   installColorPicker();
                   installWysiwyg();
               } else {
                    //set new template selected
                    console.log(data);
                    $('.step-3').addClass('done');
                    clickNext();//next step
               }
            },
            statusCode: {
                404: function(data){
                    $('#new-campaign-modal').find('.modal-step-3').find('.error-message-container.general-message').text('Erreur');                    
                }
            }
        });
    });

    //upload images
    $(document).on('click', '.btn-upload.choose-upload-img-button', function(e){
        e.preventDefault();
        $(this).parent().find('.image-input').trigger('click');
    });

    $(document).on('click', '.upload-img-button-container', function(e){
        e.preventDefault();
        $(this).parent().find('.image-input').trigger('click');
    });

    $(document).on('change', '.image-input', function(){
        if('' == $(this).val().trim()){
            var initial_image_name = $(this).parent().find('input.initial-image').val();
            if('' == initial_image_name.trim()){
                $(this).parent().find('.upload-img-button').addClass('hidden-button');
                $(this).parent().find('.upload-img-button-container').addClass('hidden-button');
                $(this).parent().find('.btn-upload.choose-upload-img-button').removeClass('hidden-button');
            } else {
                $(this).parent().find('.img-name-container').text(initial_image_name);
            }
        } else {
            $(this).parent().find('.upload-img-button').css('background-position', '15px');
            $(this).parent().find('.upload-img-button').removeClass('hidden-button');
            $(this).parent().find('.upload-img-button-container').removeClass('hidden-button');
            $(this).parent().find('.btn-upload.choose-upload-img-button').addClass('hidden-button');
            var image_file_name = $(this).val().split('\\').pop();
            $(this).parent().find('.upload-img-button').find('.img-name-container').text(image_file_name);
            $(this).parent().find('.delete-link.delete-image').show();
        }
    });

    //reset upload images
    function resetUploadButton(current_delete_link)
    {
        var wrapper = current_delete_link.parent().find('input[type=file]').wrap('<form></form>').parent();
        wrapper.trigger('reset');
        current_delete_link.parent().find('input[type=file]').unwrap();
        current_delete_link.parent().find('.upload-img-button').addClass('hidden-button');
        current_delete_link.parent().find('.upload-img-button-container').addClass('hidden-button');
        current_delete_link.parent().find('.choose-upload-img-button').removeClass('hidden-button');
        current_delete_link.hide();
    }

    $(document).on('click', '.delete-logo-image', function(e){
        e.preventDefault();
        var current_delete_link = $(this);
        resetUploadButton(current_delete_link);
        $('#create-template-dialog').find('input.delete-logo-image-command-hidden').val(true);
    });

    /**
     * *********************************************************************************************
     * Paramétrages - Communication - Emailing - Templates
     * ajout d'autres contenus
     * *********************************************************************************************
     */
    function addContentConfigBock(new_content_index, content_type)
    {
        var new_content_type = $('.block-model-container').find('.template-content-config-model').clone();
        new_content_type.removeClass('template-content-config-model');
        var html_new_content_type = new_content_type.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_content_type = html_new_content_type.replace(/__name__/g, new_content_index);
        new_content_type = $($.parseHTML(html_new_content_type));
        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_content_type);
        new_content_type.show();
        new_content_type.find('input.content-type').val(content_type);
        new_content_type.find('input.content-order').val(new_content_index + 1); // 1-based index
    }

    $(document).on('click', '.add-other-content-container .add-image-link', function(e){
        e.preventDefault();
        var available_content_num = $('.template-config-block-container')
            .find('.template-config-block.template-content-block').length;
        var new_content_index = available_content_num;
        var new_image_content = $('.block-model-container').find('.template-content-image-model').clone();
        new_image_content.removeClass('template-content-image-model');
        var html_new_image_content = new_image_content.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_image_content = html_new_image_content.replace(/__name__/g, new_content_index);
        new_image_content = $($.parseHTML(html_new_image_content));

        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_image_content);
        new_image_content.show();

        addContentConfigBock(new_content_index, $('input[name=template_content_type_image]').val());
    });

    $(document).on('click', '.add-other-content-container .add-text-link', function(e){
        e.preventDefault();
        var available_content_num = $('.template-config-block-container')
            .find('.template-config-block.template-content-block').length;
        var new_content_index = available_content_num;
        var new_text_content = $('.block-model-container').find('.template-content-text-model').clone();
        new_text_content.removeClass('template-content-text-model');
        var html_new_text_content = new_text_content.wrap('<div class="model-wrapper"></div>').parent().html();
        html_new_text_content = html_new_text_content.replace(/__name__/g, new_content_index);
        new_text_content = $($.parseHTML(html_new_text_content));

        var add_content_link_block = $(".add-other-content-container");
        add_content_link_block.before(new_text_content);
        new_text_content.find('textarea').addClass('large-textarea');
        var ckeditor_config_light_path = $('input[name=ckeditor_config_light_path]').val();
        CKEDITOR.replace(new_text_content.find('textarea').attr('id'), {
            language: 'fr',
            uiColor: '#9AB8F3',
            height: 150,
            width: 600,
            customConfig: ckeditor_config_light_path
        });
        new_text_content.show();

        addContentConfigBock(new_content_index, $('input[name=template_content_type_text]').val());
    });

    //liste des archivées
    $(".add-to-archive").on("click", function (e) {
        e.preventDefault();
        url = $(this).attr("href");
        $.ajax({
            type : "POST",
            url: url,
            success: function(html) {
                $('.row.list').html(html);
            }
        });
    });
	
	
	$(document).on('click', 'a#apercu_campagne', function(){
		var urlTpl = $(this).attr('data-url');
		var UrlApercuCampagne = $('input#preview_template_campagne').val();
		$('.chargementAjax').removeClass('hidden');
		/*
		$('#preview-template-dialog').find('.modal-body-container').html(' <iframe src="'+urlTpl+'"></iframe>');
		$('#preview-template-dialog').find('.modal-body-container').html(text);
		$('#preview-template-dialog').modal('show');
		$('.chargementAjax').addClass('hidden');
		*/
		
		$.ajax({
			type : "POST",
			url: UrlApercuCampagne,
			data : 'urlTpl='+urlTpl+'',
			dataType : 'html',
			success: function(text){
				$('#preview-template-dialog').find('.modal-body-container').html(text);
				$('#preview-template-dialog').modal('show');
				$('.chargementAjax').addClass('hidden');
			}
		});
		
		return false;
	});
});

function AfficheApercuCampagne(apercu){
}
