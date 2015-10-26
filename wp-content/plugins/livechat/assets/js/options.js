/*
 * Screets Chat
 * Options page scripts
 *
 * Copyright (c) 2013 Screets
 */

(function ($) {
	
	$(document).ready(function () {
		
		/**
		 * Color inputs
		 */
		$('.sc-chat-color-field').spectrum({
			preferredFormat 		: "hex",
			showPalette 			: true,
			showInput				: true,
			showSelectionPalette 	: true,
			palette 				: ['#bf3723', '#3a99d1', '#ffffff', '#222222'],
			localStorageKey 		: "spectrum.homepage"
		});
		
	});
	
}
	(window.jQuery || window.Zepto));
