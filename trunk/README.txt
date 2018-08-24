=== Update Alerts ===
Contributors: porticue
Author URI: http://therevoltgroup.com
Author: The Revolt Group
Donate link: https://paypal.me/porticue
Tags: plugin_management, plugin_alerts
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: trunk
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Alert admins to plugin, active theme and WordPress core updates

== Description ==

When a plugin, active theme or core update is available Update Alerts will send out emails to the configured addresses. These emails could then be used to trigger a Microsoft Flow in order to create a Jira ticket.

== Installation ==

Activate plugin. Under settings tab configure plugin how to operate.

== Frequently Asked Questions ==

= How will I be alerted? =

You can configure emails to be sent out to specified addresses.

== Screenshots ==

1. None.

== Changelog ==

= 1.1.0 =
* Added Microsoft Flow support

= 1.0 =
* Initial version


== Upgrade Notice ==

= 1.1.0 =
This update allows the direct calling of Microsoft Flow. Using an Http Request action as the entry point into the Flow will provide a request address string. Supply that address string in the plugin settings.

= 1.0 =
This is the initial release