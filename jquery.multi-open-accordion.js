/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 2.0
Purpose: To allow NoMoreCaptchas Plugin and Accordion Slider functions to work harmoniously
*/
(function($){
 	$.fn.extend({ 
		//pass the options variable to the function
 		multiAccordion: function(options) {
			// TODO: no defaults yet
			var defaults = {
			}
			var options =  $.extend(defaults, options);
    		return this.each(function() {
				var $this = $(this);
				var $h3 = $this.children('h3');
				var $div = $this.children('div');
				$this.addClass('ui-accordion ui-widget ui-helper-reset ui-accordion-icons');
				$h3.each(function(){
					$(this).addClass('ui-accordion-header ui-helper-reset ui-state-default ui-corner-all').prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
				});
				$this.children('div').each(function(){
					$(this).addClass('ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom');
				});
				$h3.click(function(){
					var $this = $(this);
					var $span = $this.children('span.ui-icon');
					var $div = $this.next();
					
					if ($this.hasClass('ui-state-default')) {
						$this.removeClass('ui-state-default ui-corner-all').addClass('ui-state-active ui-corner-top');
						$span.removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
						$div.slideDown('fast', function(){
							$div.addClass('ui-accordion-content-active');
						});
					} else {
						$this.removeClass('ui-state-active ui-corner-top').addClass('ui-state-default ui-corner-all');
						$span.removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
						$div.slideUp('fast', function(){
							$div.removeClass('ui-accordion-content-active');
						});
					}
				});
				$h3.hover(
					function() {
						$(this).addClass('ui-state-hover');
					}, function() {
						$(this).removeClass('ui-state-hover');
					}
				);
    		});
    	}
	});
})(jQuery);