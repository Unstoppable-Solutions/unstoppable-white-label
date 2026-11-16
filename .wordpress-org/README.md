# WordPress.org directory assets

Everything in this folder is published to the plugin's **WordPress.org
directory listing** — the screenshots, banner and icon shown at
https://wordpress.org/plugins/unstoppable-white-label/

It is **not** shipped inside the plugin zip. `.distignore` excludes it, so
none of it is downloaded by users who install the plugin.

Do not confuse this folder with `/assets/` at the repository root — that one
holds the plugin's own CSS and JavaScript and *is* shipped to users.

## How this reaches WordPress.org

`.github/workflows/deploy-to-wporg.yml` runs the
`10up/action-wordpress-plugin-deploy` action on every published GitHub
Release. That action mirrors this folder into the SVN `assets/` directory,
which lives alongside `trunk/` and `tags/` and is not versioned with the
plugin.

**Mirrored means mirrored.** The action deletes anything in SVN `assets/`
that has no matching file here. If an image is uploaded straight to SVN
without being committed to this folder, the next release removes it. Keeping
the files here is what makes them survive.

Because SVN `assets/` is not versioned, updating an image here and releasing
replaces it on the directory page immediately — including for the currently
published version. Screenshots do not need a plugin release of their own,
though they do need *a* release to trigger the sync.

## File names

Names are fixed. WordPress.org ignores anything it does not recognise.

### Screenshots

`screenshot-1.png` … `screenshot-N.png`

The numbers line up with the `== Screenshots ==` list in `readme.txt`, which
supplies each caption. That list currently declares six, so the full set is:

| File | Caption in `readme.txt` |
|---|---|
| `screenshot-1.png` | General settings – enable white labeling and select admin users |
| `screenshot-2.png` | Login branding – customize logo, background, colors, and layout |
| `screenshot-3.png` | Admin branding – greeting text, logo, footer, and color scheme |
| `screenshot-4.png` | Dashboard – remove widgets and create custom welcome widget |
| `screenshot-5.png` | Visibility – hide/rename menus, hide themes, hide admin notifications |
| `screenshot-6.png` | Tools – import, export, and reset settings |

Keep the two lists in step. A caption with no matching file renders as a
broken image; a file with no caption is ignored. `.jpg` works too, but do not
have both `screenshot-1.png` and `screenshot-1.jpg`.

No fixed dimensions. Roughly 1280×720 or wider reads well, and the directory
scales images down, so capture at a width that stays legible.

### Banner

| File | Size | Notes |
|---|---|---|
| `banner-772x250.png` | 772 × 250 | Standard |
| `banner-1544x500.png` | 1544 × 500 | Retina, optional but worth having |

Keep text near the centre — the banner is cropped at narrow viewports. `.jpg`
is allowed and is usually smaller for photographic banners.

### Icon

| File | Size | Notes |
|---|---|---|
| `icon-128x128.png` | 128 × 128 | Standard |
| `icon-256x256.png` | 256 × 256 | Retina |
| `icon.svg` | — | Optional; replaces both when present |

The icon appears in search results and on the Plugins screen of every site
running the plugin, so it needs to read at 32px.

## Checklist for adding or changing assets

1. Drop the files in this folder with the exact names above.
2. If screenshots changed, update `== Screenshots ==` in `readme.txt` so the
   captions still match.
3. Commit and push to `main`.
4. Publish a GitHub Release — the deploy workflow syncs this folder to SVN.
5. Check the directory page; the CDN can take a few minutes to catch up.
