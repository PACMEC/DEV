<?php
// Handles options for subscribe2
// DO NOT EDIT THIS FILE AS IT IS SET BY THE OPTIONS PAGE

if ( ! isset( s2cp()->subscribe2_options['autosub'] ) ) {
	s2cp()->subscribe2_options['autosub'] = 'no';
} // option to autosubscribe registered users to new categories

if ( ! isset( s2cp()->subscribe2_options['newreg_override'] ) ) {
	s2cp()->subscribe2_options['newreg_override'] = 'no';
} // option to autosubscribe registered users to new categories

if ( ! isset( s2cp()->subscribe2_options['wpregdef'] ) ) {
	s2cp()->subscribe2_options['wpregdef'] = 'no';
} // option to check registration form box by default

if ( ! isset( s2cp()->subscribe2_options['autoformat'] ) ) {
	s2cp()->subscribe2_options['autoformat'] = 'post';
} // option for default auto-subscription email format

if ( ! isset( s2cp()->subscribe2_options['show_autosub'] ) ) {
	s2cp()->subscribe2_options['show_autosub'] = 'yes';
} // option to display auto-subscription option to registered users

if ( ! isset( s2cp()->subscribe2_options['autosub_def'] ) ) {
	s2cp()->subscribe2_options['autosub_def'] = 'no';
} // option for user default auto-subscription to new categories

if ( ! isset( s2cp()->subscribe2_options['comment_subs'] ) ) {
	s2cp()->subscribe2_options['comment_subs'] = 'no';
} // option for commenters to subscribe as public subscribers

if ( ! isset( s2cp()->subscribe2_options['comment_def'] ) ) {
	s2cp()->subscribe2_options['comment_def'] = 'no';
} // option for comments box to be checked by default

if ( ! isset( s2cp()->subscribe2_options['one_click_profile'] ) ) {
	s2cp()->subscribe2_options['one_click_profile'] = 'no';
} // option for displaying 'one-click' option on profile page

if ( ! isset( s2cp()->subscribe2_options['bcclimit'] ) ) {
	s2cp()->subscribe2_options['bcclimit'] = 1;
} // option for default bcc limit on email notifications

if ( ! isset( s2cp()->subscribe2_options['admin_email'] ) ) {
	s2cp()->subscribe2_options['admin_email'] = 'subs';
} // option for sending new subscriber notifications to admins

if ( ! isset( s2cp()->subscribe2_options['tracking'] ) ) {
	s2cp()->subscribe2_options['tracking'] = '';
} // option for tracking

if ( ! isset( s2cp()->subscribe2_options['s2page'] ) ) {
	s2cp()->subscribe2_options['s2page'] = 0;
} // option for default ClassicPress page for Subscribe2 to use

if ( ! isset( s2cp()->subscribe2_options['stylesheet'] ) ) {
	s2cp()->subscribe2_options['stylesheet'] = 'yes';
} // option to include link to theme stylesheet from HTML notifications

if ( ! isset( s2cp()->subscribe2_options['embed'] ) ) {
	s2cp()->subscribe2_options['embed'] = 'no';
} // option to embed stylesheet and images into HTML emails

if ( ! isset( s2cp()->subscribe2_options['pages'] ) ) {
	s2cp()->subscribe2_options['pages'] = 'no';
} // option for sending notifications for Pages

if ( ! isset( s2cp()->subscribe2_options['password'] ) ) {
	s2cp()->subscribe2_options['password'] = 'no';
} // option for sending notifications for posts that are password protected

if ( ! isset( s2cp()->subscribe2_options['stickies'] ) ) {
	s2cp()->subscribe2_options['stickies'] = 'no';
} // option for including sticky posts in digest notifications

if ( ! isset( s2cp()->subscribe2_options['private'] ) ) {
	s2cp()->subscribe2_options['private'] = 'no';
} // option for sending notifications for posts that are private

if ( ! isset( s2cp()->subscribe2_options['email_freq'] ) ) {
	s2cp()->subscribe2_options['email_freq'] = 'never';
} // option for sending emails per-post or as a digest email on a cron schedule

if ( ! isset( s2cp()->subscribe2_options['cron_order'] ) ) {
	s2cp()->subscribe2_options['cron_order'] = 'desc';
} // option for ordering of posts in digest email

if ( ! isset( s2cp()->subscribe2_options['compulsory'] ) ) {
	s2cp()->subscribe2_options['compulsory'] = '';
} // option for compulsory categories

if ( ! isset( s2cp()->subscribe2_options['exclude'] ) ) {
	s2cp()->subscribe2_options['exclude'] = '';
} // option for excluded categories

if ( ! isset( s2cp()->subscribe2_options['sender'] ) ) {
	s2cp()->subscribe2_options['sender'] = 'blogname';
} // option for email notification sender

if ( ! isset( s2cp()->subscribe2_options['reg_override'] ) ) {
	s2cp()->subscribe2_options['reg_override'] = '1';
} // option for excluded categories to be overriden for registered users

if ( ! isset( s2cp()->subscribe2_options['show_meta'] ) ) {
	s2cp()->subscribe2_options['show_meta'] = '0';
} // option to display link to subscribe2 page from 'meta'

if ( ! isset( s2cp()->subscribe2_options['show_button'] ) ) {
	s2cp()->subscribe2_options['show_button'] = '1';
} // option to show Subscribe2 button on Write page

if ( ! isset( s2cp()->subscribe2_options['ajax'] ) ) {
	s2cp()->subscribe2_options['ajax'] = '0';
} // option to enable an AJAX style form

if ( ! isset( s2cp()->subscribe2_options['widget'] ) ) {
	s2cp()->subscribe2_options['widget'] = '1';
} // option to enable Subscribe2 Widget

if ( ! isset( s2cp()->subscribe2_options['counterwidget'] ) ) {
	s2cp()->subscribe2_options['counterwidget'] = '0';
} // option to enable Subscribe2 Counter Widget

if ( ! isset( s2cp()->subscribe2_options['s2meta_default'] ) ) {
	s2cp()->subscribe2_options['s2meta_default'] = '0';
} // option for Subscribe2 over ride postmeta to be checked by default

if ( ! isset( s2cp()->subscribe2_options['barred'] ) ) {
	s2cp()->subscribe2_options['barred'] = '';
} // option containing domains barred from public registration

if ( ! isset( s2cp()->subscribe2_options['exclude_formats'] ) ) {
	s2cp()->subscribe2_options['exclude_formats'] = '';
} // option for excluding post formats as supported by the current theme

if ( ! isset( s2cp()->subscribe2_options['mailtext'] ) ) {
	s2cp()->subscribe2_options['mailtext'] = __( "{BLOGNAME} has posted a new item, '{TITLE}'\n\n{POST}\n\nYou may view the latest post at\n{PERMALINK}\n\nYou received this e-mail because you asked to be notified when new updates are posted.\nBest regards,\n{MYNAME}\n{EMAIL}", 'subscribe2-for-cp' );
} // Default notification email text

if ( ! isset( s2cp()->subscribe2_options['notification_subject'] ) ) {
	s2cp()->subscribe2_options['notification_subject'] = '[{BLOGNAME}] {TITLE}';
} // Default notification email subject

if ( ! isset( s2cp()->subscribe2_options['confirm_email'] ) ) {
	s2cp()->subscribe2_options['confirm_email'] = __( "{BLOGNAME} has received a request to {ACTION} for this email address. To complete your request please click on the link below:\n\n{LINK}\n\nIf you did not request this, please feel free to disregard this notice!\n\nThank you,\n{MYNAME}.", 'subscribe2-for-cp' );
} // Default confirmation email text

if ( ! isset( s2cp()->subscribe2_options['confirm_subject'] ) ) {
	s2cp()->subscribe2_options['confirm_subject'] = '[{BLOGNAME}] ' . __( 'Please confirm your request', 'subscribe2-for-cp' );
} // Default confirmation email subject

if ( ! isset( s2cp()->subscribe2_options['remind_email'] ) ) {
	s2cp()->subscribe2_options['remind_email'] = __( "This email address was subscribed for notifications at {BLOGNAME} ({BLOGLINK}) but the subscription remains incomplete.\n\nIf you wish to complete your subscription please click on the link below:\n\n{LINK}\n\nIf you do not wish to complete your subscription please ignore this email and your address will be removed from our database.\n\nRegards,\n{MYNAME}", 'subscribe2-for-cp' );
} // Default reminder email text

if ( ! isset( s2cp()->subscribe2_options['remind_subject'] ) ) {
	s2cp()->subscribe2_options['remind_subject'] = '[{BLOGNAME}] ' . __( 'Subscription Reminder', 'subscribe2-for-cp' );
} // Default reminder email subject

if ( ! isset( s2cp()->subscribe2_options['ajax'] ) ) {
	s2cp()->subscribe2_options['ajax'] = '';
} // Default frontend form setting

if ( ! isset( s2cp()->subscribe2_options['js_ip_updater'] ) ) {
	s2cp()->subscribe2_options['js_ip_updater'] = '';
} // Default setting for using javascript to update form ip address

if ( ! isset( s2cp()->subscribe2_options['dismiss_sender_warning'] ) ) {
	s2cp()->subscribe2_options['dismiss_sender_warning'] = '';
} // Default for sender warning message dismissal

