

$("#statistique-campaign-dialog").on("shown.bs.modal",function(){
	
	$(document).on('click','.clearable .dropdown-it', function(e){
	        e.preventDefault(); 

	        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
	        setTimeout(sendChoice($(this)), 0);
	});

	$(".btn-download_csv").on("click",function(e){
        e.preventDefault();
        var id = $('input[name=id]').val();
        var route = Routing.generate('admin_communication_emailing_campaign_download',{"id":id});
        window.location.href = route;
     });

	$(".export-statistique").on("click",function(e){
        e.preventDefault();
        var id = $('input[name=id]').val();
        var titre = $('input[name=titre]').val();
        var route = Routing.generate("admin_communication_emailing_campaign_exports",{'id':id,'title':titre });
        window.location.href = route;
     });

});

	function sendChoice(){
		var filter = $('.dropdown.filtres').find('button').hasClass('active');
		var donnee = $('input[name=data]').val();
		var url = $('input[name=url]').val();
		var id = $('input[name=id]').val();
		var obj = JSON.parse(donnee);
	        var  data = {};
	        if (filter) {
	            data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
	        }
        $(".button-csv").css('display','none');    
        $(".button-excel").css('display','block');                      
        $(".btn-download1").on("click",function(e){
	        e.preventDefault(); 
	            var id = $('input[name=id]').val();
	            var route = Routing.generate('admin_communication_emailing_campaign_download',{"id":id,"status":data.status});
	            window.location.href = route;
        });
	        switch (data.status) {
	        	case "tous":
	        	$('.chargementAjax').removeClass('hidden');
	        	$.ajax({
                    url:url,
                    method:'POST',
                    data:{'id': id},
                    dataType: 'json',
                    success:function(data){
                    	$('.navigation').css('display','block');
                        $('.tableDetail').css('display','none');                     
		                $('#tableBody').children('tr').remove();
		                $('#tableBody2').children('tr').remove();
		                $('.tableDetail2').css('display','block'); 
		                $('.tableEmpty3').css('display','none'); 
                        var span1 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background1 "></i></span></div>');
                        var span2 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background2 "></i></span></div>');
                        var span3 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background3 "></i></span></div>');
                        var span4 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background4 "></i></span></div>');
                        var span5 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background5 "></i></span></div>');
                        var span6 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background6 "></i></span></div>');
                        var span7 = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background7 "></i></span></div>');
						$.each(JSON.parse(data), function(index, val) {
							if (val.etat == "sent") {
							$("<tr></tr>").appendTo('.table #tableBody2')
				            .append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "opened") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span2.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "clicked") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span2.html()+"</td><td>"+span3.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "unsub") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span4.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "blocked") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span5.html()+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "spam") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span6.html()+"</td><td>"+""+"</td>");
							} else if (val.etat == "bounce") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span7.html()+"</td>");
							}
							
						});
                    },
                complete: function(){
                	$('.chargementAjax').addClass('hidden');
            	}
                });

	        	break;
				case "delivred":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "sent" || index.etat == "clicked" || index.etat == "opened" ) {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length > 0 ) {
					console.log(newdata)
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');  
	                $('.tableEmpty3').css('display','none'); 
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background1 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
					});
				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}
				break;			
				case "opened":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "opened" || index.etat == "clicked") {
						newdata.push(index);
					}
					return newdata;
				});
	        	if (newdata.length > 0 ) {
		        	$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');
	                $('.tableEmpty3').css('display','none'); 
	                var span1 = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background1 "></i></span></div>');  
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background2 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
					});
	        	} else {
	        		$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');
	        	}
				
				break;
				case "clicked":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "clicked") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length > 0) {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                 $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block'); 
	                $('.tableEmpty3').css('display','none');  
					var span1 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-1x icon-background3 "></i></span></div>');
					var span2 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-1x icon-background2 "></i></span></div>');
					var span3 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-1x icon-background1 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+span3.html()+"</td><td>"+span2.html()+"</td><td>"+span1.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
					});

				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}
				
				break;
				case "bounce":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "bounce") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length > 0) {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                 $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');
	                 $('.tableEmpty3').css('display','none');   
					var span = $('<div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-1x icon-background7"></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td>");
					});
				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}
				
				break;
				case "spam":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "spam") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length > 0) {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block'); 
	                $('.tableEmpty3').css('display','none');  
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background6 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td>");
					});
				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}
				break;
				case "unsub":
				var newdata = [];
				var res = obj.map(function(index, elem) {
					if (index.etat == "unsub") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length > 0) {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block'); 
	                $('.tableEmpty3').css('display','none');  
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background4 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
					});	  
				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}
				      	
				break;
				case "blocked":
				var newdata = [];
				var res = obj.map(function(index, elem) {
					if (index.etat == "blocked") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.length >  0 ) {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');  
	                $('.tableEmpty3').css('display','none'); 
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-1x icon-background5 "></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td>");
					});
				} else {
					$('.navigation').css('display','none');
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','none');
	                $('.tableEmpty3').css('display','block');  
				}				
				break;	        	
			}	        	
	}

	$(document).on('click','.page-link',function(e){
		e.preventDefault(); 
        var url = $(this).attr('href');
        $('.chargementAjax').removeClass('hidden');
        $.ajax({
            type: 'GET',
            url: url,
            success: function(data){
            	console.log(data)
                $('#statistique-campaign-dialog').find('.modal-body-container').html(data.content);
                $('#statistique-campaign-dialog').find('.general-message').html('');
            },
            statusCode: {
                404: function(){
                    $('#statistique-campaign-dialog').find('.general-message').html('Page non trouvée');
                },
                500: function(){
                    $('#statistique-campaign-dialog').find('.general-message').html('Erreur interne');
                }
            },complete: function(){
                $('#statistique-campaign-dialog').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
            
        });

    });	



