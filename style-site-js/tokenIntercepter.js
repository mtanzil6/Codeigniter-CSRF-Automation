$(function(){
	// token names declaration
	var $csrfCookieName = '_csrfCookieName';
	var $csrfTokenName = '_csrfToken';
	
	// add csrf token to each ajax request
	$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		if (originalOptions.type !== 'POST' || options.type !== 'POST') 
		{
	        return;
	    }
		var token;
		if (!options.crossDomain) 
		{
		    token = $.cookie($csrfCookieName);
		    if (token) 
		    {
			  var data = {};
			  data[$csrfTokenName] = token;
			  options.data = $.param($.extend({}, originalOptions.data, data));
		    }
		}
	});
	
	// modify form csrf token on Form submit
	$("form").on("submit", function(){
		var newCsrfToken = $.cookie($csrfCookieName);
		$("input[name="+$csrfTokenName+"]").val(newCsrfToken);
	});
	
});