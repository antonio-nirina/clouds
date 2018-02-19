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
	//AjoutQuestion('1');
	NbreQuestion = 0;
	AjouterQuestionCollection();
	
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
		$('input#sondages_quiz_image').click();
	});
	
	//Previsualiser l'image
	$(document).on('change', 'input#sondages_quiz_image', function(){
		readURL(this);
	});
	
	//Trie des reponses 'up'
	$(document).on('click', 'a.reorder-up-field-row-link', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		
		var IdQuestions = ArrayAttrId[5];
		var IdReponses = ArrayAttrId[6];
		
		var Parents = $('div#content-reponses-ligne-'+IdQuestions+'-'+IdReponses+'');
		
		var PrevElement = Parents.prev();
		if(PrevElement.hasClass('content-reponses-ligne-'+IdQuestions+'')){
			Parents.insertBefore(PrevElement);
			var Ordres = $('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestions+'_sondages_quiz_reponses_'+IdReponses+'_ordre').val();
			$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestions+'_sondages_quiz_reponses_'+IdReponses+'_ordre').val(parseInt(Ordres)-1);
			
			var IdPrevElement = PrevElement.attr('id');
			var ArrayIdPrevElement = new Array;
			ArrayIdPrevElement = IdPrevElement.split('-');
			
			var OrdersPrev = PrevElement.find('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+ArrayIdPrevElement[2]+'_sondages_quiz_reponses_'+ArrayIdPrevElement[3]+'_ordre').val();
			PrevElement.find('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+ArrayIdPrevElement[2]+'_sondages_quiz_reponses_'+ArrayIdPrevElement[3]+'_ordre').val(parseInt(OrdersPrev)+1);
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
		
		var Parents = $('div#content-reponses-ligne-'+IdQuestions+'-'+IdReponses+'');
		
		var NextElement = Parents.next();
		if(NextElement.hasClass('content-reponses-ligne-'+IdQuestions+'') && $.trim(NextElement.html()) != ''){
			Parents.insertAfter(NextElement);
			var Ordres = $('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestions+'_sondages_quiz_reponses_'+IdReponses+'_ordre').val();
			$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestions+'_sondages_quiz_reponses_'+IdReponses+'_ordre').val(parseInt(Ordres)+1);
			
			var IdNextElement = NextElement.attr('id');
			var ArrayIdNextElement = new Array;
			ArrayIdNextElement = IdNextElement.split('-');
			
			var OrdersNext = NextElement.find('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+ArrayIdNextElement[2]+'_sondages_quiz_reponses_'+ArrayIdNextElement[3]+'_ordre').val();
			NextElement.find('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+ArrayIdNextElement[2]+'_sondages_quiz_reponses_'+ArrayIdNextElement[3]+'_ordre').val(parseInt(OrdersNext)-1);
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
		
		$('div#content-reponses-ligne-'+IdQuestions+'-'+IdReponses+'').remove();
		var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+IdQuestions+'').val());
		var NumeroRepEnSuivant = NumeroRepEnCours - 1;
		$('input#nbre_reponses_par_questions_'+IdQuestions+'').val(NumeroRepEnSuivant);
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
	
	//Delete banniére
	$(document).on('click', 'img#img-delete-img-banniere', function(){
		$('.chargementAjax').removeClass('hidden');
		var UrlDeleteBanniere = $('input#UrlDeleteBanniere').val();
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UrlDeleteBanniere,
				data : 'type=1',
				success: function(reponse){
					if(reponse == 'ok'){
						location.reload();
						$('.chargementAjax').addClass('hidden');
					}
				}
			});
		}, 300);				
	});
	
	//Selection menu type question 
	$(document).on('click', 'button.dropdownMenuActionTypeQuestion', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		$('div#dropdownMenuListeTypeUestion-'+ArrayAttrId[1]+'').show();
		//return false;
	});
	
	//Selection menu type question 
	$(document).on('click', 'div.dropdownMenuListeTypeUestion a.dropdown-item', function(){
		var Id = $(this).attr('data-id');
		var MenuClicker = $.trim($(this).html());
		
		var choix = '0';
		if(MenuClicker == 'cases à cocher'){
			choix = '1';
		}else if(MenuClicker == 'échelle linéaire'){
			choix = '2';
		}else if(MenuClicker == 'tableau à choix mutltiples'){
			choix = '3';
		}
		
		$('select#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+Id+'_type_question').val(choix).change();
		$('button#dropdownMenuActionTypeQuestion-'+Id+'').html(MenuClicker);
		$('div#dropdownMenuListeTypeUestion-'+Id+'').hide();
		return false;
	});
	
	$(document).on('change', 'input.choix-type-action', function(){
		var Type = $(this).val();
		//Sondages
		if(Type == '1'){
			$('span.choice-input-bonne-reponse').each(function(i){
				//if($(this).is(':visible')){
				var AttrId = $(this).attr('id');
				var ArrayAttrId = new Array;
				ArrayAttrId = AttrId.split('-');
				var IdQuestion = ArrayAttrId[4];
				var IdReponse = ArrayAttrId[5];
				
				//cacher les choix de la bonne reponse
				if($('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').hasClass('input-form-text-reponse-quiz')){
					$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').removeClass('input-form-text-reponse-quiz');
					$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').addClass('input-form-text-reponse-sondages');
					$(this).hide();
				}
				//}
			});
			
			$('div.legende-choix-sondages-quiz').each(function(p){
				$(this).hide();
			});
		//Quiz
		}else{
			$('span.choice-input-bonne-reponse').each(function(i){
				//if(!$(this).is(':visible')){
					var AttrId = $(this).attr('id');
					var ArrayAttrId = new Array;
					ArrayAttrId = AttrId.split('-');
					var IdQuestion = ArrayAttrId[4];
					var IdReponse = ArrayAttrId[5];
					
					//Afficher les choix des la bonne reponse
					$(this).show();
					if($('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').hasClass('input-form-text-reponse-sondages')){
						$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').removeClass('input-form-text-reponse-sondages');
						$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_sondages_quiz_reponses_'+IdReponse+'_reponses').addClass('input-form-text-reponse-quiz');
					}
				//}
			});
			
			$('div.legende-choix-sondages-quiz').each(function(p){
				$(this).show();
			});
		}
	});
	
	
	$(document).on('click', 'a.add-field-question', function(){
		if(NbreQuestion < 9){
			AjouterQuestionCollection();
		}
		return false;
	});
	
	$(document).on('click', 'a.add-field-link-reponse', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		
		var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+ArrayAttrId[4]+'').val());
		//On limite à 7 les nombres des reponses
		if(NumeroRepEnCours < 7){
			AjouterReponsesCollection(ArrayAttrId[4]);
		}
		
		
		return false;
	});
	
	$(document).on('click', 'button.confirm-delete-quiz', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var Id = ArrayAttrId[3];
		
		
		$('div#confirm-delete-dialog-'+Id+'').modal('hide');
		$('.chargementAjax').removeClass('hidden');
		var UriDeleteQuiz = $('input#UriDeleteQuiz').val();
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriDeleteQuiz,
				data : 'Id='+Id+'',
				success: function(reponse){
					$('.chargementAjax').addClass('hidden');
					location.reload();
				}
			});
		}, 300);
		return false;
	});
});

//Ajouter des champs questions
function AjouterQuestionCollection(){
	
	NbreQuestion++;
	var $conteneur = $('div#collectionQuestions');
	var IterationQuestion = $conteneur.find(':input').length;
	var $questions_prototype = $($conteneur.attr('data-prototype-question').replace(/__name__label__/g, '').replace(/__opt_questions__/g, IterationQuestion).replace(/__question_num__/g, NbreQuestion));
	$conteneur.append($questions_prototype);
	
	AjouterReponsesCollection(IterationQuestion);
	IterationQuestion++;
}

//Ajouter des champs reponses
function AjouterReponsesCollection(questions){
	//Incrementer les numero des reponses
	var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+questions+'').val());
	var NumeroRepEnSuivant = NumeroRepEnCours + 1;
	$('input#nbre_reponses_par_questions_'+questions+'').val(NumeroRepEnSuivant);
	
	//Affichage de la reponse
	var $conteneur = $('div#collectionReponses-'+questions+'');
	var IterationReponses = $conteneur.find(':input').length;
	var $reponses_prototype = $($conteneur.attr('data-prototype-reponse').replace(/__opt_reponse__/g, IterationReponses).replace(/__reponses_num__/g, NumeroRepEnSuivant));
	$conteneur.append($reponses_prototype);
	
	//Incrementer l'ordre des champs reponses
	$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+questions+'_sondages_quiz_reponses_'+IterationReponses+'_ordre').val(NumeroRepEnSuivant);
	
	//Verifier quelle est le type en cours (Sondages / Quiz)
	var SondagesQuizType = $('input.choix-type-action:checked').val();
	if(SondagesQuizType == '1'){
		$('input.choix-type-action:checked').val(1).change();
	}else{
		$('input.choix-type-action:checked').val(2).change();
	}
	
	IterationReponses++;
}