$(function(){
  setInterval(function(){
	 $(".slideshow ul").animate({marginLeft:-750},800,function(){
		$(this).css({marginLeft:0}).find("li:last").after($(this).find("li:first"));
	 })
  }, 3500);
});

$(document).ready(function(){
	var HeightContentCenter = $('div.content-center').outerHeight();
	$('div.content-sep-date-ligne').css('height', HeightContentCenter+'px');
});