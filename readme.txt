=== Unstoppable White Label ===
Contributors: paramedic192
Donate link: https://unstoppablesolutions.app/donate
Tags: white label, branding, admin, login, customize
Requires at least: 6.0
Tested up to: 7.1
Stable tag: 1.2.26
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Remove WordPress branding and customize your admin experience. Replace logos, customize the login page, hide admin menus, and more.

== Description ==

**Unstoppable White Label** lets you rebrand WordPress for your clients. Replace logos, customize the login page, hide admin menus, and more – all without writing any code.

Perfect for agencies, freelancers, and developers who build WordPress sites for clients.

= Features =

**Login Page Branding**

* Custom login logo with size controls
* Background color and image
* Form position (center, left, right)
* Hide "Remember Me", "Lost Password", "Back to Site", and language switcher
* Full color scheme: background, form, text, links, buttons

**Admin Bar Customization**

* Replace "Howdy" greeting with custom text
* Hide WordPress logo
* Add your own admin bar logo
* Customize logo link URL or remove it entirely

**Admin Footer**

* Replace "Thank you for creating with WordPress" text
* Hide WordPress version number

**Admin Color Scheme**

* Menu background, text, and highlight colors
* Submenu background, text, and highlight colors
* Notification bubble color
* Link and button colors

**Sidebar Menus**

* Hide unlimited sidebar menus
* Rename any sidebar menu item
* Table-based interface for easy management

**Dashboard Customization**

* Remove any dashboard widget (WordPress core and third-party)
* Hide WooCommerce, Jetpack, Elementor, Wordfence, Site Kit, and more
* Create a custom welcome widget with WYSIWYG editor
* Add text, images, links, and media to your custom widget

**Visibility Controls**

* Hide unlimited admin bar items  
* Hide unlimited plugins from the plugins list
* Hide unlimited themes from the themes list
* Remove all admin notifications for non-WL admin users

**Settings Management**

* Import/Export settings between sites
* Reset to defaults
* Designate White Label Administrators who see the real WordPress

= How It Works =

1. Install and activate the plugin
2. Go to Settings → White Label
3. Enable white labeling
4. Configure your branding options
5. Select which users are "White Label Admins" (they see real WordPress)
6. Save and you're done!

= Who Is This For? =

* **Agencies** – Deliver polished, branded WordPress sites to clients
* **Freelancers** – Make your client sites look professional
* **Developers** – Remove WordPress clutter from client dashboards
* **Site Owners** – Simplify the WordPress experience for your team

= Feature Suggestions =

Have an idea for a new feature? Email us at support@unstoppablesolutions.app

= Privacy =

This plugin does not collect any data, does not connect to external services, and does not use cookies. All settings are stored locally in your WordPress database.

== Installation ==

1. Upload the `unstoppable-white-label` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → White Label to configure the plugin

== Frequently Asked Questions ==

= Does this work with page builders like Elementor? =

Yes! This plugin focuses on WordPress core branding and works alongside any page builder, theme, or other plugins.

= Can I white label other plugins like WooCommerce? =

This plugin focuses on WordPress core branding. Plugin-specific white labeling is intentionally out of scope to ensure stability and maintainability.

= Will my settings be lost if I deactivate? =

No. Settings are preserved unless you enable "Delete data on uninstall" in the General settings AND uninstall (not just deactivate) the plugin.

= Can I use this on client sites? =

Absolutely! That's exactly what it's designed for. The plugin is GPL licensed, so you can use it on as many sites as you want.

= What happens to hidden menus and plugins? =

They're only hidden visually – nothing is deleted or disabled. White Label Admins (and anyone via direct URL) can still access everything.

= Does this affect site security? =

No. This plugin only makes visual changes. Hidden items are still accessible via direct URLs. Do not rely on this plugin for security.

== Screenshots ==

1. General settings – enable white labeling and select admin users
2. Login branding – customize logo, background, colors, and layout
3. Admin branding – greeting text, logo, footer, and color scheme
4. Dashboard – remove widgets and create custom welcome widget
5. Visibility – hide/rename menus, hide themes, hide admin notifications
6. Tools – import, export, and reset settings

== Changelog ==

= 1.2.26 =
* Fixed: Custom admin bar logo rendered at full size for White Label Administrators because the sizing CSS was skipped for them.
* Fixed: "Enter Preview Mode" / "Exit Preview Mode" buttons silently failed instead of reporting errors.
* Fixed: Preview mode reload was blocked by the unsaved-changes warning triggered on page load by the colour pickers.
* Fixed: "Exit Preview Mode" bar had no JavaScript attached outside the plugin settings screen.
* Added: No-JavaScript fallback link to exit preview mode.
* Fixed: The plugin's own admin menu can no longer be hidden while preview mode is active.
* Added: "Hide the site icon" option in Admin Bar settings, removes the core site icon beside the site name.
* Fixed: Custom admin bar logo appeared at the far right of the left-hand items instead of first position.
* Added: uswl_admin_bar_logo_position filter ('left' or 'right').
* Fixed: Asset cache-busting version was pinned to an old value, causing stale CSS/JS to be served.

= 1.2.7 =
* Fixed: Moved inline script to enqueued JS file
* Fixed: Added sanitization for imported settings
* Fixed: Escaped all CSS color values before output

= 1.2.6 =
* Removed: Custom CSS field (WordPress.org compliance)

= 1.2.5 =
* Fixed: Combined phpcs ignore rules on single line

= 1.2.4 =
* Fixed: Added phpcs ignore for JSON input sanitization (sanitized after decode)

= 1.2.3 =
* Fixed: Plugin Check compliance - added phpcs ignore for verified nonce
* Fixed: Plugin Check compliance - prefixed all template variables with uswl_

= 1.2.2 =
* Fixed: WordPress.org compliance - proper sanitization and escaping
* Fixed: Removed inline styles (moved to CSS file)
* Fixed: Added ABSPATH checks to all PHP files

= 1.2.1 =
* Fixed: Settings sections now display correctly (removed inline style override)

= 1.2.0 =
* New: Rename sidebar menus – give menus custom names
* New: Remove dashboard widgets – hide core and third-party widgets
* New: Custom dashboard widget – WYSIWYG editor with media support
* New: Hide themes from the Appearance > Themes screen
* New: Hide all admin notifications for non-WL admin users
* New: Admin Color Scheme – customize menu, submenu, notification, link, and button colors
* New: Hide WordPress version from admin footer
* New: Customize admin bar logo link URL or remove it
* New: Login page color scheme – form background, text, links, and buttons
* New: Hide login page elements – Remember Me, Lost Password, Back to Site, Language Switcher
* New: Login logo size controls (width and height)
* New: Feature suggestion email link in Visibility settings
* Changed: All features are now free with no limits
* Changed: Unlimited menus, admin bar items, plugins, and themes can be hidden
* Improved: WordPress.org compliant inline style handling
* Removed: Pro upsells and feature limitations

= 1.1.3 =
* Added: Check All / Uncheck All buttons for visibility options

= 1.1.2 =
* Changed: White Label Administrators now uses checkboxes
* Changed: Visibility options now use checkboxes for easier selection
* Fixed: Visibility settings now save and apply correctly

= 1.1.1 =
* Fixed: Custom admin bar logo now displays correctly

= 1.1.0 =
* New: Login Form Position option (Center, Left, Right)
* Fixed: Settings save now works reliably

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.2.0 =
Major update! All features are now completely free with no limits. New: rename menus, remove/add dashboard widgets, hide themes, hide notifications, admin color scheme, and login page enhancements.

= 1.1.0 =
New login form position feature! Position your login form on the left or right side to show off background images.
