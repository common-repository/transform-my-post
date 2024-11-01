=== Transform My Post ===
Contributors: danieltj
Tags: post, page, edit, post type, admin
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 2.0
License: GNU GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Transform your posts into different post types.

== Description ==

Transforming the post type of your posts, pages and custom post types can be very manual and go wrong. Now you can do it super easily without copying content or altering the database.

= Developers =

For information regarding the hooks and integrations in this plugin, refer to the wiki on the [GitHub repository](https://github.com/danieltj27/Transform-My-Post/wiki).

== Installation ==

1. Download, unzip and upload the package to your plugins directory.
2. Log into the dashboard and activate within the plugins page.
3. Edit any post and change the option under the Post Type meta box.

== Frequently Asked Questions ==

= How do I transform a post or page? =

Edit the post and look for the meta box titled Post Types. That will have an option where you can select any post type you want.

= What happens to my post data that is unsupported by some post types like revisions? =

Your data is kept safe even when changing post types. If it is unsupported, it won't be used unless you decide to switch the post type back in case you change your mind.

= Can I choose which post types can be changed? =

Yes, you can use the `transformable_post_types` filter hook which passes an array of post types that can be changed on the post edit screen.

= I've made a mistake, can I revert my changes? =

Yes, if you've accidentally changed the post type of something, all you need to do is edit the post again and switch the post type back.

== Screenshots ==

1. The transform setting within the dashboard.

== Changelog ==

Refer to the [GitHub repository](https://github.com/danieltj27/Transform-My-Post) for information on version history.
