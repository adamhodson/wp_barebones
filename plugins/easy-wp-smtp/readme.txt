=== Easy WP SMTP ===
Contributors: wpecommerce, wp.insider, alexanderfoxc
Donate link: https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197
Tags: mail, wordpress smtp, phpmailer, smtp, wp_mail, email, gmail, outgoing mail, privacy, security, sendmail, ssl, tls, wp-phpmailer, mail smtp, wp smtp
Requires at least: 4.3
Tested up to: 5.0.3
Requires PHP: 5.3
Stable tag: 1.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily send emails from your WordPress blog using your preferred SMTP server

== Description ==

Easy WP SMTP allows you to configure and send all outgoing emails via a SMTP server. This will prevent your emails from going into the junk/spam folder of the recipients.

= Easy WP SMTP Features =

* Send email using a SMTP sever.
* You can use Gmail, Yahoo, Hotmail's SMTP server if you have an account with them.
* Seamlessly connect your WordPress blog with a mail server to handle all outgoing emails (it's as if the email has been composed inside your mail account).
* Securely deliver emails to your recipients.
* Option to enable debug logging to see if the emails are getting sent out successfully or not.
* Ability to specify a Reply-to email address.

= Easy WP SMTP Plugin Usage =

Once you have installed the plugin there are some options that you need to configure in the plugin setttings (go to `Settings->Easy WP SMTP` from your WordPress Dashboard).

**a)** Easy WP SMTP General Settings

The general settings section consists of the following options

* From Email Address: The email address that will be used to send emails to your recipients
* From Name: The name your recipients will see as part of the "from" or "sender" value when they receive your message
* SMTP Host: Your outgoing mail server (example: smtp.gmail.com)
* Type of Encryption: none/SSL/TLS
* SMTP Port: The port that will be used to relay outbound mail to your mail server (example: 465)
* SMTP Authentication: No/Yes (This option should always be checked "Yes")
* Username: The username that you use to login to your mail server
* Password: The password that you use to login to your mail server

For detailed documentation on how you can configure these options please visit the [Easy WordPress SMTP](https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197) plugin page

**b)** Easy WP SMTP Testing & Debugging Settings

This section allows you to perform some email testing to make sure that your WordPress site is ready to relay all outgoing emails to your configured SMTP server. It consists of the following options:

* To: The email address that will be used to send emails to your recipients
* Subject: The subject of your message
* Message: A textarea to write your test message.

Once you click the "Send Test Email" button the plugin will try to send an email to the recipient specified in the "To" field.

== Installation ==

1. Go to the Add New plugins screen in your WordPress admin area
1. Click the upload tab
1. Browse for the plugin file (easy-wp-smtp.zip)
1. Click Install Now and then activate the plugin
1. Now, go to the settings menu of the plugin and follow the instructions

== Frequently Asked Questions ==

= Can this plugin be used to send emails via SMTP? =

Yes.

== Screenshots ==

For screenshots please visit the [Easy WordPress SMTP](https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197) plugin page

== Other Notes ==

Inspired by [WP Mail SMTP](http://wordpress.org/plugins/wp-mail-smtp/) plugin


== Changelog ==

= 1.3.8 =
* Set reasonable timeout for SMTP server connection attempt. This prevents admin area from being locked up for too long if your SMTP server refuses connections.
* Added spinner to indicate that test email is being sent.
* "Send Test Email" button is now disabled if there are unsaved settings changes.
* Minor settings page adjustments.

= 1.3.7 =
* Renamed SSL and TLS to what they actually are.

= 1.3.6 =
* SMTP Username and SMTP Host fields are no longer multiplying slashes (thanks to jstepak for reporting).
* Added option to encrypt password using AES-256 encryption. This requires PHP 5.3+ and OpenSSL PHP extension to be enabled on the server.
* Added clear message to indicate if test email was successfully sent or not. Now you don't have to figure this out from debug log :-)
* Disabled browser autocomplete for username and password fields to prevent them from being replaced by WP login credentials (if those were saved in browser).
* Removed duplicate items IDs from settings page to comply with HTML standards.

= 1.3.5 =
* Added configurable option to force replace From Name. The plugin was force-replacing it regardless before, now you can configure this (thanks to daymobrew).

= 1.3.4 =
* Fixed "Allow Insecure SSL Certificates" option was ignored (thanks to bogesman).
* Added password gag explanation to SMTP Password field.
* Added Support Forum link.
* Some minor improvements to Settings page.

= 1.3.3 =
* Added option to allow insecure SSL certificate usage on SMTP server (thanks to ravipatel and bradclarke365).
* Changing fields on Test Email tab is no longer shows "you have unsaved settings" notice.
* Plugin is compatible again with WP version 4.3+ (thanks to lucrus for reporting).

= 1.3.2 =
* Hopefully fixed inability for plugin to save settings in some circumstances (thanks to all who kept reporting this issue).
* The plugin is no longer failing if PHP mbstring extension is not installed on the server.
* Settings page is using tabs now.
* Fixed default settings were not set upon plugin activation.
* Fixed some lines that couldn't be translated to other languages (thanks to jranavas).

= 1.3.1 =
* Fixed potential issue with passwords that had special characters.
* Check if variables are set before interacting with them (removes PHP notices when WP debug mode is enabled) (thanks to rubas and matward).
* Test email message body is no longer having excess slashes inserted (thanks to tdcsforeveryone).
* Added option for plugin to block ALL emails if Domain Check option enabled and domain check fails (thanks to erikmolenaar).

= 1.3.0 =
* Plugin will display an error message if log file is not writeable when "Clear Log" is clicked.
* Actual SMTP password is replaced by a gag on the settings page.
* Fixed minor bug in Reply-To option handling (thanks to arildur).
* Some improvements in developers-related options (thanks to erikmolenaar).

= 1.2.9 =
* Added additional setting option to deal with email aliases (thanks to bradclarke365).
* Fixed "Reply-To" option wasn't saving if it is blank.

= 1.2.8 =
* New settings option to specify a reply-to email address.
* There is a new settings option to enable debug logging.

= 1.2.7 =
* Added extra debug info (when test email function is used). This debug info will show more details if anything fails. This will be helpful to debug SMTP connection failure on your server.

= 1.2.6 =
* Some special characters in the password field were getting removed when the text sanitization was done. This has been improved.
* The settings configuration will not be deleted if you deactivated the plugin. This will prevent configuration loss if you accidentally deactivate the plugin.

= 1.2.5 =
* Fixed possible XSS vulnerability with the email subject and email body input fields.

= 1.2.4 =
* Improved the admin interface.
* The test email details now gets saved after you use it. So you don't need to type it every single time you want to send a test email.

= 1.2.3 =
* Easy WP SMTP is now compatible with WordPress 4.5.

= 1.2.2 =
* Easy WP SMTP is now compatible with WordPress 4.4.

= 1.2.1 =
* Set SMTPAutoTLS to false by default as it might cause issues if the server is advertising TLS with an invalid certificate.
* Display an error message near the top of admin pages if SMTP credentials are not configured.

= 1.2.0 =
* Set email charset to utf-8 for test email functionality.
* Run additional checks on the password only if mbstring is enabled on the server. This should fix the issue with password input field not appearing on some servers.

= 1.1.9 =
* Easy SMTP is now compatible with WordPress 4.3

= 1.1.8 =
* Easy SMTP now removes slashes from the "From Name" field.

= 1.1.7 =
* Made some improvements to the encoding option.

= 1.1.7 =
* Made some improvements to the encoding option.

= 1.1.6 =
* Fixed some character encoding issues of test email functionality
* Plugin will now force the from name and email address saved in the settings (just like version 1.1.1)

= 1.1.5 =
* Fixed a typo in the plugin settings
* SMTP Password is now encoded before saving it to the wp_options table

= 1.1.4 =
* Plugin will now also override the default from name and email (WordPress)

= 1.1.3 =
* Removed "ReplyTo" attribute since it was causing compatibility issues with some form plugins

= 1.1.2 =
* "ReplyTo" attribute will now be set when sending an email
* The plugin will only override "From Email Address" and "Name" if they are not present

= 1.1.1 =
* Fixed an issue where the plugin CSS was affecting other input fields on the admin side.

= 1.1.0 =
* "The settings have been changed" notice will only be displayed if a input field is changed

= 1.0.9 =
* Fixed some bugs in the SMTP configuration and mail functionality

= 1.0.8 =
* Plugin now works with WordPress 3.9

= 1.0.7 =
* Plugin now works with WordPress 3.8

= 1.0.6 =
* Plugin is now compatible with WordPress 3.7

= 1.0.5 =
* "Reply-To" text will no longer be added to the email header
* From Name field can now contain quotes. It will no longer be converted to '\'

= 1.0.4 =
* Plugin is now compatible with WordPress 3.6

= 1.0.3 =
* Added a new option to the settings which allows a user to enable/disable SMTP debug
= 1.0.2 =
* Fixed a bug where the debug output was being displayed on the front end

= 1.0.1 =
* First commit of the plugin

== Upgrade Notice ==
There were some major changes in version 1.0.8. So you will need to reconfigure the SMTP options after the upgrade.
