<?php
	
if(strpos(getcwd(),'wp-content/plugins/rss_atom_avatar'))
	die('Error: Plugin does not support standalone calls, damned hacker.');
DEFINE(RSSATOMAVATAR_VERSION,'0.01');
/*
Plugin Name: Rss Atom Avatar
Plugin URI: http://jehy.ru/wp-plugins.en.html
Description: Add an image and favicon to your RSS and Atom feeds! To set up, visit <a href="options-general.php?page=rss-atom-avatar/rss-atom-avatar.php">configuration panel</a>.
Version: 0.01
Author: Jehy
Author URI: http://jehy.ru/index.en.html
Min WP Version: 2.5
Max WP Version: 2.6.3
*/

/*  Copyright 2008  Jehy  (email : jehy@valar.ru)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function addRssImage()
{RssAtomAvatar_addImage('rss');}
function addAtomImage()
{RssAtomAvatar_addImage('atom');}
function RssAtomAvatar_addImage($type) 
{
$logo = get_option('feed_logo');
if($type=='rss')
{
	$logo_w = get_option('feed_logo_w');
	$logo_h = get_option('feed_logo_h');
	if($logo)
	{
		echo '<image><title>'.get_bloginfo('name').'</title><url>'.$logo. '</url><link>'.get_bloginfo('url').'</link>';
		if($logo_w)
			echo '<width>'.$logo_w.'</width>';
		if($logo_h)
			echo '<height>'.$logo_h .'</height>';
		echo '<description>'.get_bloginfo('name').' - '.get_bloginfo('url').'</description></image>';
	}
}
elseif($type=='atom')
{
	$icon = get_option('feed_icon');
 	if($logo)echo '<logo>'.$logo.'</logo>';
 	if($icon)echo '<icon>'.$icon .'</icon>';
}
}


function RssAtomAvatar_Activate()
{
add_option('feed_logo','', 'Feed Logo');
add_option('feed_logo_w','', 'Feed Width');
add_option('feed_logo_h','', 'Feed Height');
add_option('feed_icon', '', 'Feed Icon');
			
$logo_filename = '';
$logo = get_bloginfo('url') . '/feed-logo.png';
if(file_exists($logo))
{
	$param=@getimagesize($logo);
	if($param)
	{
		update_option('feed_logo', $logo);
		update_option('feed_logo_w', $param[0]);
		update_option('feed_logo_h', $param[1]);
	}
}
	
$icon = get_bloginfo('url').'/favicon.ico';
if(file_exists($icon))
	update_option('feed_icon',$icon);
}

function RssAtomAvatar_DeActivate()
{
	delete_option('feed_icon');
	delete_option('feed_logo');
	delete_option('feed_logo_w');
	delete_option('feed_logo_h');
}

register_activation_hook(__FILE__,'RssAtomAvatar_Activate');
register_deactivation_hook(__FILE__,'RssAtomAvatar_DeActivate');


function RssAtomAvatar_admin_options(){
global $_REQUEST;
	echo '<div class="wrap"><h2>Rss Atom Avatar Options</h2>';
	if($_REQUEST['submit'])
		update_RssAtomAvatar_options();
	print_RssAtomAvatar_form();	
	echo '</div>';
}

function RssAtomAvatar_modify_menu(){
	add_options_page(
		'Rss Atom Avatar',
		'Rss Atom Avatar',
		'manage_options',
		__FILE__,
		'RssAtomAvatar_admin_options'
		);
}


function update_RssAtomAvatar_options()
{
global $_REQUEST;
	$ok = true;
	
	if(isset($_REQUEST['feed_icon']))
	{
		$icon_file = $_REQUEST['feed_icon'];
		update_option('feed_icon', $icon_file);
	}
	if(isset($_REQUEST['feed_logo']))
	{
		$logo = $_REQUEST['feed_logo'];
		update_option('feed_logo', $logo);
		if($_REQUEST['try_autodetect'])
		{
			$param=@getimagesize($logo);
			if($param)
			{
				update_option('feed_logo_w', $param[0]);
				update_option('feed_logo_h', $param[1]);
			}
			else echo 'Error: Failed to get logo image size...';
		}
		else
		{
			if(isset($_REQUEST['feed_logo_w']))
				update_option('feed_logo_w', $_REQUEST['feed_logo_w']);
			if(isset($_REQUEST['feed_logo_h']))
				update_option('feed_logo_h', $_REQUEST['feed_logo_h']);
		}
	}
	
	if(!$ok)
		echo '<div id="message" class="error fade"><p>Failed to save options</p></div>';
	else 
		echo '<div id="message" class="update fade"><p>Options Saved</p></div>';
	
		
}

function print_RssAtomAvatar_form()
{
global $_REQUEST;
	$feed_icon=get_option('feed_icon');
	$feed_logo=get_option('feed_logo');
	$feed_logo_w=get_option('feed_logo_w');
	$feed_logo_h=get_option('feed_logo_h');
	?>
	<form method="post" action="<?php echo $location;?>">
		<div style="float:right;margin-right:2em;">
			<b>Rss Atom Avatar <?php echo RSSATOMAVATAR_VERSION;?></b><br>
			<a href="http://jehy.ru/wp-plugins.en.html" target="_blank">Plugin Homepage</a><br />
			<a href="http://jehy.ru/articles/2008/10/11/wordpress-plugin-for-images-in-rss-and-atom-feeds/" target="_blank">Feedback</a>
		</div>
		<div class="form-table" style="width:70%; border:1px solid #666; padding:10px; background-color:#CECECE;">
		<strong>Feed Icon</strong><br> (full url, probably beginning from <?php echo get_bloginfo('url'); ?>):
			<input type="text" name="feed_icon" value="<?php echo $feed_icon; ?>" size="35" /><br />
			<em>Use an icon file (ico)</em>
		</p>
		<p style="margin-left: 25px; color: rgb(85, 85, 85); font-size: 0.85em;">
			<strong style="color:#000000;">RSS has specifications about logo height and width</strong><br />
			Maximum value for width is <em>144</em>, default value is <em>88</em>.<br />
			Maximum value for height is <em>400</em>, default value is <em>31</em>.
		</p><hr>
		<p>
		<strong>Feed Logo</strong><br>(full url, probably beginning from <?php echo get_bloginfo('url'); ?>):
			<input type="text" name="feed_logo" value="<?php echo $feed_logo; ?>" size="35" /><br>
			<em>Use an image file (jpg, png, gif)</em>
		</p>
		<p><strong>Image Dimensions</strong>:<br> Width: <input type="text" name="feed_logo_w" value="<?php echo $feed_logo_w; ?>" size="5" />px | Height: <input type="text" name="feed_logo_h" value="<?php echo $feed_logo_h; ?>" size="5" />px</p>
		<p><strong>Try to auto detect</strong>: <input type="checkbox" checked name="try_autodetect" value="1"></p>
		<p>
		<input type="submit" name="submit" value="<?php _e('Save Changes') ?>">
		</div></form>
	<?php
}

add_action('admin_menu', 'RssAtomAvatar_modify_menu');

add_action('rss_head', 'addRssImage');
add_action('rss2_head', 'addRssImage');
add_action('atom_head', 'addAtomImage');

?>