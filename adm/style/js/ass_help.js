/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
jQuery(function($) {
	let studio = {
		buttons: {
			close: $('.ass-help-close'),
			start: $('.ass-help-start'),
			next: $('.ass-help-next'),
			prev: $('.ass-help-prev')
		},
		classes: {
			active: 'ass-help-active',
			body: 'ass-help-body'
		},
		flexbox: $('.ass-help-flexbox'),
		toolbox: $('.ass-help-toolbox'),
		wrapper: $('#darkenwrapper'),
		index: 0
	};

	studio.init = function() {
		this.wrapper.append(this.toolbox);

		this.items = this.flexbox.children();

		this.buttons.start.on('click', function() { studio.toggle(true); });
		this.buttons.close.on('click', function() { studio.toggle(false); });
		this.buttons.next.on('click', function() { studio.navigate(1); });
		this.buttons.prev.on('click', function() { studio.navigate(-1); });
	};

	studio.init();

	studio.toggle = function(show) {
		$('body').toggleClass(studio.classes.body);

		studio.toolbox.toggle(show);
		studio.wrapper.toggle(show);

		show ? studio.select() : studio.deselect();
	};

	studio.navigate = function(direction) {
		let length = studio.items.length,
			index = studio.index + direction;

		if (index >= 0 && index < length) {
			studio.index = index;

			studio.select();

			studio.scrollToSlide();
		}

		studio.buttons.prev.toggle(studio.index !== 0);
		studio.buttons.next.toggle(studio.index !== (length - 1));
	};

	studio.select = function() {
		let $item = $(studio.items.get(studio.index)),
			$option = $(`label[for="${$item.data('id')}"]`).parents('dl');

		studio.deselect();

		$option.addClass(studio.classes.active).css('max-height', $(window).outerHeight() - (400 + 64 + 64));

		studio.scrollToTop($option);
	};

	studio.deselect = function() {
		$(`.${studio.classes.active}`).removeClass(studio.classes.active).css('max-height', '');
	};

	studio.scrollToSlide = function() {
		studio.flexbox.animate({
			scrollLeft: studio.index * $(window).outerWidth()
		});
	};

	studio.scrollToTop = function($element) {
		$('html, body').stop(true).animate({
			scrollTop: $element.offset().top - 64
		}, 1000);
	};
});
