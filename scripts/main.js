$(document).ready(function()
{
	var searchBarY = $('#searchBar').offset().top;
	var stickySearchBar = function()
	{
		var scrollY = $(window).scrollTop();
		if (scrollY > searchBarY) $('#searchBar').addClass('sticky');
		else $('#searchBar').removeClass('sticky');
	};
	
	stickySearchBar();

	$(window).scroll(function()
	{
		stickySearchBar();
	}
	);
});

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
	

