/**
 * Unstoppable White Label - Admin JavaScript
 *
 * @package Unstoppable_White_Label
 */

(function($) {
	'use strict';

	var USWL = {
		/**
		 * Initialize.
		 */
		init: function() {
			this.bindEvents();
			this.initSections();
			this.initColorPickers();
			this.initMediaUploaders();
			this.initLimitedSelects();
		},

		/**
		 * Bind events.
		 */
		bindEvents: function() {
			// Navigation
			$(document).on('click', '.uswl-nav-item', this.handleNavClick.bind(this));

			// Save button - clear unsaved flag so form can submit
			$('#uswl-settings-form').on('submit', function() {
				USWL.hasUnsaved = false;
				$('#uswl-save-settings').text(uswlAdmin.strings.saving);
				// Allow form to submit normally
				return true;
			});

			// Export
			$('#uswl-export-settings').on('click', this.handleExport.bind(this));

			// Import
			$('#uswl-import-file').on('change', this.handleFileSelect.bind(this));
			$('#uswl-import-settings').on('click', this.handleImport.bind(this));

			// Reset
			$('#uswl-reset-settings').on('click', this.handleReset.bind(this));

			// Check All / Uncheck All buttons
			$(document).on('click', '.uswl-check-all', this.handleCheckAll.bind(this));
			$(document).on('click', '.uswl-uncheck-all', this.handleUncheckAll.bind(this));

			// Toggle logo link field based on checkbox
			$('#uswl-remove-logo-link').on('change', function() {
				if ($(this).is(':checked')) {
					$('#uswl-logo-link-wrapper').slideUp();
				} else {
					$('#uswl-logo-link-wrapper').slideDown();
				}
			});

			// Track unsaved changes
			$('#uswl-settings-form').on('change', ':input', this.markUnsaved.bind(this));

			// Warn on leave (only if NOT submitting form)
			$(window).on('beforeunload', this.warnUnsaved.bind(this));

			// Preview mode
			$(document).on('click', '#uswl-enable-preview', this.enablePreviewMode.bind(this));
			$(document).on('click', '#uswl-exit-preview', this.disablePreviewMode.bind(this));
		},

		/**
		 * Initialize sections.
		 */
		initSections: function() {
			var hash = window.location.hash.substring(1);
			var $sections = $('.uswl-section');
			var $navItems = $('.uswl-nav-item');

			// Show first section or hash section
			if (hash && $('#' + hash).length) {
				$sections.removeClass('active');
				$navItems.removeClass('active');
				$('#' + hash).addClass('active');
				$navItems.filter('[data-section="' + hash + '"]').addClass('active');
			} else {
				$sections.first().addClass('active');
				$navItems.first().addClass('active');
			}
		},

		/**
		 * Handle navigation click.
		 */
		handleNavClick: function(e) {
			e.preventDefault();

			var $link = $(e.currentTarget);
			var section = $link.data('section');

			// Update nav
			$('.uswl-nav-item').removeClass('active');
			$link.addClass('active');

			// Update sections
			$('.uswl-section').removeClass('active');
			$('#' + section).addClass('active');

			// Update hash
			window.location.hash = section;
		},

		/**
		 * Initialize color pickers.
		 */
		initColorPickers: function() {
			$('.uswl-color-picker').wpColorPicker({
				change: function() {
					USWL.markUnsaved();
				}
			});
		},

		/**
		 * Initialize media uploaders.
		 */
		initMediaUploaders: function() {
			var frames = {};

			$(document).on('click', '.uswl-media-select', function(e) {
				e.preventDefault();

				var target = $(this).data('target');
				var $preview = $('#uswl-' + target + '-preview');
				var $idInput = $('#uswl-' + target + '-id');
				var $urlInput = $('#uswl-' + target + '-url');
				var $removeBtn = $(this).siblings('.uswl-media-remove');

				// Create frame if not exists
				if (!frames[target]) {
					frames[target] = wp.media({
						title: uswlAdmin.strings.uploadTitle,
						button: {
							text: uswlAdmin.strings.uploadButton
						},
						multiple: false
					});

					frames[target].on('select', function() {
						var attachment = frames[target].state().get('selection').first().toJSON();
						$idInput.val(attachment.id);
						$urlInput.val(attachment.url);
						$preview.html('<img src="' + attachment.url + '" alt="">');
						$removeBtn.show();
						USWL.markUnsaved();

						// Show background options if this is the login background image
						if (target === 'login-bg-image') {
							$('#uswl-bg-size-row, #uswl-bg-position-row, #uswl-bg-repeat-row').show();
						}
					});
				}

				frames[target].open();
			});

			$(document).on('click', '.uswl-media-remove', function(e) {
				e.preventDefault();

				var target = $(this).data('target');
				var $preview = $('#uswl-' + target + '-preview');
				var $idInput = $('#uswl-' + target + '-id');
				var $urlInput = $('#uswl-' + target + '-url');

				$idInput.val(0);
				$urlInput.val('');
				$preview.empty();
				$(this).hide();
				USWL.markUnsaved();

				// Hide background options if this is the login background image
				if (target === 'login-bg-image') {
					$('#uswl-bg-size-row, #uswl-bg-position-row, #uswl-bg-repeat-row').hide();
				}
			});
		},

		/**
		 * Initialize limited selects.
		 */
		initLimitedSelects: function() {
			$('.uswl-limited-select').each(function() {
				var $select = $(this);
				var max = parseInt($select.data('max'), 10);

				if (max <= 0) {
					return; // Unlimited
				}

				$select.on('change', function() {
					var selected = $(this).val() || [];
					if (selected.length > max) {
						// Remove last selected
						selected.pop();
						$(this).val(selected);
						alert(uswlAdmin.strings.limitReached);
					}
				});
			});
		},

		/**
		 * Handle save - just let the form submit normally.
		 * PHP handles the actual saving at the top of the template.
		 */
		handleSave: function(e) {
			// Allow form to submit normally
			var $button = $('#uswl-save-settings');
			$button.text(uswlAdmin.strings.saving);
			USWL.hasUnsaved = false;
			// Form submits normally, PHP handles it
		},

		/**
		 * Get form data as nested object.
		 */
		getFormData: function($form) {
			var data = {};

			$form.find(':input').each(function() {
				var $input = $(this);
				var name = $input.attr('name');
				
				if (!name || name === 'uswl_nonce' || name === '_wp_http_referer') {
					return;
				}

				// Parse name like "general[enabled]" or "visibility[hidden_menus][]"
				var match = name.match(/^([^\[]+)(?:\[([^\]]*)\])?(?:\[\])?$/);
				if (!match) {
					return;
				}

				var section = match[1];
				var key = match[2];

				if (!data[section]) {
					data[section] = {};
				}

				var value;

				if ($input.is(':checkbox')) {
					value = $input.is(':checked');
				} else if ($input.is('select[multiple]')) {
					value = $input.val() || [];
				} else {
					value = $input.val();
				}

				if (key) {
					data[section][key] = value;
				}
			});

			return data;
		},

		/**
		 * Handle export.
		 */
		handleExport: function(e) {
			e.preventDefault();

			var $button = $(e.target);
			$button.prop('disabled', true);

			$.ajax({
				url: uswlAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'uswl_export_settings',
					nonce: uswlAdmin.nonce
				},
				success: function(response) {
					if (response.success && response.data.data) {
						var blob = new Blob(
							[JSON.stringify(response.data.data, null, 2)],
							{ type: 'application/json' }
						);
						var url = URL.createObjectURL(blob);
						var a = document.createElement('a');
						var date = new Date().toISOString().split('T')[0];

						a.href = url;
						a.download = 'uswl-settings-' + date + '.json';
						document.body.appendChild(a);
						a.click();
						document.body.removeChild(a);
						URL.revokeObjectURL(url);
					} else {
						alert(response.data.message || uswlAdmin.strings.error);
					}
				},
				error: function() {
					alert(uswlAdmin.strings.error);
				},
				complete: function() {
					$button.prop('disabled', false);
				}
			});
		},

		/**
		 * Handle file select for import.
		 */
		handleFileSelect: function(e) {
			var file = e.target.files[0];
			$('#uswl-import-settings').prop('disabled', !file);
		},

		/**
		 * Handle import.
		 */
		handleImport: function(e) {
			e.preventDefault();

			if (!confirm(uswlAdmin.strings.confirmImport)) {
				return;
			}

			var file = $('#uswl-import-file')[0].files[0];
			if (!file) {
				return;
			}

			var $button = $(e.target);
			$button.prop('disabled', true);

			var reader = new FileReader();
			reader.onload = function(event) {
				var data;
				try {
					data = JSON.parse(event.target.result);
				} catch (err) {
					alert(uswlAdmin.strings.invalidFile);
					$button.prop('disabled', false);
					return;
				}

				$.ajax({
					url: uswlAdmin.ajaxUrl,
					type: 'POST',
					data: {
						action: 'uswl_import_settings',
						nonce: uswlAdmin.nonce,
						import_data: JSON.stringify(data)
					},
					success: function(response) {
						if (response.success) {
							alert(uswlAdmin.strings.importSuccess);
							window.location.reload();
						} else {
							alert(response.data.message || uswlAdmin.strings.error);
						}
					},
					error: function() {
						alert(uswlAdmin.strings.error);
					},
					complete: function() {
						$button.prop('disabled', false);
					}
				});
			};

			reader.readAsText(file);
		},

		/**
		 * Handle reset.
		 */
		handleReset: function(e) {
			e.preventDefault();

			if (!confirm(uswlAdmin.strings.confirmReset)) {
				return;
			}

			var $button = $(e.target);
			$button.prop('disabled', true);

			$.ajax({
				url: uswlAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'uswl_reset_settings',
					nonce: uswlAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						alert(uswlAdmin.strings.resetSuccess);
						window.location.reload();
					} else {
						alert(response.data.message || uswlAdmin.strings.error);
					}
				},
				error: function() {
					alert(uswlAdmin.strings.error);
				},
				complete: function() {
					$button.prop('disabled', false);
				}
			});
		},

		/**
		 * Mark form as having unsaved changes.
		 */
		markUnsaved: function() {
			this.hasUnsaved = true;
		},

		/**
		 * Warn about unsaved changes.
		 */
		warnUnsaved: function(e) {
			if (this.hasUnsaved) {
				var message = uswlAdmin.strings.unsavedChanges;
				e.returnValue = message;
				return message;
			}
		},

		/**
		 * Handle Check All button click.
		 */
		handleCheckAll: function(e) {
			e.preventDefault();
			var $button = $(e.currentTarget);
			var target = $button.data('target');
			var max = parseInt($button.data('max'), 10) || 0;
			var $list = $('#uswl-' + target + '-list');
			var $checkboxes = $list.find('input[type="checkbox"]:not(:disabled)');
			var checked = 0;

			$checkboxes.each(function() {
				// If max is 0 (Pro), check all. Otherwise respect the limit.
				if (max === 0 || checked < max) {
					$(this).prop('checked', true);
					checked++;
				}
			});

			this.markUnsaved();
		},

		/**
		 * Handle Uncheck All button click.
		 */
		handleUncheckAll: function(e) {
			e.preventDefault();
			var $button = $(e.currentTarget);
			var target = $button.data('target');
			var $list = $('#uswl-' + target + '-list');
			var $checkboxes = $list.find('input[type="checkbox"]');

			$checkboxes.prop('checked', false);
			this.markUnsaved();
		},

		/**
		 * Enable preview mode.
		 */
		enablePreviewMode: function(e) {
			this.togglePreviewMode(e, 'enable', 'Enter Preview Mode');
		},

		/**
		 * Disable preview mode.
		 */
		disablePreviewMode: function(e) {
			this.togglePreviewMode(e, 'disable', 'Exit Preview Mode');
		},

		/**
		 * Shared preview mode toggle.
		 *
		 * @param {Event}  e            Click event.
		 * @param {string} mode         'enable' or 'disable'.
		 * @param {string} originalText Button label to restore on failure.
		 */
		togglePreviewMode: function(e, mode, originalText) {
			e.preventDefault();

			var $button = $(e.currentTarget);

			// Guard against double-clicks.
			if ($button.prop('disabled')) {
				return;
			}

			$button.prop('disabled', true).text('Please wait...');

			$.ajax({
				url: uswlAdmin.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'uswl_preview_mode',
					preview_action: mode,
					nonce: uswlAdmin.nonce
				}
			}).done(function(response) {
				if (response && response.success) {
					USWL.forceReload();
					return;
				}

				// wp_send_json_error() returns HTTP 200, so it lands here, not in
				// .fail(). Surface the message instead of failing silently.
				var message = (response && response.data && response.data.message)
					? response.data.message
					: 'Could not toggle preview mode. Please refresh the page and try again.';

				$button.prop('disabled', false).text(originalText);
				window.alert(message);
			}).fail(function(jqXHR) {
				$button.prop('disabled', false).text(originalText);
				window.alert('Preview mode request failed (HTTP ' + jqXHR.status + '). Check the browser console for details.');
			});
		},

		/**
		 * Reload the page, bypassing the unsaved-changes warning.
		 *
		 * wpColorPicker fires change events on init, which flips hasUnsaved to
		 * true on page load. Without clearing it first, the reload triggers a
		 * "Leave site?" confirm and the button appears to do nothing.
		 */
		forceReload: function() {
			USWL.hasUnsaved = false;
			$(window).off('beforeunload');
			window.onbeforeunload = null;
			window.location.reload();
		},

		/**
		 * Flag for unsaved changes.
		 */
		hasUnsaved: false
	};

	// Initialize on ready
	$(document).ready(function() {
		USWL.init();
	});

})(jQuery);
