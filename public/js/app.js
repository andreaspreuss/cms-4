(function ($) {
	return $(document).ready(function () {
		$('pre > code').parent().addClass('prettyprint');
		$('article table').addClass('table table-striped table-bordered');
	});
})(jQuery);