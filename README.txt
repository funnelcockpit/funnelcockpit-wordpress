=== FunnelCockpit ===
Contributors: funnelcockpit
Donate link: https://funnelcockpit.com/
Tags: funnelcockpit, funnel, cockpit
Requires at least: 3.0.1
Tested up to: 7.0
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All-in-one funnel and landing page publishing for FunnelCockpit users.

== Description ==
FunnelCockpit helps you publish FunnelCockpit funnels and landing pages on your WordPress site. Connect your FunnelCockpit account, select the funnel page you want to use, and make it available through WordPress.

= Key features =
* *Funnel page publishing* - Connect FunnelCockpit pages to WordPress URLs
* *Landing page integration* - Publish landing pages from your FunnelCockpit account
* *Split test support* - Serve FunnelCockpit split test pages through WordPress
* *Caching* - Cache fetched funnel page content for faster delivery
* *WordPress front page support* - Use a funnel page as the WordPress front page
* *Mobile-ready output* - FunnelCockpit pages remain optimized for mobile devices

= Best for =
* Online marketers
* Businesses
* Agencies
* E-commerce stores
* Course providers
* FunnelCockpit users who want to publish funnels on WordPress

More information: https://funnelcockpit.com/
== Installation ==

1. Upload the `funnelcockpit` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.5.1 =
* Fixed FunnelCockpit admin page loading by using GET requests for funnel and funnel page lists

= 1.5.0 =
* Fixed split-test page rendering through the FunnelCockpit API
* Improved split-test compatibility when public FunnelCockpit page fetches are blocked

= 1.4.9 =
* Confirmed compatibility with WordPress 7.0
* Updated WordPress compatibility metadata

= 1.4.8 =
* Updated plugin icons (128x128 and 256x256)
* Removed deprecated banner asset
* No code changes; assets only

= 1.4.7 =
* Fixed form submission handling in admin settings page
* Improved security and error handling for settings form
* Removed redirect issues when saving plugin options

= 1.4.6 =
* Updated plugin documentation and changelog formatting

= 1.4.5 =
* Fixed API connectivity issues for improved plugin stability

= 1.4.4 =
* Fix WordPress coding standards violations for security compliance
* Add nonce verification for form security
* Sanitize and validate all user inputs properly
* Replace date() with gmdate() for timezone safety
* Remove debug code and improve error handling
* Update WordPress compatibility to 6.8

= 1.4.3 =
* Fix repository metadata issues for WordPress.org compliance

= 1.4.2 =
* Optimize error handling when submitting the options page

= 1.4.1 =
* Hide split test pages from dropdown

= 1.4.0 =
* Fixed post titles when saving pages

= 1.3.5 =
* Resolve Elementor compatibility

= 1.3.4 =
* Fix warning

= 1.3.3 =
* Fix error notices

= 1.3.2 =
* Split-tests - Hotfix (2)

= 1.3.1 =
* Split-tests - Hotfix

= 1.3.0 =
* Split-tests

= 1.2.4 =
* Add option "WordPress Header nicht entfernen"

= 1.2.3 =
* Bug Fixes

= 1.2.2 =
* Bug Fixes

= 1.2.1 =
* Bug Fixes

= 1.2.0 =
* Add ability to define funnel pages as front pages

= 1.1.0 =
* Use HTTPS with API

= 1.0.0 =
* Initial release
