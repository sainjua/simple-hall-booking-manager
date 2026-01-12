Here’s a comprehensive, up-to-date Pre-Submission Checklist for your WordPress plugin to submit to the official WordPress.org Plugin Directory (as of January 2026).
This is based on the Detailed Plugin Guidelines (last updated January 8, 2026), common rejection reasons, and best practices from the Plugins team.
Use this as a printable/markable document — go through each item and check it off before zipping and submitting your plugin.
1. Licensing & Ownership (Critical – Instant Rejection if Wrong)

 Plugin (all code, images, CSS/JS, third-party libraries) is 100% GPL-compatible (GPLv2 or later strongly recommended).
 No proprietary, closed-source, or non-GPL-compatible components (e.g., no MIT-only if it conflicts; no paid/trial-locked features).
 Plugin is your original work (or you have full rights/permission to submit it).
 No trademark violations (e.g., don't start slug/name with "Woo", "Elementor", "Yoast", "Google", etc., unless you're the official owner).

2. Plugin File Headers & Structure

 Main plugin file (e.g., your-plugin-slug.php) has complete, valid headers:
Plugin Name, Plugin URI, Description, Version, Requires at least, Tested up to, Requires PHP, Author, License (GPL-2.0-or-later), Text Domain.

 Text Domain matches your plugin slug.
 Version is properly incremented (e.g., 1.0.0 → 1.0.1).
 No development files included (no /tests/, /node_modules/, /vendor/ unless needed & minified properly, no .git/, no .DS_Store, etc.).
 Plugin is complete and functional at submission (no "coming soon" or skeleton code).

3. readme.txt (Most Common Rejection Reason!)

 Follows the exact official format.
 Key sections present & filled properly:
Plugin Name, Tags (max ~5, relevant, no spam/keyword stuffing),
Requires at least, Tested up to, Requires PHP, Stable tag (matches current version, e.g., "1.0.0"),
Description, Installation, FAQ, Screenshots (with captions), Changelog, Upgrade Notice.

 No spammy content: no affiliate links (unless clearly disclosed & relevant), no blackhat SEO, no excessive caps/exclamation points.
 Written for humans, not search engines.

4. Security & Best Practices (Security Team Can Close Plugin Later!)

 All inputs sanitized/escaped/validated (use sanitize_*(), esc_*(), nonces on forms/AJAX).
 Capability checks (current_user_can()) on admin actions.
 No direct file access (add defined('ABSPATH') || exit; at top of PHP files).
 No user tracking/telemetry without explicit opt-in consent + clear privacy policy in readme.
 No loading executable code from third parties (remote JS/CSS only if secure, documented, and user-initiated).
 Use WordPress core libraries (don't bundle your own jQuery, PHPMailer, etc.).
 Code is human-readable (no obfuscation, minification only where necessary & source provided).

5. Code Quality & Performance

 No errors/warnings/notices with WP_DEBUG + SCRIPT_DEBUG enabled.
 Tested on latest WordPress + PHP 8.0+ (ideally PHP 8.1/8.2/8.3).
 Run the official Plugin Check tool — fix all errors & most warnings.
 No unnecessary admin notices, ads, or dashboard hijacking (notices must be dismissible & limited).
 No "Powered by" or credit links forced on frontend (must be optional).

6. External Services / SaaS / Freemium Rules

 If using external API/service: clearly documented in readme (link to ToS, privacy policy).
No trialware — full functionality available without payment/registration/quotas.
 No paywall-locked features in the submitted version.

7. Assets & Presentation

 Optional but highly recommended:
assets/ folder ready for SVN later: banner-772x250.png, banner-1544x500.png, icon-128x128.png, icon-256x256.png.
Screenshots in readme (numbered 1–n, with descriptive captions).

 Plugin name/slug chosen wisely (permanent, descriptive, no trademark issues).

8. Final Pre-Submission Checks

 Zip contains only the plugin folder (e.g., your-plugin-slug/ — not nested deeper or with extra parent folder).
 Tested manually: install via wp-admin → activate → use all features → deactivate → delete (no errors).
 WordPress.org account has Two-Factor Authentication (2FA) enabled (required since late 2024).
 Email plugins@wordpress.org whitelisted to avoid missing review emails.

Quick Rejection Hot List (Avoid These!)

Incomplete readme.txt or missing stable tag.
Non-GPL license or bundled proprietary code.
Obfuscated/unreadable code.
Trial/paid-locked features.
Trademark in slug (e.g., "chatgpt-ai-tool" often rejected).
Security vulnerabilities (XSS, missing nonces, etc.).
Spammy readme (too many tags, affiliate spam).

If everything checks green → zip it up and head to:
https://wordpress.org/plugins/developers/add/
Submit with confidence — most rejections are fixable (readme issues are the #1 cause). If you get feedback, address it quickly and resubmit.