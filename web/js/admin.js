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
			console.log($(this));
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
    $('.checkbox-publish-all').on('click', function(e){
        if($(this).is(':checked'))
        {
            $('.form-field-published').prop("checked", true);
        }
        else
        {
            $('.form-field-published').prop("checked", false);
        }
    });

    $('.checkbox-mandatory-all').on('click', function(e){
        if($(this).is(':checked'))
        {
            $('.form-field-mandatory').prop("checked", true);
        }
        else
        {
            $('.form-field-mandatory').prop("checked", false);
        }
    });
    /**
     * *********************************************************************************************
     * FIN
     * Paramétrages - Inscriptions
     * Selection multiple : champs à publier, rendre obligatoire
     * *********************************************************************************************
     */
});

