# Changelog

All notable user-facing changes are documented here. For WordPress.org users,
the same changelog lives in `readme.txt` and renders on the plugin's directory
page. This file follows the format described at https://keepachangelog.com.

## [Unreleased]

Nothing yet. See `ROADMAP.md` for what's planned.

## [1.2.27]

### Fixed
- The plugin's own admin menu can no longer be hidden at all. It was
  previously protected only while preview mode was active, so hiding
  "White Label" through the normal Visibility settings removed the only
  discoverable route back into the settings screen.
- `MenuVisibility::get_max_hidden()` defaulted the
  `uswl_visibility_max_hidden_menus` filter to `5` while
  `AdminAssets::get_max_limit()` defaulted the same filter to `-1`. The method
  is unused by the settings UI, so there was no user-facing effect.

### Changed
- Verified against WordPress 7.1.3. The `Tested up to` field records `7.1`,
  since WordPress.org accepts only `major.minor` there.

## [1.2.26]

### Added
- "Hide the site icon" option in Admin Bar settings, which removes the core
  site icon shown beside the site name.
- `uswl_admin_bar_logo_position` filter (`left` or `right`).
- No-JavaScript fallback link for leaving preview mode.

### Fixed
- Custom admin bar logo rendered at full size for White Label Administrators,
  because the sizing CSS was skipped for exactly those users.
- Custom admin bar logo appeared at the far right of the left-hand items
  instead of in first position.
- "Enter Preview Mode" and "Exit Preview Mode" failed silently instead of
  reporting the error.
- Preview mode reload was blocked by the unsaved-changes warning that the
  colour pickers trigger on page load.
- "Exit Preview Mode" had no JavaScript attached outside the plugin's own
  settings screen.
- Asset cache-busting version was pinned to an old value, so stale CSS and JS
  were served after updates.

## [1.2.7]

### Fixed
- Moved inline script to an enqueued JS file.
- Added sanitization for imported settings.
- Escaped all CSS colour values before output.

## [1.2.6]

### Removed
- Custom CSS field, for WordPress.org compliance.

## [1.2.0]

### Added
- Rename sidebar menus.
- Remove dashboard widgets, core and third-party.
- Custom dashboard widget with a WYSIWYG editor.
- Hide themes from Appearance → Themes.
- Hide all admin notifications for non-White-Label users.
- Admin colour scheme: menu, submenu, notification, link and button colours.
- Hide the WordPress version from the admin footer.
- Custom admin bar logo link, or no link.
- Login page colour scheme and element hiding.
- Login logo size controls.

### Changed
- All features are free with no limits.

### Removed
- Pro upsells and feature limitations.

## [1.0.0]

- Initial release.
