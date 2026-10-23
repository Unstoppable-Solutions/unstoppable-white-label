# Roadmap — Unstoppable White Label

Development priorities for the free plugin. Pro-only work lives in the Pro
repo and is referenced here only where it affects the free side.

**Status values:** Proposed · Planned · In Progress · Completed
**Priority:** High · Medium · Low
**Complexity:** Small · Medium · Large
**Source:** Previous Discussion · Code Review · New Suggestion

Items marked **Confirmed** are agreed requirements. Everything else is a
proposal awaiting a decision.

---

## Phase 1 — Immediate Priorities

### 1.1 Repository migration and verification — **Confirmed**
Move the plugin into the `Unstoppable-Solutions` org and verify the tree is
complete.
*Benefit:* GitHub becomes the single source of truth.
Priority: High · Complexity: Small · Milestone: 1.2.27 · **Status: Completed** · Source: Previous Discussion

### 1.2 WordPress 7.1.3 compatibility metadata — **Confirmed**
Verified against WordPress 7.1.3. `Tested up to` reads `7.1`: WordPress.org
accepts a `major.minor` value only, and Plugin Check errors on a patch
version. The field was already correct; the verification is what changed.
*Benefit:* Directory listing reflects testing through the current release.
Priority: High · Complexity: Small · Milestone: 1.2.27 · **Status: Completed** · Source: Previous Discussion

### 1.3 Unconditional self-lockout guard — **Confirmed**
`MenuVisibility::hide_menus()` protected the plugin's own menu only while
preview mode was active. Hiding "White Label" through the normal Visibility
settings removed the only discoverable route back into the settings screen.
The guard is now unconditional.
*Benefit:* An admin can no longer lock themselves out of the plugin.
Priority: High · Complexity: Small · Milestone: 1.2.27 · **Status: Completed** · Source: Previous Discussion

### 1.4 Menu limit default disagreed with the rest of the plugin
`MenuVisibility::get_max_hidden()` defaulted the
`uswl_visibility_max_hidden_menus` filter to `5`, while
`AdminAssets::get_max_limit()` defaulted the same filter to `-1`. The method
is not currently called by the settings UI, so there was no user-facing
effect, but the two defaults would have diverged the moment it was wired up.
*Benefit:* Removes a trap for whoever next uses that method.
Priority: Medium · Complexity: Small · Milestone: 1.2.27 · **Status: Completed** · Source: Code Review

### 1.5 Unprefixed template variable in the Visibility section
`templates/admin/section-visibility.php` declares `$plugins` while every
other variable in the file carries the `uswl_` prefix. Release 1.2.3 claimed
to have prefixed all template variables; this one was missed. Plugin Check
flags unprefixed globals in templates.
*Benefit:* Closes a WordPress.org compliance gap before a reviewer finds it.
Priority: Medium · Complexity: Small · Milestone: 1.2.28 · **Status: Proposed** · Source: Code Review

### 1.6 Minimum WordPress version is declared inconsistently
The plugin header and `readme.txt` both say `Requires at least: 6.0`, but
`USWL_MIN_WP_VERSION` is `5.8`, so the runtime check is more permissive than
the published requirement. Raising the constant to `6.0` would deactivate the
plugin for anyone still on 5.8–5.9, so this needs a deliberate decision
rather than a silent harmonisation.
*Benefit:* The advertised requirement and the enforced one agree.
Priority: Medium · Complexity: Small · Milestone: TBD · **Status: Proposed** · Source: Code Review

### 1.7 Stale comment in the deploy workflow
`deploy-to-wporg.yml` still opens with "Dormant until this plugin is accepted
into the WordPress.org directory" and instructs the reader to add per-repo
SVN secrets and set `SLUG`. The plugin is live, `SLUG` is set, and the
secrets are org-level per `CLAUDE.md`.
*Benefit:* Stops the comment misleading a future maintainer mid-release.
Priority: Low · Complexity: Small · Milestone: 1.2.28 · **Status: Proposed** · Source: Code Review

### 1.8 Verify the Pro upgrade URL in `AdminNotices`
Carried over from earlier planning: the `$pro_url` property may still hold a
boilerplate placeholder from the plugin template.
*Benefit:* Upsell notices point somewhere real.
Priority: Medium · Complexity: Small · Milestone: 1.2.28 · **Status: Proposed** · Source: Previous Discussion

---

## Phase 2 — Short-Term Improvements

### 2.1 "Replace Logo" should imply removing the WordPress logo
Replacing the admin bar logo and removing the WordPress logo are two separate
checkboxes. An agency user who uploads a logo but misses the second checkbox
gets both logos side by side, even though the field is labelled
"Replace Logo" and the help text says "Replace the WordPress logo … with your
own".
*Benefit:* The setting does what its label promises; one less way to ship a
half-configured client site.
Priority: High · Complexity: Small · Milestone: 1.3.0 · **Status: Proposed** · Source: Previous Discussion

### 2.2 Warn before hiding a menu that leads somewhere important
Even with 1.3 in place, hiding Settings or Plugins can strand a
non-White-Label user. A short inline warning on the affected rows would make
the consequence visible at the moment of choosing.
*Benefit:* Fewer support emails that begin "I can't find…".
Priority: Medium · Complexity: Medium · Milestone: 1.3.0 · **Status: Proposed** · Source: New Suggestion

### 2.3 Configurable preview duration
The preview session is hard-coded to 10 minutes in
`Capabilities::enable_preview_mode()`. Longer reviews require re-entering it.
*Benefit:* Fits how agencies actually review a build.
Priority: Low · Complexity: Small · Milestone: 1.3.0 · **Status: Proposed** · Source: New Suggestion

### 2.4 Settings search
Six sections and a growing number of fields; finding one toggle means
remembering which tab it lives on.
*Benefit:* Faster configuration, better first-run experience.
Priority: Low · Complexity: Medium · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

---

## Phase 3 — Mid-Term Development

### 3.1 Role-based white labeling
White Label Administrators are designated by user ID. On a site with staff
turnover, or any site with more than a handful of editors, roles are the
natural unit.
*Benefit:* Configure once per role instead of per person; survives staff
changes.
Priority: High · Complexity: Medium · Milestone: 1.4.0 · **Status: Proposed** · Source: New Suggestion

### 3.2 Multisite / network-wide settings
Settings are per-site. An agency running a network currently configures each
site individually.
*Benefit:* Opens the plugin to network operators, a segment competitors serve.
Priority: Medium · Complexity: Large · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

### 3.3 WP-CLI commands
Export, import and reset are UI-only today. CLI equivalents would let the
same branding be applied during site provisioning.
*Benefit:* Branding becomes part of an automated build, not a manual step.
Priority: Medium · Complexity: Medium · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

### 3.4 Login page presets
A handful of ready-made login layouts to start from rather than a blank
colour-picker form.
*Benefit:* Shortens the path to a presentable result for non-designers.
Priority: Low · Complexity: Medium · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

---

## Phase 4 — Long-Term Development

### 4.1 Pro version on the documented hook API
Build Pro against the existing filter and action surface rather than forking
the free plugin. The hook inventory was mapped during planning.
*Benefit:* Pro stays upgrade-safe; the free plugin stays the single codebase.
Priority: High · Complexity: Large · Milestone: TBD · **Status: Proposed** · Source: Previous Discussion

### 4.2 Portable brand profiles
Extend export/import into a named, reusable brand profile that can be applied
to a new site in one step.
*Benefit:* Turns per-site setup into a one-click action across a client base.
Priority: Medium · Complexity: Large · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

### 4.3 Custom client-facing admin pages
Let an agency add its own documentation or support page into the client's
admin menu.
*Benefit:* Deflects support requests to the agency's own material.
Priority: Medium · Complexity: Large · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

### 4.4 Accessibility pass on the colour scheme controls
Nothing currently warns when a chosen menu/text pairing falls below WCAG
contrast thresholds.
*Benefit:* Keeps a branded admin usable, and matters for public-sector and
nonprofit clients with accessibility obligations.
Priority: Medium · Complexity: Medium · Milestone: TBD · **Status: Proposed** · Source: New Suggestion

---

## Maintenance

- Bump `Tested up to` as WordPress ships each release.
- Keep `readme.txt`, `CHANGELOG.md` and the plugin header version in step —
  see the Release workflow in `CLAUDE.md`.
- Update this file as items land.
