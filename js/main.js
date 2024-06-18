(function () {
	'use strict'
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	tooltipTriggerList.forEach(function (tooltipTriggerEl) {
		new bootstrap.Tooltip(tooltipTriggerEl)
	})
})()

var $sidebar = $('#sidebar')
	, $w = $(window);
$w.resize(function () {
	var pos = $w.height() < $sidebar.height() ? 'absolute' : 'fixed';
	$sidebar.css({ position: pos });
});