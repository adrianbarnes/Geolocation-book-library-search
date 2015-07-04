$('document').ready(function(){
	$('.details, #eachBook').hover(function(){
		$('#eachBook').css('outline', 'rgb(27, 161, 226) solid 1px');
	},function(){
		$('#eachBook').css('outline', '0');
	});


	$('.details, #eachBook').mousedown(function(){
		$('#eachBook').addClass('selected').css();
	});
	$('.details, #eachBook').mouseup(function(){
		$('#eachBook').removeClass('selected').css();
	});

	/*$('.btn-search').click(function(){{
		alert('femi');
	}});*/
});