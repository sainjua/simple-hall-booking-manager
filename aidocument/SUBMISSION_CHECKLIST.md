# WordPress Plugin Submission Checklist

## 1. Code Quality

- [x] PHP, JS, and CSS follow WordPress coding standards (indentation, spacing, naming).  
- [x] Functions, classes, and global variables use a unique prefix or namespace.  
- [x] All debug code removed (`var_dump`, `print_r`, `die`, excessive `error_log`, test routes).  
- [x] PHPCS with WordPress Coding Standards (WPCS) runs clean or with only acceptable warnings.  

## 2. Security

- [x] All user input sanitized before use (e.g., `sanitize_text_field`, `sanitize_email`, `sanitize_textarea_field`).  
- [x] All output escaped before rendering (e.g., `esc_html`, `esc_attr`, `esc_url`, `wp_kses`).  
- [x] All forms and state‑changing actions use nonces and verify them (`check_admin_referer` / `wp_verify_nonce`).  
- [x] Capability checks added on all admin pages and AJAX/REST handlers (`current_user_can`).  
- [x] All SQL queries use `$wpdb->prepare()` or safe APIs; no unsanitized or dynamic SQL.  
- [x] No use of `eval`, unsafe `include`/`require` of user-controlled data, or writing files to arbitrary locations.  

## 3. Structure and Behavior

- [x] Main plugin file has a valid header (Plugin Name, URI, Description, Version, Author, Author URI, License, Text Domain).  
- [x] Clear folder structure (e.g., `includes/`, `admin/`, `public/`, `assets/`, `languages/`).  
- [x] Uses WordPress APIs where appropriate (Options API, Settings API, REST API, Transients, HTTP API).  
- [x] Activation/deactivation hooks implemented if needed (`register_activation_hook`, `register_deactivation_hook`).  
- [x] Uninstall logic present when appropriate (`uninstall.php` or `register_uninstall_hook`) and documented.  

## 4. Performance and Compatibility

- [x] Scripts and styles enqueued only on relevant pages (`wp_enqueue_scripts`, `admin_enqueue_scripts` with conditions).  
- [x] No heavy/blocking operations on every request (use caching/transients where reasonable).  
- [x] Plugin tested with the latest stable WordPress version.  
- [x] Plugin tested with supported PHP versions (including the minimum you claim to support).  
- [x] Plugin tested with at least one default theme (e.g., Twenty Twenty‑Four) and a couple of popular plugins for conflicts.  

## 5. WordPress.org Submission Requirements

- [x] Plugin and bundled libraries are GPL‑compatible and properly attributed.  
- [x] No tracking without explicit informed consent; any tracking is disclosed in the description.  
- [x] No hidden, cloaked, or spammy links or behavior.  
- [x] `readme.txt` present and valid (WordPress.org readme format).  
- [x] `readme.txt` includes:
  - [x] Plugin name, contributors, tags, requires at least, tested up to, stable tag.  
  - [x] Short description and detailed description.  
  - [x] Installation instructions.  
  - [x] FAQ section (if relevant).  
  - [x] Screenshots section (if you provide screenshots).  
  - [x] Changelog with version entries.  
  - [x] Upgrade notice for important/breaking changes.  
- [x] Version in main plugin file header matches version in `readme.txt` (stable tag / changelog).  
- [x] Text domain set correctly and matches plugin slug, with strings prepared for translation (`__`, `_e`, `_x`, etc.).  

## 6. Final Manual Checks

- [x] Plugin activates and deactivates without errors on a clean WordPress install.  
- [x] No PHP notices, warnings, or JS errors in normal usage.  
- [x] All user-facing text reviewed for clarity, spelling, and tone.  
- [x] Screenshots and icons prepared per WordPress.org image size guidelines (if submitting to the directory).  

## 7. WordPress.org Policy Compliance

### Licensing and third‑party assets

- [x] All plugin code, images, fonts, and libraries are GPL or GPL‑compatible, and their licenses are verified.  
- [x] Licenses/attributions for bundled third‑party assets are documented where appropriate.  
- [x] Any external APIs or services used comply with their own terms of use and are mentioned in the readme.  

### Trialware and premium features

- [x] The plugin is not trialware: no features stop working after a time limit or usage quota.  
- [x] The plugin does not gate code already shipped in the .org plugin behind a paywall.  
- [x] Any premium functionality is provided via a separate add‑on or external service, not locked code inside the .org plugin.  

### Tracking, privacy, and external requests

- [x] The plugin does not track users or send data to external servers without explicit, informed opt‑in.  
- [x] Any data collection or external communication is clearly explained in the settings/readme, ideally with a privacy policy link.  
- [x] The plugin does not offload unrelated assets (images, JS, CSS) to external servers purely for tracking or other non‑service reasons.  

### Remote code and updates

- [x] The plugin does not install or update plugins, themes, or add‑ons from servers other than WordPress.org inside WP admin.  
- [x] The plugin does not execute arbitrary remote PHP/JS code; any remote code is only via legitimate SaaS/service integrations.  
- [x] Third‑party CDNs are not used for general JS/CSS that could be shipped locally, except where required by a documented service.  

### Admin UX, links, and spam

- [x] Any “Powered by” or credit links on the front end are optional and default to OFF (opt‑in only).  
- [x] Admin notices, upsells, and banners are minimal, contextual, dismissible, and do not hijack the dashboard.  
- [x] Readme, tags, and translation strings are written for humans, without keyword stuffing or spammy/irrelevant tags.  
- [x] Any affiliate links are clearly disclosed and not cloaked or redirected to hide the destination.  

### Trademarks, names, and slugs

- [x] Plugin name and slug do not start with or misuse someone else’s trademark or project name unless you have permission.  
- [x] Branding avoids confusing users into thinking the plugin is an official product of another company/project if it is not.  

### Repository and releases

- [x] The plugin uses WordPress’ bundled libraries (e.g., jQuery, PHPMailer) instead of shipping its own copies.  
- [x] Version numbers are incremented for each release, and `readme.txt` (trunk) always reflects the current version.  
- [x] SVN is used only for release‑ready code; there are no rapid, minor commits just to appear “recently updated.”  
- [x] A complete, working plugin is provided at submission time (no “reserved name” or placeholder submissions).  
