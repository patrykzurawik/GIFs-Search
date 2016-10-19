$(window).scroll(function()
{
	if($(this).scrollTop()>450) $('#scrollUpButton').fadeIn();
	else
	$('#scrollUpButton').fadeOut();
});

jQuery(function($)
{
	$.scrollTo(0);

	$('#scrollUpButton').click(function()
	{
		$.scrollTo($('body'), 1000);
		$('#scrollUpButton').fadeOut();
	}
	);
});
	

