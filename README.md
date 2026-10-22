# Unstoppable White Label

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/unstoppable-white-label.svg)](https://wordpress.org/plugins/unstoppable-white-label/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/unstoppable-white-label.svg)](https://wordpress.org/plugins/unstoppable-white-label/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)

Replace WordPress branding, customize the login screen, swap admin logos, hide menu items, and tailor the dashboard — ship client sites that look and feel like yours.

> **End users:** install from the WordPress Plugin Directory — [wordpress.org/plugins/unstoppable-white-label](https://wordpress.org/plugins/unstoppable-white-label/)

## Features

- **Custom login page** — swap the WordPress logo, change colors, add a background image, point the logo URL anywhere
- **Admin branding** — replace the toolbar logo, footer text, favicon
- **Menu hiding** — hide specific admin menu items per role
- **Dashboard widget control** — remove default WordPress widgets, add your own
- **Role-aware** — rules apply by user role, so admins still see the real thing
- **No code required** — everything configured from Settings → White Label

## Who it's for

Agencies handing off sites to clients, resellers who want their own branding on client dashboards, and anyone tired of the "Howdy, admin" WordPress experience looking generic.

## Development

### Release workflow

Releases are deployed to WordPress.org automatically by GitHub Actions:

1. Finish the feature work on `main`
2. Bump the version in `unstoppable-white-label.php` (header + any constant), `readme.txt` (Stable tag), and add a Changelog entry
3. Commit and push `main`
4. On GitHub, create a new **Release** tagged `vX.Y.Z` — the deploy workflow fires and pushes to WordPress.org SVN

### CI

Every push and pull request runs the official WordPress Plugin Check against the codebase.

## License

GPL-2.0-or-later. See the `License` header in `unstoppable-white-label.php`.

## Author

Developed and maintained by [Unstoppable Solutions](https://unstoppablesolutions.app).
