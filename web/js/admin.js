function sidebar_height() {
    var main_height = $(document).height() - $('.menu-niv-1').height() - $('.menu-niv-2').height();
    var container_display = $('#sidebar').parent().css('display');

    if( container_display == "block") {
        $('#sidebar').height(main_height);
        $('#sidebar').css({'box-shadow':'5px -7px 5px -5px #aaa,5px 7px 5px -5px #aaa'})
    } else {
        $('#sidebar').css({'height':'auto','box-shadow':'5px -7px 5px -5px #aaa'});
    }
}
window.onload = function () {
    sidebar_height();    
}
$(window).resize(function () {
    sidebar_height();
});
$(document).ready(function(){
    //sidebar height adjustment 
    setTimeout(function() {        
        sidebar_height(); 
    }, 500); 

    //correction z-index 
    $(document).on('show.bs.modal', function () {
        $('main').parents('.container-fluid').css('z-index',"unset");
    });
    $(document).on('hidden.bs.modal', function () {
        $('main').parents('.container-fluid').css('z-index',"1");
    });

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
            $(this).parents('table').find('.form-field-mandatory').each(function(){
                var label = $(this).parents('td').next('td').find('label').html(); 
                if (label.indexOf("*") < 0) {
                    $(this).parents('td').next('td').find('label:first').html(label+" *");
                }
            });
        }
        else
        {
            $(this).parents('table').find('.form-field-mandatory').prop("checked", false);
            $(this).parents('table').find('.form-field-mandatory').each(function(){
                var label = $(this).parents('td').next('td').find('label').html(); 
                if (label.indexOf("*") > 0) {
                    $(this).parents('td').next('td').find('label:first').html(label.replace(" *",""));
                }
            });
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
        var label = $(this).parents('td').next('td').find('label').html();
        // console.log($(this).parents('td').next('td').find('label')[0]);
        console.log(label);
        if(!$(this).is(':checked'))
        {
            $(this).parents('table').find('.checkbox-mandatory-all').prop("checked", false);
            if (label.indexOf("*") > 0) {
                $(this).parents('td').next('td').find('label:first').html(label.replace(" *",""));
            }
        }
        else
        {
            var all_checked = true;
            if (label.indexOf("*") < 0) {
                $(this).parents('td').next('td').find('label:first').html(label+" *");
            }
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
});

