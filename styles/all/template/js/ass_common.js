jQuery(function($) {
	let studio = {
		body: '.aps-body',
		link: '[data-shop-link]',
		ajax: {
			alert: $('#phpbb_alert'),
			data: $('#darkenwrapper'),
			spinner: $('#studio_spinner'),
			timer: null
		},
		carousel: {
			items: $('[data-shop-carousel]'),
			item: $('[data-shop-carousel-data]'),
			data: {
				dotsClass: 'shop-carousel-dots',
				nextArrow: '<button class="aps-button-blue shop-carousel-next" type="button"><i class="icon fa-chevron-right fa-fw"></i></button>',
				prevArrow: '<button class="aps-button-blue shop-carousel-prev" type="button"><i class="icon fa-chevron-left fa-fw"></i></button>',
				draggable: true
			}
		},
		inventory: {
			card: $('.shop-inventory-card'),
			info: $('.shop-inventory-info'),
			items: $('.shop-inventory-list .shop-inventory-panel'),
			proxy: $('.shop-inventory-proxy'),
			classes: {
				card: '.shop-inventory-card',
				cardActive: 'shop-inventory-card-active',
				cardPaused: 'shop-inventory-card-paused',
				item: '.shop-inventory-item',
				itemAnimate: 'shop-inventory-item-animate',
				itemCount: '.shop-inventory-item-count',
				itemTime: '.shop-inventory-item-time',
				link: '.shop-inventory-link',
				panel: '.shop-inventory-panel',
				refund: '.shop-inventory-refund',
				row: '.shop-inventory-row'
			}
		},
		sketch: {
			dots: [],
			pool: [],
			limit: 280,
			timer: null,
			timeout: 1500,
			colours: ['#12a3eb', '#12a3eb', '#2172b8', '#18a39b', '#82c545', '#f8b739', '#f06045', '#ed2861', '#c12680', '#5d3191'],
			drawing: null,
			overlay: $('.shop-inventory-overlay'),
		}
	};

	/**
	 * Set up carousel items.
	 *
	 * @return {void}
	 */
	studio.carousel.setup = function() {
		let none = false,
			data = studio.carousel.item.data();

		if (data) {
			none = !data.dots && !data.arrows;

			delete data['shopCarouselData'];
		}

		// Register all carousel items
		studio.carousel.items.each(function() {
			let $footer = $(this).parent().next('.aps-panel-footer');

			let slickData = $.extend({}, data, studio.carousel.data);

			if ($(this).data('shop-carousel') === 'images') {
				$.extend(slickData, {
					arrows: false,
					asNavFor: $footer,
					dots: false,
					fade: true,
					slidesToScroll: 1,
					slidesToShow: 1
				});
			} else {
				$.extend(slickData, {
					appendDots: $footer,
					appendArrows: $footer
				});
			}

			// Initiate a Slick instance
			$(this).slick(slickData);

			if ($(this).data('shop-carousel') === 'images') {
				$footer.slick({
					arrows: false,
					asNavFor: this,
					centerMode: true,
					dots: false,
					focusOnSelect: true,
					slidesToScroll: 1,
					slidesToShow: 3
				});
			}

			// If there are no navigation arrows or dots, remove the footer
			if (none) {
				$footer.remove();
			}
		});

		/**
		 * Remove the carousel data element.
		 */
		studio.carousel.item.remove();
	};

	studio.carousel.setup();

	/**
	 * Show the loader for Shop AJAX requests.
	 *
	 * @return {void}
	 */
	studio.ajax.loading = function() {
		if (!studio.ajax.spinner.is(':visible')) {
			studio.ajax.spinner.fadeIn(100);

			studio.ajax.clear();
			studio.ajax.timer = setTimeout(studio.ajax.timeout, 60000)
		}
	};

	/**
	 * Show the time out message for Shop AJAX requests.
	 *
	 * @return {void}
	 */
	studio.ajax.timeout	= function() {
		studio.ajax.message({
			title: studio.ajax.alert.attr('data-l-err'),
			html: studio.ajax.alert.attr('data-l-timeout-processing-req'),
			type: 'error'
		});
	};

	/**
	 * Get the localised message for Shop AJAX request errors.
	 *
	 * @param  {string}		attribute
	 * @return {string}
	 */
	studio.ajax.data.get = function(attribute) {
		return $(this).attr(`data-ajax-error-${attribute}`);
	};

	/**
	 * Clear the timer for Shop AJAX requests.
	 *
	 * @return {void}
	 */
	studio.ajax.clear = function() {
		if (studio.ajax.timer !== null) {
			clearTimeout(studio.ajax.timer);

			studio.ajax.timer = null;
		}
	};

	/**
	 * The error handler for Shop AJAX requests.
	 *
	 * @param  {Object}		jqXHR
	 * @param  {Object}		jqXHR.responseJSON
	 * @param  {String}		jqXHR.responseText
	 * @param  {string}		textStatus
	 * @param  {string}		errorThrown
	 * @return {void}
	 */
	studio.ajax.error = function(jqXHR, textStatus, errorThrown) {
		if (typeof console !== 'undefined' && console.log) {
			console.log(`AJAX error. status: ${textStatus}, message: ${errorThrown}`);
		}

		studio.ajax.clear();

		let responseText, errorText = '';

		try {
			responseText = JSON.parse(jqXHR.responseText);
			responseText = responseText.message;
		} catch (e) {}

		if (typeof responseText === 'string' && responseText.length > 0) {
			errorText = responseText;
		} else if (typeof errorThrown === 'string' && errorThrown.length > 0) {
			errorText = errorThrown;
		} else {
			errorText = studio.ajax.data.get(`text-${textStatus}`);

			if (typeof errorText !== 'string' || !errorText.length) {
				errorText = studio.ajax.data.get('text');
			}
		}

		studio.ajax.message({
			title: typeof jqXHR.responseJSON !== 'undefined' ? jqXHR.responseJSON.title : studio.ajax.data.get('title', false),
			html: errorText,
			type: 'error'
		});
	};

	/**
	 * The success handler for Shop AJAX requests.
	 *
	 * @param {Object}		r
	 * @param {function}	callback
	 * @return {void}
	 */
	studio.ajax.success = function(r, callback) {
		studio.ajax.clear();

		/**
		 * @param {string}	r.MESSAGE_BODY		The message template body
		 * @param {string}	r.YES_VALUE			The "yes" language string
		 * @param {string}	r.S_CONFIRM_ACTION	The confirm_box() action
		 * @param {string}	r.S_HIDDEN_FIELDS	The confirm_box() hidden fields
		 */

		// If there is no confirm action (not a confirm_box())
		if (typeof r.S_CONFIRM_ACTION === 'undefined') {
			// Call the callback
			if (typeof phpbb.ajaxCallbacks[callback] === 'function') {
				phpbb.ajaxCallbacks[callback].call(this, r);
			}

			// Show the message
			studio.ajax.message({
				title: r.MESSAGE_TITLE,
				html: r.MESSAGE_TEXT
			});
		} else {
			// Show the confirm box
			studio.ajax.message({
				title: r.MESSAGE_TITLE,
				html: r.MESSAGE_BODY,
				type: 'question',
				showCancelButton: true,
				confirmButtonText: r.YES_VALUE
			}).then((result) => {
				// A button was pressed, was it the Confirm button?
				if (result.value) {
					let data = $('form', swal.getContent()).serialize();

					data = data ? `${data}&` : '';
					data = data + $(`<form>${r.S_HIDDEN_FIELDS}</form>`).serialize() + `&confirm=${r.YES_VALUE}`;

					// Make the request
					studio.ajax.request(r.S_CONFIRM_ACTION, callback, 'POST', data);
				}
			});
		}
	};

	/**
	 * Show a message for Shop AJAX requests.
	 *
	 * @param  {Object}		options
	 * @return {swal}
	 */
	studio.ajax.message = function(options) {
		return swal.fire($.extend({
			type: 'success',
			cancelButtonClass: 'aps-button-alert aps-button-red',
			confirmButtonClass: 'aps-button-alert aps-button-green shop-button-active',
			showCloseButton: true,
			buttonsStyling: false,
		}, options));
	};

	/**
	 * Make a Shop AJAX request.
	 *
	 * @param  {string}		url
	 * @param  {function}	callback
	 * @param  {string=}	type
	 * @param  {Object=}	data
	 * @return {void}
	 */
	studio.ajax.request = function(url, callback, type, data) {
		// Start the loading function
		studio.ajax.loading();

		// Make the request
		let request = $.ajax({
			url: url,
			type: type || 'GET',
			data: data || '',
			error: studio.ajax.error,
			success: function(r) {
				studio.ajax.success(r, callback)
			},
		});

		// No matter what the request returns, always stop the spinner
		request.always(function() {
			if (studio.ajax.spinner.is(':visible')){
				studio.ajax.spinner.fadeOut(100);
			}
		});
	};

	/**
	 * Register all shop links for Shop AJAX requests.
	 */
	$(studio.body).on('click', studio.link, function(e) {
		studio.ajax.request($(this).attr('href'), $(this).data('shop-link'));

		e.preventDefault();
	});

	/**
	 * Remove an inventory item.
	 *
	 * @param  {Object}	r
	 * @return {void}
	 */
	studio.inventory.remove = function(r) {
		let $item		= $(`[data-shop-item="${r.id}"]`),
			$section	= $item.parents(studio.inventory.classes.panel),
			$column		= $section.parent('.aps-col'),
			$row		= $column.parent(studio.inventory.classes.row);

		$item.remove();
		$section.remove();
		$column.remove();

		if ($row.children().length === 0) {
			$row.remove();
		}
	};

	/**
	 * Add AJAX callback for purchasing a Shop item.
	 *
	 * @param {Object}		r			The response object
	 * @param {string}		r.points	The new points value
	 * @param {number|bool}	r.stock		The new stock value
	 * @return {void}
	 */
	phpbb.addAjaxCallback('shop_purchase', function(r) {
		$('.aps-menu > .aps-list-right > :first-child > span').text(r.points);

		if (r.stock !== false) {
			let $item = $(`[data-shop-item="${r.id}"]`);

			$item.find('.shop-item-stock').text(r.stock);

			if (r.stock === 0) {
				$item.find('[data-shop-link="shop_purchase"]').remove();
			}
		}
	});

	/**
	 * Add AJAX callback for deleting a Shop item.
	 *
	 * @return {void}
	 */
	phpbb.addAjaxCallback('shop_inventory_delete', studio.inventory.remove);

	/**
	 * Add AJAX callback for using a Shop item.
	 *
	 * @param  {Object}		r					The response object
	 * @param  {number}		r.id				The item identifier
	 * @param  {Object}		r.data				The item data
	 * @param  {number}		r.data.use_count	The item use count
	 * @param  {string}		r.data.use_time		The item use time
	 * @param  {bool}		r.delete			The delete indicator
	 * @param  {bool}		r.success			The success indicator
	 * @param  {string}		r.file				A window location for downloading a file
	 * @return {void}
	 */
	phpbb.addAjaxCallback('shop_inventory_use', function(r) {
		if (r.success) {
			if (r.delete) {
				studio.inventory.remove(r);
			} else {
				let $item = $(`[data-shop-item="${r.id}"]`),
					$refund = $item.find(studio.inventory.classes.refund).remove();

				$refund.remove();
				$refund.next().removeClass('s2').addClass('s4');

				if (r.data) {
					$item.find(studio.inventory.classes.itemCount).text(r.data['use_count']);
					$item.find(studio.inventory.classes.itemTime).text(r.data['use_time']).parent().show();
				}

				if (r.limit) {
					let notice = '<div class="aps-col s12 shop-no-pad"><div class="aps-button-red shop-button-active shop-cursor-normal">' + r.limit + '</div></div>';

					$item.find('> .aps-row > :first-child').after(notice);

					$item.find('[data-shop-link="shop_inventory_use"]').remove();
				}
			}

			if (r.file) {
				setTimeout(function() {
					window.location = r.file;
				}, 1500);
			}
		}
	});

	/**
	 * Register the inventory items as draggables.
	 *
	 * @return {void}
	 */
	studio.inventory.items.each(function() {
		$(this).draggable({
			appendTo: studio.body,
			containment: studio.body,
			helper: 'clone',
			scope: 'inventory',
			snap: studio.inventory.classes.card,
			snapMode: 'inner',
			start: function(e, ui) {
				studio.inventory.info.children().not(studio.inventory.proxy).remove();

				$(this).draggable('instance').offset.click = {
					left: Math.floor(ui.helper.width() / 2),
					top: Math.floor(ui.helper.height() / 2)
				};

				studio.sketch.create(ui.helper);
			},
			stop: function(e, ui) {
				studio.sketch.timer = setTimeout(studio.sketch.destroy, studio.sketch.timeout);
			}
		});
	});

	/**
	 * Register the inventory 'placeholder card' as droppable.
	 *
	 * @return {void}
	 */
	studio.inventory.card.each(function() {
		$(this).droppable({
			activeClass: studio.inventory.classes.cardActive,
			hoverClass: studio.inventory.classes.cardPaused,
			scope: 'inventory',
			drop: function(e, ui) {
				let clone = ui.draggable.clone(false),
					link = clone.find(studio.inventory.classes.link),
					item = clone.find(studio.inventory.classes.item);

				studio.inventory.info.append(clone);

				clone.removeClass('ui-draggable ui-draggable-handle');

				link.remove();

				item.addClass(studio.inventory.classes.itemAnimate);
			},
		})
	});

	studio.sketch.dot = function(x, y, radius) {
		this.init(x, y, radius);
	};

	studio.sketch.dot.prototype = {
		init: function(x, y, radius) {
			this.alive = true;

			this.radius = radius || 10;
			this.wander = 0.15;
			this.theta = random(TWO_PI);
			this.drag = 0.92;
			this.color = '#12a3eb';

			this.x = x || 0.0;
			this.y = y || 0.0;

			this.vx = 0.0;
			this.vy = 0.0;
		},

		move: function() {
			this.x += this.vx;
			this.y += this.vy;

			this.vx *= this.drag;
			this.vy *= this.drag;

			this.theta += random(-0.5, 0.5) * this.wander;
			this.vx += Math.sin(this.theta) * 0.1;
			this.vy += Math.cos(this.theta) * 0.1;

			this.radius *= 0.96;
			this.alive = this.radius > 0.5;
		},

		draw: function( ctx ) {
			ctx.beginPath();
			ctx.arc(this.x, this.y, this.radius, 0, TWO_PI);
			ctx.fillStyle = this.color;
			ctx.fill();
		}
	};

	studio.sketch.create = function(helper) {
		studio.sketch.clear();

		studio.sketch.drawing = Sketch.create({
			container: studio.sketch.overlay[0],
			eventTarget: helper[0],
			retina: 'auto'
		});

		studio.sketch.drawing.spawn = function(x, y) {
			let dot, theta, force;

			if (studio.sketch.dots.length >= studio.sketch.limit) {
				studio.sketch.pool.push(studio.sketch.dots.shift());
			}

			dot = studio.sketch.pool.length ? studio.sketch.pool.pop() : new studio.sketch.dot();
			dot.init( x, y, random(5, 40));

			dot.wander = random(0.5, 2.0);
			dot.color = random(studio.sketch.colours);
			dot.drag = random(0.9, 0.99);

			theta = random(TWO_PI);
			force = random(2, 8);

			dot.vx = Math.sin(theta) * force;
			dot.vy = Math.cos(theta) * force;

			studio.sketch.dots.push(dot);
		};

		studio.sketch.drawing.update = function() {
			let i, dot;

			for (i = studio.sketch.dots.length - 1; i >= 0; i--) {
				dot = studio.sketch.dots[i];

				if (dot.alive) {
					dot.move();
				} else {
					studio.sketch.pool.push(studio.sketch.dots.splice(i, 1)[0]);
				}
			}
		};

		studio.sketch.drawing.draw = function() {
			studio.sketch.drawing.globalCompositeOperation = 'lighter';

			for (let i = studio.sketch.dots.length - 1; i >= 0; i--) {
				studio.sketch.dots[i].draw(studio.sketch.drawing);
			}
		};

		studio.sketch.drawing.mousemove = function() {
			let touch, max, i, j, n;

			for (i = 0, n = studio.sketch.drawing.touches.length; i < n; i++) {
				touch = studio.sketch.drawing.touches[i];
				max = random(1, 4);

				for (j = 0; j < max; j++) {
					studio.sketch.drawing.spawn(touch.x, touch.y);
				}
			}
		};
	};

	studio.sketch.clear = function() {
		if (studio.sketch.timer !== null) {
			clearTimeout(studio.sketch.timer);

			studio.sketch.timer = null;
		}

		studio.sketch.destroy();
	};

	studio.sketch.destroy = function() {
		if (studio.sketch.drawing !== null) {
			studio.sketch.drawing.clear();
			studio.sketch.drawing.destroy();

			studio.sketch.drawing = null;
		}
	};
});
