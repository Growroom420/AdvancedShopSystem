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
		iconPicker: $('.aps-icon-picker'),
		imageAddRow: $('#add_image_row'),
		sortRows: $('[data-studio-sortable]'),
		selects: $('[data-studio-select]'),
		sliders: $('.shop-slider'),
		panels: $('[data-studio-panel]'),
		dates: {
			format: 'DD/MM/YYYY HH:mm',
			items: $('.shop-date'),
			data: $('[data-shop-date]').data()
		},
		types: {
			template: $('#type_template'),
			select: $('#type')
		},
		title: $('#title'),
		slug: $('#slug')
	};

	if (studio.iconPicker.length) {
		studio.iconPicker.iconpicker({
			collision: true,
			placement: 'bottomRight',
			component: '.aps-icon-picker + i',
			locale: {
				cancelLabel: 'Clear',
				format: studio.dates.format
			}
		});
	}

	if (studio.selects.length) {
		studio.selects.select2({
			closeOnSelect: false,
		});
	}

	/**
	 * Load the requested Item type template.
	 *
	 * @return {void}
	 */
	studio.types.select.on('change', function() {
		$.ajax({
			url: studio.types.template.data('shop-url'),
			type: 'POST',
			data: {
				type: $(this).val()
			},
			success: function(r) {
				if (r.success) {
					studio.ajaxifyFiles(studio.types.template.html(r.body));
				} else if (r.error) {
					phpbb.alert(r.MESSAGE_TITLE, r.MESSAGE_TEXT);
				}
			}
		})
	});

	/**
	 * Create date range pickers.
	 *
	 * @return {void}
	 */
	studio.dates.items.each(function() {
		let $input = $(this).find('input'),
			$start = $input.first(),
			$until = $input.last();

		/**
		 * Localised language strings.
		 *
		 * @param studio.dates.data.sun
		 * @param studio.dates.data.mon
		 * @param studio.dates.data.tue
		 * @param studio.dates.data.wed
		 * @param studio.dates.data.thu
		 * @param studio.dates.data.fri
		 * @param studio.dates.data.sat
		 * @param studio.dates.data.january,
		 * @param studio.dates.data.february,
		 * @param studio.dates.data.march,
		 * @param studio.dates.data.april,
		 * @param studio.dates.data.may,
		 * @param studio.dates.data.june,
		 * @param studio.dates.data.july,
		 * @param studio.dates.data.august,
		 * @param studio.dates.data.september,
		 * @param studio.dates.data.october,
		 * @param studio.dates.data.november,
		 * @param studio.dates.data.december
		 */
		$start.daterangepicker({
			startDate: $start.val() || false,
			endDate: $until.val() || false,
			timePicker: true,
			timePicker24Hour: true,
			autoUpdateInput: false,
			showWeekNumbers: true,
			applyButtonClasses: 'aps-button-green',
			cancelButtonClasses: 'aps-button-red',
			locale: {
				applyLabel: studio.dates.data.apply,
				cancelLabel: studio.dates.data.clear,
				format: studio.dates.format,
				firstDay: 1,
				daysOfWeek: [
					studio.dates.data.sun,
					studio.dates.data.mon,
					studio.dates.data.tue,
					studio.dates.data.wed,
					studio.dates.data.thu,
					studio.dates.data.fri,
					studio.dates.data.sat
				],
				monthNames: [
					studio.dates.data.january,
					studio.dates.data.february,
					studio.dates.data.march,
					studio.dates.data.april,
					studio.dates.data.may,
					studio.dates.data.june,
					studio.dates.data.july,
					studio.dates.data.august,
					studio.dates.data.september,
					studio.dates.data.october,
					studio.dates.data.november,
					studio.dates.data.december
				],
			}
		}).on('apply.daterangepicker', function(e, picker) {
			$start.val(picker.startDate.format(studio.dates.format));
			$until.val(picker.endDate.format(studio.dates.format));
		}).on('cancel.daterangepicker', function() {
			$start.val('');
			$until.val('');
		});

		$until.on('click', function() { $start.data('daterangepicker').show(); });
	});

	/**
	 * Automatically create a slug from a title/
	 *
	 * @return {void}
	 */
	if (studio.title.length && studio.slug.length) {
		studio.title.on('blur', function() {
			let title = $(this).val();

			studio.slug.val(function(event, slug) {
				return (slug) ? slug : title.toLowerCase().replace(/[^a-z0-9-_\s]/gi, '').trim().replace(/[\s]+/g, '-');
			});
		});
	}

	/**
	 * Make the category and item tables sortables.
	 *
	 * @return {void}
	 */
	if (studio.sortRows.length) {
		studio.sortRows.sortable({
			axis: 'y',
			containment: $(this).selector,
			cursor: 'move',
			delay: 150,
			handle: '.aps-button-blue',
			forcePlaceholderSize: true,
			placeholder: 'panel',
			tolerance: 'pointer',
			update: function(e, ui) {
				// On update (when rows changes position), save the order
				$.ajax({
					url: $(this).parents('form').attr('action') + '&action=move',
					type: 'POST',
					data: {
						id: ui.item.data('id'),
						order: ui.item.index(),
					},
				});
			}
		});
	}

	/**
	 * Ajaxify the different panels in the Settings page.
	 *
	 * @return {void}
	 */
	studio.panels.each(function() {
		let $banner = $(this).find('i.shop-panel-icon'),
			$bannerSize = $(this).find('select[name$="banner_size"]'),
			$bannerColour = $(this).find('select[name$="banner_colour"]'),
			$iconColour = $(this).find('select[name$="icon_colour"]'),
			$icon = $(this).find('.aps-icon-picker');

		let updateBanner = function() {
			let bg		= $bannerColour.val(),
				size	= $bannerSize.val(),
				color	= $iconColour.val(),
				icon	= $icon.val();

			size = size ? `shop-panel-icon-${size}` : '';
			bg = bg ? `shop-panel-icon-${bg}` : '';

			if ($.inArray(icon, ['', 'fa-li', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x']) !== -1) {
				$banner.hide();
			} else {
				$banner.attr('class', `icon ${icon} icon-${color} shop-panel-icon ${size} ${bg}`).show();
			}
		};

		$bannerSize.add($bannerColour).add($iconColour).on('change', updateBanner);
		$icon.on('iconpickerSelected keyup', updateBanner);
	});

	/**
	 * Show the slider value in the output element after it.
	 *
	 * @return {void}
	 */
	studio.sliders.on('input', function() {
		$(this).next('output').text(this.value);
	});

	/**
	 * Add an additional item image input row.
	 *
	 * @return {void}
	 */
	studio.imageAddRow.on('click', function() {
		let $parent	= $(this).parent(),
			$prev	= $parent.prev(),
			$row	= $prev.clone(),
			$input	= $row.find('input');

		/**
		 * Increment a number by one.
		 *
		 * @param  {?}		value
		 * @return {number}
		 */
		function increment(value) {
			return parseInt(value) + 1;
		}

		// Increment the id="" and name="" attributes
		$input.attr('id', $input.attr('id').replace(/\d+$/, increment));
		$input.attr('name', $input.attr('name').replace(/\d+(?=]$)/, increment));
		$input.val('');

		// Ajaxify the file links
		studio.ajaxifyLinks(0, $row);

		// And insert the row
		$row.insertBefore($parent)
	});

	/**
	 * Register shop file links as pop up requests.
	 *
	 * @param  {jQuery=}		context
	 * @return {void}
	 */
	studio.ajaxifyFiles = function(context) {
		$('[data-shop-file]', context).each(studio.ajaxifyLinks);
	};

	/**
	 * Open a new pop up window for the shop file links.
	 *
	 * @param  {number}			i
	 * @param  {HTMLElement}	element
	 * @return {void}
	 */
	studio.ajaxifyLinks = function(i, element) {
		let $this = $(element),
			$input = $this.find('input');

		$this.find('input').on('click', function() {
			let url = $this.data('shop-file') + encodeURIComponent($input.val()) + '&input=' + encodeURIComponent($input.attr('id'));

			window.open(url.replace(/&amp;/g, '&'), 'file', 'height=570,resizable=yes,scrollbars=yes, width=760');
		});
	};

	studio.ajaxifyFiles();

	/**
	 * Add AJAX callback for resolving items.
	 *
	 * @return {void}
	 */
	phpbb.addAjaxCallback('shop_resolve', function() {
		$(this).parents('fieldset').hide();

		let $active = $('#active');

		// If the item is not active, highlight the activate button.
		if ($active.is(':checked') === false) {
			let $span = $active.next('span');

			$span.addClass('ass-button-pulse');

			$active.on('change', function() {
				$span.removeClass('ass-button-pulse');
			});
		}
	});
});
