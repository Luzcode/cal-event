(function() {
	// Get url params
	var query_str = window.location.search;
	query_str = query_str.substring(1); // ignore ?

	window.url_params = {};

	var start = 0;
	var end = query_str.length;
	var ampersand_loc = 0;

	while (ampersand_loc != query_str.length) {
		var equal_loc = query_str.indexOf("=", start);
		if (equal_loc == -1) {
			equal_loc = query_str.length;
		}
		ampersand_loc = query_str.indexOf("&", start);
		if (ampersand_loc == -1) {
			ampersand_loc = query_str.length;
		}

		window.url_params[query_str.substring(start, equal_loc)] 
		= query_str.substring(equal_loc + 1, ampersand_loc);

		start = ampersand_loc + 1;
	}

})();

$(function() {
	// automatically start ics generation	
	$.ajax({
		url: 'generate-ics.php',
		type: 'POST',
		dataType: 'json',
		data: {
			method: "start_ics",
			ids: JSON.stringify(window.url_params)
		}
	})
	.done(function(response) {
		if (response.success || true) {
			$(".loader").addClass("no-display");
			$("#file_name").val(response.file_name);
			$(".content-container").fadeIn(1500);
		}
	})
	.fail(function(error) {
		console.log(error);
	})
		
});