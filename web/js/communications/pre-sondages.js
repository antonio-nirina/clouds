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

function template(obj) {
    var html = [];
    $.each(obj, function(index, item){
        console.log(html.titre)
        html = $(".nom-quest2").html(item.id);
        
    });
    
    return html;
}

function seletedSondage(AttrId){
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
}

var checked = [];
function getChecked() {
    return checked;
}

$(document).ready(function(){
    $('.chargementAjax').removeClass('hidden');
    $('.main-section').jplist({
        itemsBox: '.list',
        itemPath: '.element',
        panelPath: '.control-panel'
    });
    $('.jplist-no-results').removeClass('hidden-block');
    $('.chargementAjax').addClass('hidden');
});

$(document).ready(function(){ 
    
    /*var objArchived = $('input[name=obj]').val();
    if (JSON.parse(objArchived).length == 0) {
        $(".not-results").removeClass("not-list");
    }*/
    $('.nav-item a').on('shown.bs.tab', function (e) {
        var x = $(e.target).text();
        var y = $(e.relatedTarget).text();
        if (x === 'individuelles') {
            $('.pagination-header').addClass('active');
            $('.download-btn').removeClass('active');
        } else if ( x === 'globales') {
            $('.download-btn').addClass('active');
            $('.pagination-header').removeClass('active');
        }
    })
    
    $(document).on('change', '.data-container .styled-checkbox', function(){
        var checked = getChecked();
        var text = (checked.length == 1)?checked.length+" sondage / sélectionnée":((checked.length > 1)?checked.length+" sondage / sélectionnées":"");
        $(".checked-quiz .sondage-selected ").val(text);
        if (checked.length > 0) {
            $('.checked-quiz').css('display',"flex");
            $('.checked-quiz .text-selected .delete-supp1').css('display','block');
            $('.checked-quiz .text-selected .delete-supp').css('display','block');
        } else {
            $('.checked-quiz').css('display',"none");
            $('.checked-quiz').trigger('hide-block');
        }
    });
    
    $(document).on('click', '.checked-quiz .text-selected .delete-supp1', function(){
        $('.data-container .styled-checkbox').each(function(){
            $(this).prop('checked', false);
        });
        $('.checked-quiz').css('display',"none");
        $('.checked-quiz').trigger('hide-block');
    });
    
    $(document).on('click', '.checked-quiz .supp-supp .delete-supp', function(){
        $('.data-container .styled-checkbox').each(function(){
            $(this).prop('checked', false);
        });
        $('.checked-quiz').css('display',"none");
        $('.checked-quiz').trigger('hide-block');
    });
    
    $(document).on('click', '.detaille .data-container .styled-checkbox', function(){
        if($(this).is(':checked')){
            checked.push($(this).attr('id'));
            
        } else {
            checked.splice(checked.indexOf($(this).attr('id')), 1);
        }
        
    });
    // Selection de type d'action de groupe
    $(document).on('click', '.grouped-action-choice', function(e){
        e.preventDefault();
        var selected_element_container = $(this).parents('.supp-supp');
        selected_element_container.find('button.dropdown-toggle').text($(this).text());
        $('.checked-quiz').find('.btn-supp .btn-valider').attr('data-grouped-action', $(this).attr('data-grouped-action'));
    });
    // suivre pour les dropdown and valide
    $(document).on('hide-block', '.checked-quiz', function(){
        var dropdown_toggle_button = $(this).find('.supp-supp').find('button.suppr');
        dropdown_toggle_button.text(dropdown_toggle_button.attr('data-default'));
        $(this).find('.btn-supp').find('.btn-valider').attr('data-grouped-action', '');
        //$(this).find('.selected-elements-button-container').find('.dropdown-container .delete-input').hide();
        checked = [];
    });
    //submit sondage selected
    $(document).on('click', '.checked-quiz .btn-supp .btn-valider', function(e){
        e.preventDefault();
        if ('undefined' !== typeof $(this).attr('data-grouped-action') && '' != $(this).attr('data-grouped-action')){
            $('.chargementAjax').removeClass('hidden');
            var arr_checked = getChecked();
            var str_checked = arr_checked.join(',');
            var data = {'id_list': str_checked, 'grouped_action_type': $(this).attr('data-grouped-action')};
            if ($('input[name=archived_status]').length > 0 && 'true' == $('input[name=archived_status]').val()) {
                var redirection_url = $('input[name=list-url-archived]').val();
            } else {
                var redirection_url = $('input[name=list-url]').val();
            }
            console.log(data)
            $.ajax({
                type: 'POST',
                url: $(this).attr('data-target-url'),
                data: data,
                success: function(){
                    window.location.replace(redirection_url);
                },
                complete: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }
    });
    
	$('.create_sondage_quiz').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $('input[name=create_sondage_quiz_url]').val();
        $.ajax({
            type: 'GET',
            url: url,
            success: function(data){
                $('#create-sondage-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();                 
                //Initialisation de la liste des questions
                var $QuestionEnCours = $('div.content-questionnaire');
                var Questions = parseInt(parseInt($QuestionEnCours.length) - 1);
                NbreQuestion = Questions;
                $('div.content-questionnaire').each(function(){
                    var AttrId = $(this).attr('id');
                    seletedSondage(AttrId);
                    
                });
                
                if($('input#nbre_questions').val() == '0'){
                    AjouterQuestionCollection();
                }
                
            },
            statusCode: {
                404: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-sondage-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-sondage-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    // édition sondage&quiz, appel de formulaire
    $(document).on('click', '.edit-pre-sondage', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        $.ajax({
            type: 'GET',
            url: url,
            success: function(data){
                $('#edit-sondage-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();
                var $QuestionEnCours = $('div.content-questionnaire');
                var Questions = parseInt(parseInt($QuestionEnCours.length) - 1);
                NbreQuestion = Questions;
                $('div.content-questionnaire').each(function(){
                    var AttrId = $(this).attr('id');
                    seletedSondage(AttrId);
                    
                });
                
                if($('input#nbre_questions').val() == '0'){
                    AjouterQuestionCollection();
                }
            },
            statusCode: {
                404: function(data){
                    $('#edit-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#edit-sondage-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#edit-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#edit-sondage-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#edit-sondage-modal').modal('show');
                setTimeout(function(){
                    $('.chargementAjax').addClass('hidden');
                }, 1050);
            }
        });
    });
    
    // submit create sondage
    
    $(document).on('click', '#create-sondage-modal  .submit .btn-valider', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var redirect = $('input[name=list-url]').val();
        var  url = $('input[name=create_sondage_quiz_url]').val();
        var data = $(this).attr("name");
        console.log(url)
        $("#create-sondage-modal form").ajaxSubmit({
            type: 'POST',
            url: url,
            data: {"data":data},
            success: function(data){
                if(data['error']){
                    $('#create-sondage-modal').find('.modal-body-container').html(data.content);
                    installWysiwyg();
                    var $QuestionEnCours = $('div.content-questionnaire');
                    var Questions = parseInt(parseInt($QuestionEnCours.length) - 1);
                    NbreQuestion = Questions;
                    $('div.content-questionnaire').each(function(){
                        var AttrId = $(this).attr('id');
                        seletedSondage(AttrId);
                        
                    });
                    
                    if($('input#nbre_questions').val() == '0'){
                        AjouterQuestionCollection();
                    }
                } else {
                    window.location.replace(redirect);
                }
            },
            statusCode: {
                404: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-sondage-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-sondage-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
        
    });
    
    // submit Edit sondage
    
    $(document).on('click', '#edit-sondage-modal  .submit .btn-valider', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var redirect = $('input[name=list-url]').val();
        var url = $('input[name=edit_sondage_quiz_url').val();
        var data = $(this).attr("name");
        console.log(url)
        $("#edit-sondage-modal form").ajaxSubmit({
            type: 'POST',
            url: url,
            data: {"data":data},
            success: function(data){
                if(data['error']){
                    $('#edit-sondage-modal').find('.modal-body-container').html(data.content);
                    installWysiwyg();
                    var $QuestionEnCours = $('div.content-questionnaire');
                    var Questions = parseInt(parseInt($QuestionEnCours.length) - 1);
                    NbreQuestion = Questions;
                    $('div.content-questionnaire').each(function(){
                        var AttrId = $(this).attr('id');
                        seletedSondage(AttrId);
                        
                    });
                    
                    if($('input#nbre_questions').val() == '0'){
                        AjouterQuestionCollection();
                    }
                } else {
                    window.location.replace(redirect);
                }
            },
            statusCode: {
                404: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-sondage-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-sondage-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-sondage-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
        
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
    
    //Selection  type Roles 
    $(document).on('click', 'div.dropdownMenuListeTypeRole a.dropdown-item', function(){
        var Id = $(this).attr('data-id');
        var val = $(this).attr('data-value');
        var MenuClicker = $.trim($(this).html());             
        $('select#sondages_quiz_questionnaire_infos_authorized_role').val(val).change();
        $('button#dropdownMenuActionTypeRole-'+Id+'').html(MenuClicker);
        $('div#dropdownMenuListeTypeRole-'+Id+'').hide();
        return false;
    });
    
    //Selection menu type question 
    $(document).on('click', 'button.dropdownMenuActionTypeQuestion', function(){
        var AttrId = $(this).attr('id');
        var ArrayAttrId = new Array;
        ArrayAttrId = AttrId.split('-');
        $('div#dropdownMenuListeTypeUestion-'+ArrayAttrId[1]+'').show();
        //return false;
    });
    
    $(document).on('click', 'button.dropdownMenuActionTypeRole', function(){
        var AttrId = $(this).attr('id');
        var ArrayAttrId = new Array;
        ArrayAttrId = AttrId.split('-');
        $('div#dropdownMenuListeTypeRole-'+ArrayAttrId[1]+'').show();
        //return false;
    });
    
    //publier sondage&Quiz
    
    $(document).on('click', '.publier-pre-sondage', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        var redirect = $('input[name=list-url]').val();
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirect);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    //Archived Sondage&Quiz
    
    $(document).on('click', ".archived-pre-sondage", function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        var redirect_target = $('input[name=list-url]').val();
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirect_target);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    //Show POPUP Delete
    
    $(document).on('click', ".delete-pre-sondage", function(e){
        e.preventDefault();
        var url = $(this).attr('data-url');
        $('#confirm-delete-news-modal').find('.confirm-delete').attr('data-url', url);
        $('#confirm-delete-news-modal').modal('show');
    });
    
    //Delete Sondage&Quiz
    $(document).on('click', '#confirm-delete-news-modal .confirm-delete', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        console.log(url)
        var redirect_url = $('input[name=list-url]').val();
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirect_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    //Cloture Sondage
    
    $(document).on('click', ".cloture-pre-sondage", function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        var redirect_target = $('input[name=list-url]').val();
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirect_target);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    $(document).on('click', '.restore-news-post', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var target_url = $(this).attr('data-url');
        var redirection_url = $('input[name=list-url]').val();
        $.ajax({
            type: 'POST',
            url: target_url,
            success: function(){
                window.location.replace(redirection_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    //Duplicate
    
    $(document).on('click', ".duplique-pre-sondage", function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        console.log(url)
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data){
                $('#duplicate-news-modal').find('.modal-body-container').html(data.content);
                $('#duplicate-news-modal').find('.general-message').html('');
            },
            statusCode: {
                404: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#duplicate-news-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
            
        });
    });
    
    // duplication, soumission
    $(document).on('click', '#duplicate-news-modal .save.save-duplication', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var duplicate_template_url = $(this).attr('data-target-url');
        var redirect_target = $('input[name=list-url]').val();
        $('#duplicate-news-modal form').ajaxSubmit({
            type: 'POST',
            url: duplicate_template_url,
            success: function(data){
                if(data['error']){
                    $('#duplicate-news-modal').find('.modal-body-container').html(data.content);
                    $('#duplicate-news-modal').find('.general-message').html('');
                } else {
                    window.location.replace(redirect_target);
                }
            },
            error: function(){
                $('#duplicate-template-dialog').find('.close-modal').show();
            },
            statusCode: {
                404: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                    
                },
                500: function(data){
                    $('#duplicate-news-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#duplicate-news-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
            
        });
    });
    
    // duplication, annulation
    $(document).on('click', '#duplicate-news-modal .cancel', function(e){
        e.preventDefault();
        $('#duplicate-news-modal').find('.modal-body-container').html('');
        $('#duplicate-news-modal').find('.error-message-container.general-message').text('');
        $('#duplicate-news-modal').modal('hide');
    });
    
    
    var SondagesQuizType = $('input.choix-type-action:checked').val();
    if(!SondagesQuizType){
        $('input#sondages_quiz_questionnaire_infos_type_sondages_quiz_1').prop('checked', true);
        HideShowSondagesOrQuiz('2');
    }
    HideShowSondagesOrQuiz(SondagesQuizType);
    
    
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
        var attrTitre = $('.titre-section-sondage-quiz').attr('data-titre');
        $('.chargementAjax').removeClass('hidden');
        var UriDeleteQuestions = $('input#UriDeleteQuestions').val();
        setTimeout(function(){
            $.ajax({
                type : "POST",
                url: UriDeleteQuestions,
                data : 'IdQuestion='+IdQuestion+'',
                success: function(reponse){
                    if (typeof response == "undefined") {
                        $('.chargementAjax').addClass('hidden');
                        $("div#content-block-question-"+IdQuestion+"").remove();
                    } else{
                        $('.chargementAjax').addClass('hidden');
                        $('div#confirm-delete-question-dialog-'+IdQuestion+'').modal('hide');
                        location.reload();
                    }
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
    
    $(document).on('click', '.stat-pre-sondage', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        $.ajax({
            type: 'GET',
            url: url,
            success: function(data){
                $('#edit-sondage-modal').find('.modal-body-container').html(data.content);          
            },
            statusCode: {
                404: function(data){
                    // $('#edit-sondage-modal').find('.modal-body-container').html('');
                    // var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    // $('#edit-sondage-modal').find('.error-message-container.general-message').text(message);
                    console.log('404');
                },
                500: function(data){
                    // $('#edit-sondage-modal').find('.modal-body-container').html('');
                    // var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    // $('#edit-sondage-modal').find('.error-message-container.general-message').text(message);
                    console.log('500');
                }
            },
            complete: function(){
                $('#edit-sondage-modal').modal('show');
                setTimeout(function(){
                    $('.chargementAjax').addClass('hidden');
                }, 1050);
                Highcharts.chart('container_bare', {
                    tooltip: {
                        enabled: false
                    },
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: ['Parce que...', 'Pour des raisons...', 'Sans aucune...'],
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        reversed: true
                    },
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                crop: false,
                                overflow: 'none',
                                style: {
                                    color: '#7682da',
                                    fontSize: 17
                                },
                                formatter: function () {
                                    var pcnt = (this.y / 30) * 100;
                                    return this.y + ' <span style="color: #000000; font-weight: normal;">(' + Highcharts.numberFormat(pcnt) + '%)</span>';
                                }
                            }
                        }
                    },
                    series: [{
                        showInLegend: false,
                        name: 'John',
                        data: [
                            {y: 10, color: '#7682da'},
                            {y: 20, color: '#7682da'},
                            {y: 0, color: '#7682da'}
                        ]
                    }],
                    credits: {
                        enabled: false
                    }
                });
                Highcharts.chart('container_column', {
                    tooltip: {
                        enabled: false
                    },
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: ['médiocre', 'moyen', 'satisfaisant', 'très bon', 'excellent'],
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        reversed: true
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true,
                                crop: false,
                                overflow: 'none',
                                align: 'center',
                                style: {
                                    color: '#7682da',
                                    fontSize: 17
                                },
                                formatter: function () {
                                    var pcnt = (this.y / 30) * 100;
                                    return this.y + '<br/> <span style="color: #000000; font-weight: normal;">(' + Highcharts.numberFormat(pcnt) + '%)</span>';
                                }
                            }
                        }
                    },
                    series: [{
                        showInLegend: false,
                        name: 'John',
                        data: [
                            {y: 0, color: '#7682da'},
                            {y: 10, color: '#7682da'},
                            {y: 0, color: '#7682da'},
                            {y: 20, color: '#7682da'},
                            {y: 0, color: '#7682da'}
                        ]
                    }],
                    credits: {
                        enabled: false
                    }
                });
                Highcharts.chart('container_column2', {
                    tooltip: {
                        enabled: false
                    },
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    legend: {
                        align: 'left',
                        verticalAlign: 'top',
                        x: 18,
                        symbolHeight: 10,
                        symbolWidth: 15,
                        symbolRadius: 0
                    },
                    xAxis: {
                        categories: ['Lorem', 'ipsum', 'dolor', 'sit amet'],
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        },
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            crop: false,
                            overflow: 'none'
                        }
                    },
                    series: [{
                        name: '1',
                        color: '#1dc7ff',
                        marker : {enabled: false},
                        data: [50, 10, 0, 0]
                    }, {
                        name: '2',
                        color: '#ff0000',
                        marker: {
                            enabled: false
                        },
                        marker : {enabled: false},
                        data: [10, 40, 0, 0]
                    }, {
                        name: '3',
                        color: '#ff9600',
                        marker : {enabled: false},
                        data: [10, 10, 0, 20]
                    }, {
                        name: '4',
                        color: '#ff02fc',
                        marker : {enabled: false},
                        data: [10, 50, 0, 10]
                    }, {
                        name: '5',
                        color: '#14a400',
                        marker : {enabled: false},
                        data: [10, 0, 20, 10]
                    }, {
                        name: 'NSP',
                        color: '#838383',
                        marker : {enabled: false},
                        data: [20, 0, 10, 10]
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    });
    
    // restauration par groupe par bouton
    // soumission action de groupe
    $(document).on('click', '.restore-data', function(e){
        e.preventDefault();
        var arr_checked = getChecked();
        if (arr_checked.length > 0) {
            $('.chargementAjax').removeClass('hidden');
            var str_checked = arr_checked.join(',');
            var data = {'id_list': str_checked, 'grouped_action_type': 'restore'};
            var redirection_url = $('input[name=list-url-archived]').val();
            $.ajax({
                type: 'POST',
                url: $(this).attr('data-url'),
                data: data,
                success: function(){
                    window.location.replace(redirection_url);
                },
                complete: function(){
                    $('.chargementAjax').addClass('hidden');
                }
            });
        }
    });
    
    $(document).on('click', '.restore-arhived-sondage', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var redirection_url = $('input[name=list-url-archived]').val();
        var url = $(this).attr('data-url');
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirection_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });
    
    $(document).on('click', ".delete-arhived-sondage", function(e){
        e.preventDefault();
        var url = $(this).attr('data-url');
        $('#confirm-delete-news-modal').find('.confirm-delete-archive').attr('data-url', url);
        $('#confirm-delete-news-modal').modal('show');
    });
    
    //Delete Sondage&Quiz Archived
    $(document).on('click', '#confirm-delete-news-modal .confirm-delete-archive', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var url = $(this).attr('data-url');
        var redirect_url = $('input[name=list-url-archived]').val();
        console.log(url)
        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                window.location.replace(redirect_url);
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
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