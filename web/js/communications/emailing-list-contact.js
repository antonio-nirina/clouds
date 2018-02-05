$(document).ready(function(){
	
	/*
	$(document).on('keypress', 'input#id_search_table', function(){
		var InfosPress = $(this).val();
		$('div#ListUserContactMailjet_filter input').focus();
		$('div#ListUserContactMailjet_filter input').trigger('change').val(InfosPress);
	});
	*/
	
	//Click sur créer une liste 
	$(document).on('click', 'button#creer-list-contact', function(){
		$('.chargementAjax').removeClass('hidden');
		var UriCreerListContact = $('input#UriCreerListContact').val();
		
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriCreerListContact,
				data : '',
				success: function(reponse){
					$('div#creer-list-contact').find('.body-container').html('');
					$('div#creer-list-contact').find('.body-container').html(reponse);
					$('div#creer-list-contact').modal('show');
					$('.chargementAjax').addClass('hidden');
					//table.destroy();
					var table1 = $('#ListUserContactMailjet').DataTable( {
						lengthChange: false,
						"info":     false,
						/*searching: false,*/
						"columnDefs": [ {
							"targets": 1,
							"orderable": false
						} ]
						//buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
					});

					table1.buttons().container().appendTo( '#ListUserContactMailjet_wrapper .col-md-6:eq(0)' );
					
					//Modification libellé 'Previews && Next'
					OverridePagination();
					
					//Modifier conteneur pagination 
					UpdateWidthPagination();
					
					//Décocher tous les contacts 
					$('input.form-field-published').each(function(i){
						$(this).prop("checked", false);
					});
					
					//Ajouter separateur sur les boutons de pagination
					AddSeparatorPaginate();
				}
			});
		}, 300);
	});
	
	//Click sur suppression de la liste des contacts 
	$(document).on('click', 'a.list-contact-delete', function(){
		var IdList = $(this).attr('data-id');
		
		var Html = '';
		Html += '<div class = "row" style = "margin-top:60px;">';
		Html += '<div class = "col-12"><span>Vous souhaitez supprimer définitivement cette liste!</span></div>';
		Html += '</div>';
		
		Html += '<div class = "row" style = "margin-top:60px;">';
		Html += '<div class = "col-12 col-md-6" style = "text-align:center;"><button id = "oui-supprimer-contact-list" class="btn-valider valider submit-form" data-id = "'+IdList+'">supprimer</button></div>';
		Html += '<div class = "col-12 col-md-6" style = "text-align:center;"><button id = "non-supprimer-contact-list" class="btn-valider valider submit-form" >annuler</button></div>';
		Html += '</div>';
		
		$('div#delete-list-contact').find('.body-container').html('');
		$('div#delete-list-contact').find('.body-container').html(Html);
		$('div#delete-list-contact').modal('show');
		$('.chargementAjax').addClass('hidden');

		return false;
	});
	
	$(document).on('click', 'button#non-supprimer-contact-list', function(){
		$('div#delete-list-contact').modal('hide');
	});
	
	$(document).on('click', 'button#oui-supprimer-contact-list', function(){
		var IdList = $(this).attr('data-id');
		var UriDeleteListContact = $('input#UriDeleteListContact').val();
		$('.chargementAjax').removeClass('hidden');
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriDeleteListContact,
				data : 'IdList='+IdList+'',
				success: function(reponse){
					$('.chargementAjax').addClass('hidden');
					$('div#delete-list-contact').modal('hide');
					location.reload();
				}
			});
		}, 300);
		//$('div#delete-list-contact').modal('hide');
	});
	
	//Click sur édition des contacts
	$(document).on('click', 'a.list-contact-edit', function(){
		var IdList = $(this).attr('data-id');
		$('.chargementAjax').removeClass('hidden');
		var UriEditListContact = $('input#UriEditListContact').val();
		
		setTimeout(function(){
			$.ajax({
				type : "POST",
				url: UriEditListContact,
				data : 'IdList='+IdList+'',
				success: function(reponse){
					$('#edit-list-contact').find('.body-container').html('');
					$('#edit-list-contact').find('.body-container').html(reponse);
					$('#edit-list-contact').modal('show');
					$('.chargementAjax').addClass('hidden');
					//table.destroy();
					var table2 = $('#ListUserContactMailjet').DataTable( {
						"dom": 'rtp',
						lengthChange: false,
						"info":     false,
						searching: false,
						responsive: true,
						"columnDefs": [ {
							"targets": 1,
							"orderable": false
						} ]
						//buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
					});

					table2.buttons().container().appendTo( '#ListUserContactMailjet_wrapper .col-md-6:eq(0)' );
					
					//Modification libellé 'Previews && Next'
					OverridePagination();
					
					//Modifier conteneur pagination 
					UpdateWidthPagination();
					
					//Ajouter separateur sur les boutons de pagination
					AddSeparatorPaginate();
				}
			});
		}, 300);
	});
	
	$(document).on("click", ".form-field-published", function(){
		
        if(!$(this).is(":checked"))
        {
            $(this).parents('.block-table').find('.checkbox-publish-all').prop("checked", false);
        }
    });
	
	$(document).on('click', 'label.check-ligne', function(){
		var DataId = $(this).attr('data-id');
		$('input#cl1-'+DataId+'').click();
	});
	
	$(document).on('click', 'ul.pagination', function(){
		OverridePagination();
		AddSeparatorPaginate();
	});
	
	$(document).on('click', 'table#ListUserContactMailjet th', function(){
		OverridePagination();
		AddSeparatorPaginate();
	});
	
	$(document).on('click', 'button#valider-creation-contact-list', function(){
		//Verifier les champs obligatoires
		var id_nom_contact_list = $.trim($('input#id_nom_contact_list').val());
		
		var cpt = 0;
		var ListIdUser = new Array;
		$('input.contact-ajout:checked').each(function(e){
			ListIdUser.push($(this).val());
			cpt++;
		});
		
		if(cpt > 0){
			var ListIdUserAll = ListIdUser.join('##_##');
			var UrlAddContactList = $('input#UrlAddContactList').val();
			$('.chargementAjax').removeClass('hidden');
			setTimeout(function(){
				$.ajax({
					type : "POST",
					url: UrlAddContactList,
					data : 'UserId='+ListIdUserAll+'&ListName='+id_nom_contact_list+'',
					success: function(reponse){
						$('.chargementAjax').addClass('hidden');
						$('div#creer-list-contact').find('.body-container').html('');
						$('div#creer-list-contact').find('.body-container').html('');
						$('div#creer-list-contact').modal('hide');
						location.reload();
					}
				});
			}, 300);
		}
	});
	
	$(document).on('click', 'a.close-modal', function(){
		setTimeout(function(){
			location.reload();
		}, 300);
	});
	
	$(document).on('click', 'button#valider-edition-contact-list', function(){
		//Verifier les champs obligatoires
		var id_nom_contact_list = $.trim($('input#id_nom_contact_list').val());
		
		var cpt = 0;
		var ListIdUser = new Array;
		$('input.contact-ajout:checked').each(function(e){
			ListIdUser.push($(this).val());
			cpt++;
		});
		
		if(cpt > 0){
			var ListIdUserAll = ListIdUser.join('##_##');
			var UrlEditContactList = $('input#UrlEditContactList').val();
			$('.chargementAjax').removeClass('hidden');
			setTimeout(function(){
				$.ajax({
					type : "POST",
					url: UrlEditContactList,
					data : 'UserId='+ListIdUserAll+'&IdList='+id_nom_contact_list+'',
					success: function(reponse){
						$('.chargementAjax').addClass('hidden');
						$('div#edit-list-contact').find('.body-container').html('');
						$('div#edit-list-contact').find('.body-container').html('');
						$('div#edit-list-contact').modal('hide');
						//location.reload();
					}
				});
			}, 300);
		}
	});
	
	//Incremente/Decremente les nombres des contacts 
	$(document).on('click', 'input.contact-ajout', function(){
		var NbreContactsEnCours = parseInt($('input#id_nbre_contacts_selectionner').val());
		if($(this).is(':checked')){
			NbreContactsEnCours++;
		}else{
			NbreContactsEnCours--;
		}
		$('input#id_nbre_contacts_selectionner').val(NbreContactsEnCours);
	});
	
	$(document).on('click', 'input#checkbox-publish-all', function(){
		if($(this).is(':checked')){
			$('input#id_nbre_contacts_selectionner').val(0);
		}
		
		if($(this).is(':checked')){
			$('input.contact-ajout').each(function(i){
				$(this).prop("checked", false);
				$(this).click();
			});
		}else{
			$('input.contact-ajout').each(function(i){
				$(this).prop("checked", true);
				$(this).click();
			});
		}
	});
	
	//Dupliquer la liste des contacts 
	$(document).on('click', 'a.list-contact-duplicate', function(){
		var IdList = $(this).attr('data-id');
		var NameList = $(this).attr('data-name');
		
		var Html = '';
		Html += '<div class = "row" style = "margin-top:60px;">';
		
		Html += '<div class="col-12 col-md-12 col-lg-2">';
		Html += '<label>nom du nouveau modèle</label>';
		Html += '</div>';

		Html += '<div class="col-12 col-md-10 col-lg-8">';
		Html += '<input id = "id_list_name_dupliquer" value="'+NameList+'" class="liste_name_input" type="text">';
		Html += '<span class="delete-input"></span>';
		Html += '</div>';
		
		Html += '</div>';
		
		Html += '<div class = "row" style = "margin-top:60px;">';
		Html += '<div class = "col-12 col-md-6" style = "text-align:center;"><button id = "oui-dupliquer-contact-list" class="btn-valider valider submit-form" data-id = "'+IdList+'">enregistrer</button></div>';
		Html += '<div class = "col-12 col-md-6" style = "text-align:center;"><button id = "non-dupliquer-contact-list" class="btn-valider valider submit-form" >annuler</button></div>';
		Html += '</div>';
		
		$('div#dupliquer-list-contact').find('.body-container').html('');
		$('div#dupliquer-list-contact').find('.body-container').html(Html);
		$('div#dupliquer-list-contact').modal('show');
		$('input#id_list_name_dupliquer').focus();

		return false;
	});
	
	$(document).on('click', 'button#non-dupliquer-contact-list', function(){
		$('div#dupliquer-list-contact').modal('hide');
	});
	
	$(document).on('click', 'button#oui-dupliquer-contact-list', function(){
		var ListName = $.trim($('input#id_list_name_dupliquer').val());
		var ListId = $(this).attr('data-id');
		
		if(ListName != ''){
			$('.chargementAjax').removeClass('hidden');
			var UrlDupliquerContactList = $('input#UrlDupliquerContactList').val();
			setTimeout(function(){
				$.ajax({
					type : "POST",
					url: UrlDupliquerContactList,
					data : 'ListName='+ListName+'&ListId='+ListId+'',
					success: function(reponse){
						$('.chargementAjax').addClass('hidden');
						$('div#dupliquer-list-contact').modal('hide');
						location.reload();
					}
				});
			}, 300);
		}
	});
	
	//Export la liste des contacts 
	/*
	$(document).on('click', 'a.list-contact-export', function(){
		$('.chargementAjax').removeClass('hidden');
	});
	*/
});

function UpdateWidthPagination(){
	var LargeurTableData = $('div#ListUserContactMailjet_wrapper').width();
	$('div#ListUserContactMailjet_paginate').css('width', ''+LargeurTableData+'px');
}

function OverridePagination(){
	//Modification libellé pagination 'Next' => 'dernier'
	$('li#ListUserContactMailjet_next a').html('dernier');
	
	//Cacher le bouton 'Previews'
	$('li#ListUserContactMailjet_previous').hide();
}

function AddSeparatorPaginate(){
	$('li.paginate_button').each(function(i){
		if(i > 0){
			$('<li class = "paginate_separator"><span>&nbsp;</span></li>').insertAfter($(this));
		}
	});
	$('li.paginate_separator').last().remove();
}