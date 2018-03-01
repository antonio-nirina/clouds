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

function HideShowSondagesOrQuiz(Type){
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
			if($(this).next('input.input-switch-type').hasClass('input-form-text-reponse-quiz')){
				$(this).next('input.input-switch-type').removeClass('input-form-text-reponse-quiz');
				$(this).next('input.input-switch-type').addClass('input-form-text-reponse-sondages');
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
				if($(this).next('input.input-switch-type').hasClass('input-form-text-reponse-sondages')){
					$(this).next('input.input-switch-type').addClass('input-form-text-reponse-quiz');
					$(this).next('input.input-switch-type').removeClass('input-form-text-reponse-sondages');
				}
			//}
		});
		
		$('div.legende-choix-sondages-quiz').each(function(p){
			$(this).show();
		});
	}
}

$(document).ready(function(){ 
	//Verifier quelle est le type en cours (Sondages / Quiz)
	var SondagesQuizType = $('input.choix-type-action:checked').val();
	if(!SondagesQuizType){
		$('input#sondages_quiz_questionnaire_infos_type_sondages_quiz_1').prop('checked', true);
		HideShowSondagesOrQuiz('2');
	}
	HideShowSondagesOrQuiz(SondagesQuizType);
	
	//Initialisation de la liste des questions
	var $QuestionEnCours = $('div.content-questionnaire');
	var Questions = parseInt(parseInt($QuestionEnCours.length) - 1);
	NbreQuestion = Questions;
	
	//Set select type_question 
	$('div.content-questionnaire').each(function(s){
		var AttrId = $(this).attr('id');
		if(AttrId){
			var ArrayAttrId = new Array;
			ArrayAttrId = AttrId.split('-');
			var Id = ArrayAttrId[2];
			var TypeQuestions = $('select#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+Id+'_type_question').val();
			if(TypeQuestions > 0){
				if(TypeQuestions == 1){
					$('button#dropdownMenuActionTypeQuestion-'+Id+'').html('cases à cocher');
				}else if(TypeQuestions == 2){
					$('button#dropdownMenuActionTypeQuestion-'+Id+'').html('choix multiples');
				}else if(TypeQuestions == 3){
					$('button#dropdownMenuActionTypeQuestion-'+Id+'').html('échelle linéaire');
				}else if(TypeQuestions == 4){
					$('button#dropdownMenuActionTypeQuestion-'+Id+'').html('tableau à choix mutltiples');
				}
			}
		}
	});
	
	if($('input#nbre_questions').val() == '0'){
		AjouterQuestionCollection();
	}
	
	//Activer/desactiver section 
	$('section.fieldset').mouseover(function(){
		$(this).removeClass('inactive');
		$(this).addClass('active');
		$(this).find('div.block-active-hover').show();
		$('.cke_wysiwyg_frame').contents().find('body').css('background-color', 'white');
	}).mouseout(function(){
		$(this).removeClass('active');
		$(this).addClass('inactive');
		$(this).find('div.block-active-hover').hide();
		$('.cke_wysiwyg_frame').contents().find('body').css('background-color', '#F5F5F5');
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
			
			var Ordres = Parents.find('span.content-champs-reponses input.ordre-reponses').val();
			Parents.find('span.content-champs-reponses input.ordre-reponses').val(parseInt(Ordres)-1);
			
			var OrdresPrev = PrevElement.find('span.content-champs-reponses input.ordre-reponses').val();
			PrevElement.find('span.content-champs-reponses input.ordre-reponses').val(parseInt(OrdresPrev)+1);
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
			
			var Ordres = Parents.find('span.content-champs-reponses input.ordre-reponses').val();
			Parents.find('span.content-champs-reponses input.ordre-reponses').val(parseInt(Ordres)+1);
			
			var OrdresNext = NextElement.find('span.content-champs-reponses input.ordre-reponses').val();
			NextElement.find('span.content-champs-reponses input.ordre-reponses').val(parseInt(OrdresNext)-1);
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
		
		var DataReponseId = $(this).attr('data-reponse');
		if(DataReponseId > 0){
			$('.chargementAjax').removeClass('hidden');
			var UriDeleteReponsesSondagesQuiz = $('input#UriDeleteReponsesSondagesQuiz').val();
			setTimeout(function(){
				$.ajax({
					type : "POST",
					url: UriDeleteReponsesSondagesQuiz,
					data : 'IdReponses='+DataReponseId+'',
					success: function(reponse){
						$('.chargementAjax').addClass('hidden');
						$('div#content-reponses-ligne-'+IdQuestions+'-'+IdReponses+'').remove();
						var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+IdQuestions+'').val());
						var NumeroRepEnSuivant = NumeroRepEnCours - 1;
						$('input#nbre_reponses_par_questions_'+IdQuestions+'').val(NumeroRepEnSuivant);
						location.reload();
					}
				});
			}, 300);
		}else{
			$('div#content-reponses-ligne-'+IdQuestions+'-'+IdReponses+'').remove();
			var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+IdQuestions+'').val());
			var NumeroRepEnSuivant = NumeroRepEnCours - 1;
			$('input#nbre_reponses_par_questions_'+IdQuestions+'').val(NumeroRepEnSuivant);
		}
		
		return false;
	});
	
	//Afficher/cacher section 1
	$(document).on('click', 'div#id-ligne-separator', function(){
		if($('div.conteneur-menu-banniere-sondage-quiz').is(':visible')){
			$('div.conteneur-menu-banniere-sondage-quiz').hide();
			$('div#id-ligne-separator').removeClass('ligne-separator-hide');
			$('div#id-ligne-separator').addClass('ligne-separator');
		}else{
			$('div.conteneur-menu-banniere-sondage-quiz').show();
			$('div#id-ligne-separator').addClass('ligne-separator-hide');
			$('div#id-ligne-separator').removeClass('ligne-separator');
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
		}else if(MenuClicker == 'choix multiples'){
			choix = '2';
		}else if(MenuClicker == 'échelle linéaire'){
			choix = '3';
		}else if(MenuClicker == 'tableau à choix mutltiples'){
			choix = '4';
		}
		
		$('select#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+Id+'_type_question').val(choix).change();
		$('button#dropdownMenuActionTypeQuestion-'+Id+'').html(MenuClicker);
		$('div#dropdownMenuListeTypeUestion-'+Id+'').hide();
		return false;
	});
	
	$(document).on('change', 'input.choix-type-action', function(){
		var Type = $(this).val();

		HideShowSondagesOrQuiz(Type);
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
		
		var Type = $(this).attr('data-type');
		//On limite à 7 les nombres des reponses
		if(NumeroRepEnCours < 7){
			AjouterReponsesCollection(ArrayAttrId[4], Type);
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
		var UriSondagesQuiz = $('input#UriSondagesQuiz').val();
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriDeleteQuiz,
				data : 'Id='+Id+'',
				success: function(reponse){
					$('.chargementAjax').addClass('hidden');
					document.location.href = UriSondagesQuiz;
				}
			});
		}, 300);
		return false;
	});
	
	$(document).on('click', 'span.edit-post-questions', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var IdQuestion = ArrayAttrId[3];
		
		$('div#content-questionnaire-apercu-'+IdQuestion+'').hide();
		$('div#content-questionnaire-'+IdQuestion+'').show();
		$('div#ligne-separator-question-'+IdQuestion+'').show();
	});
	
	$(document).on('click', 'div.ligne-separator-question-hide', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var IdQuestion = ArrayAttrId[3];
		
		$('div#content-questionnaire-apercu-'+IdQuestion+'').show();
		$('div#content-questionnaire-'+IdQuestion+'').hide();
		$('div#ligne-separator-question-'+IdQuestion+'').hide();
		
		var InputQuestionSaisie = $('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+IdQuestion+'_questions').val();
		$('h3#h3-block-title-'+IdQuestion+'').html(InputQuestionSaisie);
		$('h5#h5-block-title-'+IdQuestion+'').html(InputQuestionSaisie);
	});
	
	$(document).on('click', 'button.confirm-delete-question', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var IdQuestion = ArrayAttrId[4];
		$('.chargementAjax').removeClass('hidden');
		var UriDeleteQuestions = $('input#UriDeleteQuestions').val();
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriDeleteQuestions,
				data : 'IdQuestion='+IdQuestion+'',
				success: function(reponse){
					$('.chargementAjax').addClass('hidden');
					$('div#confirm-delete-question-dialog-'+IdQuestion+'').modal('hide');
					location.reload();
				}
			});
		}, 300);
		return false;
	});
	
	$(document).on('click', 'a.reorder-up-field-row-link-question', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var IdQuestion = ArrayAttrId[6];
		
		var Parents = $('div#content-block-question-'+IdQuestion+'');
		var PrevElement = Parents.prev();
		if(PrevElement.hasClass('content-block-question')){
			Parents.insertBefore(PrevElement);
			
			var Ordres = Parents.find('input.ordre-questions').val();
			Parents.find('input.ordre-questions').val(parseInt(Ordres)-1);
			
			var OrdresPrev = PrevElement.find('input.ordre-questions').val();
			PrevElement.find('input.ordre-questions').val(parseInt(OrdresPrev)+1);
		}
		return false;
	});
	
	$(document).on('click', 'a.reorder-down-field-row-link-question', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var IdQuestion = ArrayAttrId[6];
		
		var Parents = $('div#content-block-question-'+IdQuestion+'');
		var NextElement = Parents.next();
		if(NextElement.hasClass('content-block-question') && $.trim(NextElement.html()) != ''){
			Parents.insertAfter(NextElement);
			
			var Ordres = Parents.find('input.ordre-questions').val();
			Parents.find('input.ordre-questions').val(parseInt(Ordres)+1);
			
			var OrdresNext = NextElement.find('input.ordre-questions').val();
			NextElement.find('input.ordre-questions').val(parseInt(OrdresNext)-1);
		}
		return false;
	});
	
	$(document).on('click', 'span.delete-input-reponse', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		
		var IdQuestion = ArrayAttrId[3];
		var IdReponse = ArrayAttrId[4];
		
		var Prevs = $(this).prev();
		Prevs.find('input.input-form-text-reponse-quiz').val('');
		return false;
	});
});

//Ajouter des champs questions
function AjouterQuestionCollection(){
	var NumeroQuestionEnCours = parseInt($('input#nbre_questions').val());
	var NumeroQuestionSuivant = NumeroQuestionEnCours + 1;
	
	NbreQuestion++;
	var $conteneur = $('div#collectionQuestions');
	var IterationQuestion = $conteneur.find(':input').length;
	var $questions_prototype = $($conteneur.attr('data-prototype-question').replace(/__name__label__/g, '').replace(/__opt_questions__/g, IterationQuestion).replace(/__question_num__/g, NbreQuestion));
	$conteneur.append($questions_prototype);
	
	//Incrementer l'ordre des champs reponses
	$('div#content-questionnaire-'+IterationQuestion+'').find('input.ordre-questions').val(NbreQuestion);
	
	AjouterReponsesCollection(IterationQuestion, '0');
	IterationQuestion++;
}

//Ajouter des champs reponses
function AjouterReponsesCollection(questions, type){
	var IterationReponses = $('input#nbre_reponses_par_questions_'+questions+'').val();
	
	//Incrementer les numero des reponses
	var NumeroRepEnCours = parseInt($('input#nbre_reponses_par_questions_'+questions+'').val());
	var NumeroRepEnSuivant = NumeroRepEnCours + 1;
	$('input#nbre_reponses_par_questions_'+questions+'').val(NumeroRepEnSuivant);
	
	//Affichage de la reponse
	var $conteneur = $('div#collectionReponses-'+questions+'');
	
	if(type == '1'){
		var $reponses_prototype = $($conteneur.attr('data-prototype-reponse').replace(/__opt_reponse__/g, IterationReponses).replace(/__reponses_num__/g, NumeroRepEnCours).replace(/__opt_questions__/g, questions-1).replace(/__reponse_libelle__/g, NumeroRepEnSuivant));
		$conteneur.append($reponses_prototype);
		
		//Incrementer l'ordre des champs reponses
		$('div#content-reponses-ligne-'+(questions-1)+'-'+IterationReponses+'').find('input.ordre-reponses').val(NumeroRepEnSuivant);
	}else{
		var $reponses_prototype = $($conteneur.attr('data-prototype-reponse').replace(/__opt_reponse__/g, IterationReponses).replace(/__reponses_num__/g, NumeroRepEnCours).replace(/__opt_questions__/g, questions).replace(/__reponse_libelle__/g, NumeroRepEnSuivant));
		$conteneur.append($reponses_prototype);
		
		//Incrementer l'ordre des champs reponses
		$('div#content-reponses-ligne-'+questions+'-'+IterationReponses+'').find('input.ordre-reponses').val(NumeroRepEnSuivant);
	}
	
	//$('input#sondages_quiz_questionnaire_infos_sondages_quiz_questions_'+questions+'_sondages_quiz_reponses_'+IterationReponses+'_ordre').val(NumeroRepEnSuivant);
	
	//Verifier quelle est le type en cours (Sondages / Quiz)
	var SondagesQuizType = $('input.choix-type-action:checked').val();
	HideShowSondagesOrQuiz(SondagesQuizType);
	
	IterationReponses++;
}