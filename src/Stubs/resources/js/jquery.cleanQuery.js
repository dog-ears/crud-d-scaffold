/*
 * jQuery cleanQuery 2013-03-23
 * Authored by guimihanui
 * Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

(function($) {
	function cleanQuery(query) {
		var arr = [];
		$.each(query.split('&'), function(i, param) {
			if (param.split('=')[1]) { arr.push(param); }
		});
		return arr.join('&');
	}
	
	$.fn.cleanQuery = function() {
		this.on('submit', function(event) {
			event.preventDefault();
			
			var query = cleanQuery($(this).serialize());
			location.href = this.action + '?' + query;
		});
		
		return this;
	};
})(jQuery);
