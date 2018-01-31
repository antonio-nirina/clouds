$(document).ready(function(){
	
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
						searching: false,
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
						location.reload();
					}
				});
			}, 300);
		}
	});
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