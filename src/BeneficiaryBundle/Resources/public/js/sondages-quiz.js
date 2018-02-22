$(document).ready(function(){
	
	$(document).on('click', 'span.icone-edit-participant', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var Id = ArrayAttrId[3];
		$('div#content-block-detail-sondages-quiz-'+Id+'').show();
		$('div#content-block-resume-sondages-quiz-'+Id+'').hide();
	});
	
	$(document).on('click', 'span.lib-cacher', function(){
		var AttrId = $(this).attr('id');
		var ArrayAttrId = new Array;
		ArrayAttrId = AttrId.split('-');
		var Id = ArrayAttrId[2];
		$('div#content-block-detail-sondages-quiz-'+Id+'').hide();
		$('div#content-block-resume-sondages-quiz-'+Id+'').show();
	});
});