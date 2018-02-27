$(document).ready(function(){

$("#statistique-campaign-dialog").on("shown.bs.modal",function(){
$(document).on('click','.clearable .dropdown-it', function(e){
        e.preventDefault(); 
        var donnee = $('input[name=data]').val(); 
        var obj = JSON.parse(donnee);
		console.log(obj)
        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        setTimeout(sendChoice($(this)), 0);
    });

	function sendChoice(){
		var filter = $('.dropdown.filtres').find('button').hasClass('active');
		var donnee = $('input[name=data]').val();
		var url = $('input[name=url]').val();
		var id = $('input[name=id]').val();
		var obj = JSON.parse(donnee);
		console.log(obj)
	        var  data = {};
	        if (filter) {
	            data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
	        }
	        switch (data.status) {
	        	case "tous":
	        	$('.chargementAjax').removeClass('hidden');
	        	$.ajax({
                    url:url,
                    method:'POST',
                    data:{'id': id},
                    dataType: 'json',
                    success:function(data){
                        $('.tableDetail').css('display','none');                     
		                $('#tableBody').children('tr').remove();
		                $('#tableBody2').children('tr').remove();
		                $('.tableDetail2').css('display','block'); 
                        var span1 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background1 "></i></span></div>');
                        var span2 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background2 "></i></span></div>');
                        var span3 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background3 "></i></span></div>');
                        var span4 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background4 "></i></span></div>');
                        var span5 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background5 "></i></span></div>');
                        var span6 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background6 "></i></span></div>');
                        var span7 = $('<div><span class="fa-stack fa-lg"><i  class="fa fa-circle fa-stack-2x icon-background7 "></i></span></div>');
						$.each(JSON.parse(data), function(index, val) {
							if (val.etat == "delivre") {
							$("<tr></tr>").appendTo('.table #tableBody2')
				            .append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "open") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span2.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "click") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span2.html()+"</td><td>"+span3.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "desabo") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span4.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "bloque") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span5.html()+"</td><td>"+""+"</td><td>"+""+"</td>");
							} else if (val.etat == "spam") {
								$("<tr></tr>").appendTo('.table #tableBody2')
		            			.append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span6.html()+"</td><td>"+""+"</td>");
							} else if (val.etat == "erreur") {
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
					if (index.etat == "delivre") {
						newdata.push(index);
					}
					return newdata;
				});
				$('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');  
				var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background1 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
				});
				break;			
				case "opened":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "open") {
						newdata.push(index);
					}
					return newdata;
				});
				$('.tableDetail').css('display','none');                     
               $('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');
                var span1 = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background1 "></i></span></div>');  
				var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background2 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+span1.html()+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
				});
				break;
				case "clicked":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "click") {
						newdata.push(index);
					}
					return newdata;
				});
				console.log(newdata)
				$('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                 $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');  
				var span1 = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background3 "></i></span></div>');
				var span2 = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background2 "></i></span></div>');
				var span3 = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background1 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+span3.html()+"</td><td>"+span2.html()+"</td><td>"+span1.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
				});
				break;
				case "bounce":
				var newdata = [];
	        	var res = obj.map(function(index, elem) {
					if (index.etat == "erreur") {
						newdata.push(index);
					}
					return newdata;
				});
				if (newdata.etat != "undefined") {
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                 $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');  
					var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background7"></i></span></div>');
					$.each(newdata, function(index, val) {
						$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td>");
					});
				} else {
					$('.tableDetail').css('display','none');                     
	                $('#tableBody').children('tr').remove();
	                 $('#tableBody2').children('tr').remove();
	                $('.tableDetail2').css('display','block');
					$("<tr></tr>").appendTo('.table #tableBody2')
			            .append("<td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
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
				$('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                 $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');  
				var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background6 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td>");
				});

				break;
				case "unsub":
				var newdata = [];
				var res = obj.map(function(index, elem) {
					if (index.etat == "unsub") {
						newdata.push(index);
					}
					return newdata;
				});

				$('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                 $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');  
				var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background4 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td>");
				});	        	
				break;
				case "blocked":
				var newdata = [];
				var res = obj.map(function(index, elem) {
					if (index.etat == "bloque") {
						newdata.push(index);
					}
					return newdata;
				});
				$('.tableDetail').css('display','none');                     
                $('#tableBody').children('tr').remove();
                 $('#tableBody2').children('tr').remove();
                $('.tableDetail2').css('display','block');  
				var span = $('<div><span class="fa-stack fa-lg"><i id="erreur" class="fa fa-circle fa-stack-2x icon-background5 "></i></span></div>');
				$.each(newdata, function(index, val) {
					$("<tr></tr>").appendTo('.table #tableBody2')
		            .append("<td>"+ val.emails+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+""+"</td><td>"+span.html()+"</td><td>"+""+"</td><td>"+""+"</td>");
				});
				break;
	        	
			}	        	
	}

	});

	/*function addData(newdata,span){
		$.each(newdata, function(index, val) {
			$("<tr></tr>").appendTo('.table #tableBody2')
            .append("<td>"+ val.email+"</td>")
            .append(span);
		});
	}*/

});

