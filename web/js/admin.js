// function sidebar_height() {
//     var main_height = $('main').height();
//     var container_display = $('#sidebar').parent().css('display');

//     if( container_display == "block") {
//         $('#sidebar').height(main_height);
//         $('#sidebar').css({'box-shadow':'5px -7px 5px -5px #aaa,5px 7px 5px -5px #aaa'});
//     } else {
//         $('#sidebar').css({'height':'auto','box-shadow':'5px -7px 5px -5px #aaa'});
//     }
// }
// window.onload = function () {
//     sidebar_height();    
// }
// $(window).resize(function () {
//     sidebar_height();
// });

$(document).ready(function(){
	$('input#header_data_header_message').css('padding-left', '10px!important;');
    //correction z-index 
    $(document).on('show.bs.modal', function () {
        $('main').parents('.container-fluid').css('z-index',"unset");
    });
    $(document).on('hidden.bs.modal', function () {
        var all_closed = true;
        $('.modal').each(function() {//cas multiple ouverture
            if ($(this).css('display') == 'block') {
                all_closed = false;
            }
        });
        if (all_closed) {
            $('main').parents('.container-fluid').css('z-index',"1");
        }
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
		$('div.block-active-hover').css('display', 'none');
		
        if(!($(this).hasClass('active'))) {
            $(this).addClass('active');
            $(this).find('.block-active-hover').css('display', 'block');
        }
		
        if($(this).hasClass('inactive')) {
            $(this).removeClass('inactive');
			//$(this).find('.block-active-hover').css('display', 'none');
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
	 
	 
	/**
     * *********************************************************************************************
     * DEBUT
     * Géstions des menu sedibar (hover)
     * *********************************************************************************************
     */
	//Hover : menu programme
	$("div#sidebar a#programme").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-programme').removeClass('img-programme-desactive');
				$('span#img-programme').addClass('img-programme-active');
				$('div#sidebar a#programme span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#programme span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#programme span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-programme').removeClass('img-programme-active');
				$('span#img-programme').addClass('img-programme-desactive');
				$('div#sidebar a#programme span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#programme span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#programme span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	
	//Hover : menu inscriptions
	$("div#sidebar a#inscriptions").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-inscriptions').removeClass('img-inscriptions-desactive');
				$('span#img-inscriptions').addClass('img-inscriptions-active');
				$('div#sidebar a#inscriptions span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#inscriptions span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#inscriptions span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-inscriptions').removeClass('img-inscriptions-active');
				$('span#img-inscriptions').addClass('img-inscriptions-desactive');
				$('div#sidebar a#inscriptions span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#inscriptions span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#inscriptions span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	
	//Hover : menu resultats
	$("div#sidebar a#resultats").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-resultats').removeClass('img-resultats-desactive');
				$('span#img-resultats').addClass('img-resultats-active');
				$('div#sidebar a#resultats span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#resultats span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#resultats span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-resultats').removeClass('img-resultats-active');
				$('span#img-resultats').addClass('img-resultats-desactive');
				$('div#sidebar a#resultats span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#resultats span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#resultats span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	
	//Hover : menu points
	$("div#sidebar a#points").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-points').removeClass('img-points-desactive');
				$('span#img-points').addClass('img-points-active');
				$('div#sidebar a#points span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#points span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#points span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-points').removeClass('img-points-active');
				$('span#img-points').addClass('img-points-desactive');
				$('div#sidebar a#points span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#points span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#points span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	
	//Hover : menu design
	$("div#sidebar a#design").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-design').removeClass('img-design-desactive');
				$('span#img-design').addClass('img-design-active');
				$('div#sidebar a#design span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#design span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#design span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-design').removeClass('img-design-active');
				$('span#img-design').addClass('img-design-desactive');
				$('div#sidebar a#design span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#design span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#design span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	
	//Hover : menu contenus
	$("div#sidebar a#contenus").on({
		mouseenter: function () {
			if(!$(this).hasClass("active")){
				$('span#img-contenus').removeClass('img-contenus-desactive');
				$('span#img-contenus').addClass('img-contenus-active');
				$('div#sidebar a#contenus span.border-left-hover-active').css('display', 'block');
				$('div#sidebar a#contenus span.text-menu').css('color', 'var(--couleur_2)');
				$('div#sidebar a#contenus span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
			}
		},
		mouseleave: function () {
			if(!$(this).hasClass("active")){
				$('span#img-contenus').removeClass('img-contenus-active');
				$('span#img-contenus').addClass('img-contenus-desactive');
				$('div#sidebar a#contenus span.border-left-hover-active').css('display', 'none');
				$('div#sidebar a#contenus span.text-menu').css('color', 'var(--couleur_5)');
				$('div#sidebar a#contenus span.numero-menu').css({'color':'var(--couleur_5)', 'border':'1px solid var(--couleur_5)'});
			}
		}
	});
	/**
     * *********************************************************************************************
     * FIN
     * Géstions des menu sedibar (hover)
     * *********************************************************************************************
     */
	 
	 
	/**
     * *********************************************************************************************
     * DEBUT
     * Géstions des menu sedibar (::active)
     * *********************************************************************************************
     */
	if($("div#sidebar a#programme").hasClass("active")){
		$('span#img-programme').removeClass('img-programme-desactive');
		$('span#img-programme').addClass('img-programme-active');
		$('div#sidebar a#programme span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#programme span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#programme span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}else if($("div#sidebar a#inscriptions").hasClass("active")){
		$('span#img-inscriptions').removeClass('img-inscriptions-desactive');
		$('span#img-inscriptions').addClass('img-inscriptions-active');
		$('div#sidebar a#inscriptions span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#inscriptions span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#inscriptions span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}else if($("div#sidebar a#resultats").hasClass("active")){
		$('span#img-resultats').removeClass('img-resultats-desactive');
		$('span#img-resultats').addClass('img-resultats-active');
		$('div#sidebar a#resultats span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#resultats span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#resultats span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}else if($("div#sidebar a#points").hasClass("active")){
		$('span#img-points').removeClass('img-points-desactive');
		$('span#img-points').addClass('img-points-active');
		$('div#sidebar a#points span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#points span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#points span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}else if($("div#sidebar a#design").hasClass("active")){
		$('span#img-design').removeClass('img-design-desactive');
		$('span#img-design').addClass('img-design-active');
		$('div#sidebar a#design span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#design span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#design span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}else if($("div#sidebar a#contenus").hasClass("active")){
		$('span#img-contenus').removeClass('img-contenus-desactive');
		$('span#img-contenus').addClass('img-contenus-active');
		$('div#sidebar a#contenus span.border-left-hover-active').css('display', 'block');
		$('div#sidebar a#contenus span.text-menu').css('color', 'var(--couleur_2)');
		$('div#sidebar a#contenus span.numero-menu').css({'color':'var(--couleur_2)', 'border':'1px solid var(--couleur_2)'});
	}

    /**
     * *********************************************************************************************
     * Bouton/Lien suppression contenu de champ de texte
     * *********************************************************************************************
     */
    $(document).on('input', '.removable-content-input', function(e){
        e.preventDefault();
        if($(this).hasClass('fixed-size')){
            if('' != $(this).val().trim()){
                $(this).next('.delete-input').show();
                $(this).next('.delete-input').css({
					'float': 'none',
					'position': 'static'
				});
			} else {
                $(this).next('.delete-input').hide();
			}
		} else if ($(this).hasClass('large-input-text')) {
            if('' != $(this).val().trim()){
                $(this).next('.delete-input').show();
                $(this).addClass('quite-large-input-text');
            } else {
                $(this).next('.delete-input').hide();
                $(this).removeClass('quite-large-input-text');
            }
		} else {
            if('' != $(this).val().trim()){
                $(this).next('.delete-input').show();
            } else {
                $(this).next('.delete-input').hide();
            }
		}
    });

    $(document).on('click', '.delete-input', function(e){
        e.preventDefault();
        $(this).prev('input[type=text]').val('');
        $(this).prev('input[type=text]').removeClass('quite-large-input-text');
        if($(this).parents('.delete-input-common-container').find('.message-preview').length > 0){
            $(this).parents('.delete-input-common-container').find('.message-preview').text('');
		}
        $(this).hide();
    });
    /**
     * *********************************************************************************************
     * FIN
	 * Bouton/Lien suppression contenu de champ de texte
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Bullde d'aide
     * *********************************************************************************************
     */
	$('.bulle-aide').on('click', function(e){
		e.preventDefault();
		$('#'+$(this).attr('data-trigger-id')).trigger('click');
	});
    /**
     * *********************************************************************************************
     * FIN
	 * Bullde d'aide
     * *********************************************************************************************
     */
	 
	 $(document).on('click', 'span#fermerPopUp', function(){
		$('#conteneur-popup').hide();
		$('#body-popup').hide();
	});
});