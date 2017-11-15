$(document).ready(function(){
	//Gestions choix récompenses
	$(document).on('click', '.radioBtn', function(){
		$('.radioBtn').each(function(i){
			$(this).addClass('unchecked');
			$(this).removeClass('checked');
			
			$(this).parent().parent().removeClass("active");
			$(this).parent().parent().addClass("inactive");
		});
		
		$(this).addClass('checked');
		
		$(this).parent().parent().removeClass("inactive");
		$(this).parent().parent().addClass("active");
	});
	
	//Gestions des choix mode multi-opérations
	$(document).on('click', '.checkboxBtn', function(){
		/*
		$('.checkboxBtn').each(function(i){
			$(this).addClass('no-mode');
			$(this).removeClass('est-mode');
		});
		*/
		
		if($(this).hasClass('est-mode')){
			$(this).addClass('no-mode');
			$(this).removeClass('est-mode');
		}else{
			$(this).addClass('est-mode');
			$(this).removeClass('no-mode');
		}
	});

    $('.program-choose .fieldset').on('mouseenter',function(){
        $('.fieldset.active').removeClass('active').addClass('inactive');
        if(!($(this).hasClass('active'))) {
            $(this).addClass('active');
        }
        if($(this).hasClass('inactive')) {
            $(this).removeClass('inactive');
        }
    });

	//bouton multi-opération pour les programmes
	$('.checkboxBtn.program').on('click', function(){
		if ($(this).hasClass('est-mode')) {
			$(this).find('input').val(0);
		} else {
			$(this).find('input').val(1);
		}
	});

	$('.radioBtn.program').on('click',function(){
		$('.fieldset .checkboxBtn.program').each(function(){
			if($(this).hasClass('est-mode'))
			{
				$(this).removeClass('est-mode').addClass('no-mode');
				$(this).find('input').val(0);
			}
		})
	})

	/**
     * *********************************************************************************************
     * Paramétrages - Inscriptions
     * Selection multiple : champs à publier, rendre obligatoire
     * *********************************************************************************************
     */
    $(document).on('click', '.checkbox-publish-all', function(e){        
        if($(this).is(':checked'))
        {
            $(this).parents('table').find('.form-field-published').prop("checked", true);
        }
        else
        {
            $(this).parents('table').find('.form-field-published').prop("checked", false);
        }
    });
    $(document).on('click', '.checkbox-mandatory-all', function(e){
        if($(this).is(':checked'))
        {
            $(this).parents('table').find('.form-field-mandatory').prop("checked", true);
        }
        else
        {
            $(this).parents('table').find('.form-field-mandatory').prop("checked", false);
        }
    });
    $(document).on('click','.form-field-published', function(e){    
        if(!$(this).is(':checked'))
        {
            $(this).parents('table').find('.checkbox-publish-all').prop("checked", false);
        }
        else
        {
            var all_checked = true;
            $(this).parents('table').find('.form-field-published').each(function(){
                if(!$(this).is(':checked'))
                    all_checked = false;               
            });
            if(all_checked)
                $(this).parents('table').find('.checkbox-publish-all').prop("checked", true);
        }
    });
    $(document).on('click','.form-field-mandatory', function(e){    
        if(!$(this).is(':checked'))
        {
            $(this).parents('table').find('.checkbox-mandatory-all').prop("checked", false);
        }
        else
        {
            var all_checked = true;
            $(this).parents('table').find('.form-field-mandatory').each(function(){
                if(!$(this).is(':checked'))
                    all_checked = false;               
            });
            if(all_checked)
                $(this).parents('table').find('.checkbox-mandatory-all').prop("checked", true);
        }
    });

    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Selection multiple : champs à publier, rendre obligatoire
     * *********************************************************************************************
     */
    //declartion import
    $('.btn-valider.btn-download').on('click',  function(){
        $('form[name=result_setting]').submit();
        // var url = $('input[name=url]').val();
        // var data = $('form[name=result_setting]').serialize();

        // $.ajax({
        //     type : 'POST',
        //     data : data,
        //     url : url,
        //     success : function(html){
        //         console.log(html);
        //     }
        // });
    });
    $('.btn-valider.btn-upload').on('click',  function(){
        $("#result_setting_upload_uploaded_file").click();
    });
    $("#result_setting_upload_uploaded_file").on('change',function() {
        $(this).parents('form').submit();
    });
});

