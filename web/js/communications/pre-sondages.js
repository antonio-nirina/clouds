$(document).ready(function(){ 

$('section.page').mouseover(function(){
		$(this).removeClass('inactive');
		$(this).addClass('active');
		$(".trie-sondage").addClass('preSondageQuiz');
		$(this).find('div.block-active-hover').show();
	}).mouseout(function(){
		$(this).removeClass('active');
		$(this).addClass('inactive');
		$(".trie-sondage").removeClass('preSondageQuiz');
		$(this).find('div.block-active-hover').hide();
	});

	$(".radioChecked").on("click",function(){
		$(this).removeClass('notChecked');
		$(this).addClass('checked');
		//$(".quizSelected").css("display","block");
		$('.delete-input').css("display","block");
		$(".checked-quiz").css("display","flex");
	});

	$(".delete-input").on("click",function(){
		$(".radioChecked").removeClass("checked");
		$(".radioChecked").addClass('notChecked');
		//$(".quizSelected").css("display","none");
		$(".checked-quiz").css("display","none");
	});
});