$(document).ready(function(){
	//Block mon compte
	$("div.content-mon-compte").on({
		mouseenter: function () {
			$('p#block-active-hover-mon-compte').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-mon-compte').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block classement
	$("div.content-classement").on({
		mouseenter: function () {
			$('p#block-active-hover-classement').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-classement').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block contact
	$("div.content-contact").on({
		mouseenter: function () {
			$('p#block-active-hover-contact').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-contact').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block challenge en cours
	$("div.content-challenge-en-cours").on({
		mouseenter: function () {
			$('p#block-active-hover-challenge-en-cours').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-challenge-en-cours').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block flux cadeaux
	$("div.content-flux-cadeaux").on({
		mouseenter: function () {
			$('p#block-active-hover-flux-cadeaux').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-flux-cadeaux').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block réseau sociaux
	$("div.content-rs-sociaux").on({
		mouseenter: function () {
			$('p#block-active-hover-rs-sociaux').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-rs-sociaux').hide();
			$(this).css('background', '#f5f5f5');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block édito
	$("div.content-edito").on({
		mouseenter: function () {
			$('p#block-active-hover-edito').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-edito').hide();
			//$(this).css('background', '#f5f5f5');
			$(this).css('background', 'white');
			$(this).css('box-shadow', 'none');
		}
	});

    $("div.content-edito").on({
        mouseenter: function () {
            $(this).find('p.block-active-hover').show();
            $(this).css('background', 'white');
            $(this).css('box-shadow', '1px 1px 2px grey');
        },
        mouseleave: function () {
            $(this).find('p.block-active-hover').hide();
            //$(this).css('background', '#f5f5f5');
            $(this).css('background', 'white');
            $(this).css('box-shadow', 'none');
        }
    });
	
	//Block date challenge
	$("div.content-date-challenge").on({
		mouseenter: function () {
			$('p#block-active-hover-date-challenge').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-date-challenge').hide();
			//$(this).css('background', '#f5f5f5');
			$(this).css('background', 'white');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block trophées
	$("div.content-trophees").on({
		mouseenter: function () {
			$('p#block-active-hover-trophees').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-trophees').hide();
			//$(this).css('background', '#f5f5f5');
			$(this).css('background', 'white');
			$(this).css('box-shadow', 'none');
		}
	});
	
	//Block trophées
	$("div.content-nouveaux-challenge").on({
		mouseenter: function () {
			$('p#block-active-hover-nouveaux-challenge').show();
			$(this).css('background', 'white');
			$(this).css('box-shadow', '1px 1px 2px grey');
		},
		mouseleave: function () {
			$('p#block-active-hover-nouveaux-challenge').hide();
			//$(this).css('background', '#f5f5f5');
			$(this).css('background', 'white');
			$(this).css('box-shadow', 'none');
		}
	});
	
	
	//Commuter les 2 blocks 'div.content-center' et 'div.content-righter' en version mobile
	var Width = $(document).width();
	if(Width <= 1155){
		var contentRighter = $('div.content-righter').clone();
		$('div.content-righter').remove();
		contentRighter.insertAfter('div.content-center');
		
		//Block challenge en cours
		$("div.content-challenge-en-cours").on({
			mouseenter: function () {
				$('p#block-active-hover-challenge-en-cours').show();
				$(this).css('background', 'white');
				$(this).css('box-shadow', '1px 1px 2px grey');
			},
			mouseleave: function () {
				$('p#block-active-hover-challenge-en-cours').hide();
				$(this).css('background', '#f5f5f5');
				$(this).css('box-shadow', 'none');
			}
		});
		
		//Block flux cadeaux
		$("div.content-flux-cadeaux").on({
			mouseenter: function () {
				$('p#block-active-hover-flux-cadeaux').show();
				$(this).css('background', 'white');
				$(this).css('box-shadow', '1px 1px 2px grey');
			},
			mouseleave: function () {
				$('p#block-active-hover-flux-cadeaux').hide();
				$(this).css('background', '#f5f5f5');
				$(this).css('box-shadow', 'none');
			}
		});
		
		//Block réseau sociaux
		$("div.content-rs-sociaux").on({
			mouseenter: function () {
				$('p#block-active-hover-rs-sociaux').show();
				$(this).css('background', 'white');
				$(this).css('box-shadow', '1px 1px 2px grey');
			},
			mouseleave: function () {
				$('p#block-active-hover-rs-sociaux').hide();
				$(this).css('background', '#f5f5f5');
				$(this).css('box-shadow', 'none');
			}
		});
	}
	
	//Lecture video
	$(document).on('click', 'span.video-slide-play-btn', function(){
		var IdVideo = $(this).attr('data-url');
		
		//Ouvre le popUp
		$('#conteneur-popup').show();
		$('#body-popup').removeAttr('style');
		$('#body-popup').show();
		$('#body-popup').html('');
		
		var Chargements = $('p.chargementAjax').clone();
		$('#body-popup').html(Chargements);
		$('#body-popup').find('p.chargementAjax').show();
		
		setTimeout(function(){
			$.ajax({
				type: 'POST',
				url: UrlAfficheVideo,
				data:'video_id='+IdVideo+'',
				success: function(retour){
					$('div#body-popup').html(retour);
				}
			});
		}, 500);
	});
	
	//fermer popUp 
	$(document).on('click', 'span#fermerPopUp', function(){
		$('#conteneur-popup').hide();
		$('#body-popup').hide();
		$('#body-popup').html('');
	});
	// ouverture detail aperçu
	$(document).on('click', '.preview-icon', function(e){
		e.preventDefault();
		var detail_container = $(this).parents('.main-container').find('.standard-content-detail-container');
		var chevron_up = $(this).parent().children('.chevron-up');
		var preview_icon_container = $(this).parent();
		$(this).hide();
		chevron_up.show();
		preview_icon_container.css('align-items', 'flex-start');
		detail_container.show();
	});
	// fermeture detail aperçu
	$(document).on('click', '.chevron-up', function(e){
		e.preventDefault();
		var detail_container = $(this).parents('.main-container').find('.standard-content-detail-container');
		var preview_icon = $(this).parent().children('.preview-icon');
		var preview_icon_container = $(this).parent();
		$(this).hide();
		preview_icon.show();
		preview_icon_container.css('align-items', 'center');
		detail_container.hide();
	});
});