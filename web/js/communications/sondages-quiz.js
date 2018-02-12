function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('img#img-preview').attr('src', e.target.result);
      $('img#img-preview').show();
    }

    reader.readAsDataURL(input.files[0]);
  }
}

function AjoutQuestion(id){
	$('.chargementAjax').removeClass('hidden');
	var UrlAjoutQuestions = $('input#UrlAjoutQuestions').val();
	setTimeout(function(){
		$.ajax({
			type : "POST",
			url: UrlAjoutQuestions,
			data : 'idQuestion='+id+'',
			success: function(reponse){
				$('.chargementAjax').addClass('hidden');
				$('div#question-emplacement-'+id+'').html(reponse);
				
				//On load les 3 reponses
				AjoutReponses(id, 1);
			}
		});
	}, 300);
}

function AjoutReponses(idQuestions, idReponses){
	$('.chargementAjax').removeClass('hidden');
	var UrlAjoutReponses = $('input#UrlAjoutReponses').val();
	setTimeout(function(){
		$.ajax({
			type : "POST",
			url: UrlAjoutReponses,
			data : 'idReponses='+idReponses+'&idQuestions='+idQuestions+'',
			success: function(reponse){
				$('.chargementAjax').addClass('hidden');
				$('div#reponses-emplacement-'+idQuestions+'-'+idReponses+'').html(reponse);
			}
		});
	}, 300);
}

$(document).ready(function(){
	//Initialisation de la liste des questions
	AjoutQuestion('1');
	
	//Activer/desactiver section 
	$('section.fieldset').mouseover(function(){
		$(this).removeClass('inactive');
		$(this).addClass('active');
		$(this).find('div.block-active-hover').show();
	}).mouseout(function(){
		$(this).removeClass('active');
		$(this).addClass('inactive');
		$(this).find('div.block-active-hover').hide();
	});
	
	//Simuler click input file
	$(document).on('click', 'button.btn-upload-img-sondage-quiz', function(){
		$('input#input-upload-img-sondage-quiz').click();
	});
	
	//Previsualiser l'image
	$(document).on('change', 'input#input-upload-img-sondage-quiz', function(){
		readURL(this);
	});
	
	//Ajout des questions
	$(document).on('click', 'a.add-field-question', function(){
		var ListQuestion = new Array;
		$('div.question-emplacement').each(function(e){
			var ContentQuestionaire = $.trim($(this).html());
			if(ContentQuestionaire == ''){
				ListQuestion.push(e);
			}
		});
		
		var IdQuestion = parseInt(ListQuestion[0])+1;
		if(IdQuestion <= 9){
			AjoutQuestion(IdQuestion);
		}
		return false;
	});
	
	//Ajout reponse 
	$(document).on('click', 'a.add-field-link-reponse', function(){
		var IdAttr = $(this).attr('id');
		var IdArray = new Array;
		IdArray = IdAttr.split('-');
		var IdQuestion = IdArray[4];
		
		var ListReponses = new Array;
		$('div.reponses-emplacement-'+IdQuestion+'').each(function(e){
			var ContentReponses = $.trim($(this).html());
			if(ContentReponses == ''){
				ListReponses.push(e);
			}
		});
		
		var IdReponses = parseInt(ListReponses[0])+1;
		if(IdReponses <= 7){
			AjoutReponses(IdQuestion, IdReponses);
		}
		return false;
	});
	
	//Trie des reponses 'up'
	$(document).on('click', 'a.reorder-up-field-row-link', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		
		var IdQuestions = ArrayAttrId[5];
		var IdReponses = ArrayAttrId[6];
		
		var Parents = $('div#reponses-emplacement-'+IdQuestions+'-'+IdReponses+'');
		
		var PrevElement = Parents.prev();
		if(PrevElement.hasClass('reponses-emplacement-'+IdQuestions+'')){
			Parents.insertBefore(PrevElement);
			var Ordres = $('input#order-'+IdQuestions+'-'+IdReponses+'').val();
			$('input#order-'+IdQuestions+'-'+IdReponses+'').val(parseInt(Ordres)-1);
			
			var IdPrevElement = PrevElement.attr('id');
			var ArrayIdPrevElement = new Array;
			ArrayIdPrevElement = IdPrevElement.split('-');
			
			var OrdersPrev = PrevElement.find('input#order-'+ArrayIdPrevElement[2]+'-'+ArrayIdPrevElement[3]+'').val();
			PrevElement.find('input#order-'+ArrayIdPrevElement[2]+'-'+ArrayIdPrevElement[3]+'').val(parseInt(OrdersPrev)+1);
		}
		return false;
	});
	
	//Trie des reponses 'down'
	$(document).on('click', 'a.reorder-down-field-row-link', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		
		var IdQuestions = ArrayAttrId[5];
		var IdReponses = ArrayAttrId[6];
		
		var Parents = $('div#reponses-emplacement-'+IdQuestions+'-'+IdReponses+'');
		
		var NextElement = Parents.next();
		if(NextElement.hasClass('reponses-emplacement-'+IdQuestions+'') && $.trim(NextElement.html()) != ''){
			Parents.insertAfter(NextElement);
			var Ordres = $('input#order-'+IdQuestions+'-'+IdReponses+'').val();
			$('input#order-'+IdQuestions+'-'+IdReponses+'').val(parseInt(Ordres)+1);
			
			var IdNextElement = NextElement.attr('id');
			var ArrayIdNextElement = new Array;
			ArrayIdNextElement = IdNextElement.split('-');
			
			var OrdersNext = NextElement.find('input#order-'+ArrayIdNextElement[2]+'-'+ArrayIdNextElement[3]+'').val();
			NextElement.find('input#order-'+ArrayIdNextElement[2]+'-'+ArrayIdNextElement[3]+'').val(parseInt(OrdersNext)-1);
		}
		return false;
	});
	
	//Suppressions des reponses 
	$(document).on('click', 'span.corbeil-input', function(){
		var IdAttr = $(this).attr('id');
		var ArrayIdAttr = new Array;
		ArrayIdAttr = IdAttr.split('-');
		
		var IdQuestions = ArrayIdAttr[2];
		var IdReponses = ArrayIdAttr[3];
		
		$('div#reponses-emplacement-'+IdQuestions+'-'+IdReponses+'').html('');
	});
	
	//Afficher/cacher section 1
	$(document).on('click', 'div#id-ligne-separator', function(){
		if($('div.conteneur-menu-banniere-sondage-quiz').is(':visible')){
			$('div.conteneur-menu-banniere-sondage-quiz').hide();
		}else{
			$('div.conteneur-menu-banniere-sondage-quiz').show();
		}
	});
	
	//Afficher/cacher les questionnaires 
	$(document).on('click', 'div.ligne-separator-question', function(){
		var IdAttr = $(this).attr('id');
		var ArrayIdAttr = new Array;
		ArrayIdAttr = IdAttr.split('-');
		var IdQuestion = ArrayIdAttr[3];
		
		if($('div#content-questionnaire-'+IdQuestion+'').is(':visible')){
			$('div#content-questionnaire-'+IdQuestion+'').hide();
		}else{
			$('div#content-questionnaire-'+IdQuestion+'').show();
		}
	});
});