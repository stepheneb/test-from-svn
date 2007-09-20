/**
 * Select To Rating jQuery plugin
 *
 * (c) 2007 Paul Burney
 *
 * Based on Star Rating by Wil Stuckey
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){ 

	var buildRating = function($obj) {

		var $obj = buildInterface($obj);
		var $stars = $($obj.children('.star'));
		var $cancel = $($obj.children('.cancel'));
		var defaultIndex = $obj.defaultIndex;
		
		// add hover and focus events for stars

		$stars.mouseover(
			function() {
				event.drain();
				event.fill(this);
			}
		);
		
		$stars.mouseout(
			function() {
				event.drain();
				event.reset(this);
			}
		);

		$stars.focus(
			function() {
				event.drain();
				event.fill(this);
			}
		);

		$stars.blur(
			function() {
				event.drain();
				event.reset(this);
			}
		);
		
		$stars.click(
			function() {
				event.drain();
				event.fill(this);
				event.update(this);
				
			}
		);

		// add cancel button events
		$cancel.mouseover(
			function(){
				event.drain();
				$(this).addClass('on');
			}
		);

		$cancel.mouseout(
			function(){
				event.reset();
				$(this).removeClass('on');
			}
		);

		$cancel.focus(
			function(){
				event.drain();
				$(this).addClass('on');
			}
		);

		$cancel.blur(
			function(){
				event.reset();
				$(this).removeClass('on');
			}
		);
	
		$cancel.click(
			function() {
				event.drain();
				event.update(this);
			}
		);

		var event = {

			fill: function(el) { // fill to the current mouse position.
				var index = $stars.index(el) + 1;
				//$stars.children('a').css('width', '100%');
				$stars.slice(0,index).addClass('hover');
			},

			drain: function() { // drain all the stars.
				$stars.filter('.on').removeClass('on');
				$stars.filter('.hover').removeClass('hover');
			},

			reset: function(el) { // Reset the stars to the default index.
				var index = $stars.index(el) + 1;
				$stars.slice(0,defaultIndex).addClass('on');
				//$stars.eq(defaultIndex).addClass('on').children('a').css('width', percent + "%").end().end()
			},
			
			update: function(el) {
				var index = $stars.index(el) + 1;
				$obj.originalSelect[0].options[index].selected = true;
				defaultIndex = index;
				$stars.slice(0,index).addClass('on');
			}
			
		}        

		event.reset();

		return $obj;

	}
	
	
	var buildInterface = function($element){

		var $container = $(document.createElement('span')).attr({"class": "rating-stars"});
		
		var $optionGroup = $element.children('option');
		
		for (var i = 0; i < $optionGroup.size(); i++){
		
			if ($optionGroup[i].value == "0") {
				$div = $('<span class="cancel"><a href="#0" title="Cancel Rating">Cancel Rating</a></span>');
			} else {
				$div = $('<span class="star"><a href="#' + $optionGroup[i].value + '" title="Give it ' + $optionGroup[i].value + '/'+ $optionGroup.size() +'">' + $optionGroup[i].value + '</a></span>');
			}

			$container.append($div[0]);
			
			if (i == $element[0].selectedIndex) {
				$.extend($container, { defaultIndex: i, originalSelect: $element});
			}

		}
	
		$element.after($container);
		
		$element[0].style.visibility = 'hidden';
		
		// returns a jQuery object so that the methods can be added to it

		return $container;

	}
    

	// setup the jQuery function


	$.fn.selectToRating = function(){

		var stack = [];

		this.each(

			function() {

				var ret = buildRating($(this));
				stack = $.merge(ret, stack);
			}
		);

		return $(stack);

	}

	// fix ie6 background flicker problem.

	if ($.browser.msie == true) {
		document.execCommand('BackgroundImageCache', false, true);
	}

})(jQuery)