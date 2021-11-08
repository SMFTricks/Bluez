<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;

	/*Theme changer*/
	$settings['theme_variants'] = array('default', 'green', 'red');
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?fin20" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tools/1.2.7/jquery.tools.min.js"></script>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
    $context['url_logo'] = $settings['images_url'].'/logo.png';
	echo '
<div id="wrapper">
	<div id="header">
		<div class="wrapper">
		<div id="branding">
			<a href="', $scripturl, '"><img src="', !empty($settings['header_logo_url']) ? $settings['header_logo_url'] : $context['url_logo'], '" alt="' . $context['forum_name'] . '" /></a>
			<div id="userinf" class="smalltext">';
		
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
		{
			echo '
				<a href="', $scripturl, '?action=profile"><img class="ava_bar" src="', $context['user']['avatar']['href'], '" alt="" /></a>';
		}
		else {
			echo '
				<a href="', $scripturl, '?action=profile"><img class="ava_bar" src="', $settings['images_url'], '/theme/avatardefault.png" alt="" /></a>';
			}
				
		echo '
			<span class="greeting"><a href="', $scripturl, '?action=profile">', $context['user']['name'], '</a></span>
			<div id="userbar">
				<ul class="tabs">
					<li><a class="current" href="', $scripturl, '?action=profile;area=forumprofile"><img src="', $settings['images_url'], '/theme/icons/icon2.png" alt="" /></a></li>
					<li><a href="', $scripturl, '?action=profile;area=account"><img src="', $settings['images_url'], '/theme/icons/icon3.png" alt="" /></a></li>
					<li style="cursor:default"><a href="#"><img src="', $settings['images_url'], '/theme/icons/icon4.png" alt="" /></a></li>
					<li><a href="', $scripturl, '?action=unread"><img src="', $settings['images_url'], '/theme/icons/icon5.png" alt="" /></a></li>
					<li><a href="', $scripturl, '?action=unreadreplies"><img src="', $settings['images_url'], '/theme/icons/icon6.png" alt="" /></a></li>
				</ul>
				<div class="panes">
					<div style="display: block;">', $txt['forumprofile'], '</div>
					<div style="display: none;">', $txt['account'], '</div>
					<div style="display: none;">';
						// Show the total time logged in?
						if (!empty($context['user']['total_time_logged_in']))
						{
							echo $txt['totalTimeLogged1'], '<br /> ';

							// If days is just zero, don't bother to show it.
							if ($context['user']['total_time_logged_in']['days'] > 0)
								echo $context['user']['total_time_logged_in']['days'], $txt['totalTimeLogged2'], '';

							// Same with hours - only show it if it's above zero.
							if ($context['user']['total_time_logged_in']['hours'] > 0)
								echo $context['user']['total_time_logged_in']['hours'], $txt['totalTimeLogged3'], '';
							
							// Same with minutes - only show it if it's above zero.
							if ($context['user']['total_time_logged_in']['minutes'] > 0)
								echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'], '';
						}

						echo '
					</div>
					<div style="display: none;">', $txt['unread_topics_visit'], '</div>
					<div style="display: none;">', $txt['unread_replies'], '</div>
				</div>
				<script type="text/javascript">
					$(function() {
						// setup ul.tabs to work as tabs for each div directly under div.panes
						$("ul.tabs").tabs("div.panes > div",{event:\'mouseover\'});
					});
				</script>
			</div>';
			
			}
			else {		
		echo '
		<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
			<input class="input_text2" type="text" size="10" name="user" value="', $txt['username'], '" onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\'', $txt['username'], '\';" class="input_text" />
			<input class="input_password2" type="password" size="10" name="passwrd" value="', $txt['password'], '" onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\'', $txt['password'], '\';" class="input_text" />
			<input class="button_submit2" type="submit" value="', $txt['login'], '" /><br />
			<input type="hidden" name="hash_passwrd" value="" /><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>';				
	}
	echo '
		</div>
	</div>
			', template_menu() ,'			
		</div>
	</div>';

	// The main content should go here.
	echo '
	<div class="wrapper"><div id="content_section">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div class="wrapper"><div id="footer">	
	<table width="100%" cellspacing="0">
	  <tr>
	    <td width="47,5%">
		<ul class="reset">
			<li class="copyright">', theme_copyright(), '</li>
			<li class="copyright">Theme by <a href="https://smftricks.com/">SMF Tricks</a></li>
			<li><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
			<li class="last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';
	echo'	   
	    </td>
		<td width="5%" align="center">
		    <a class="backtop" href="#"></a>
		</td>
		<td width="47,5%">';

	if(!empty($settings['icons_check']))
	{
	echo'
		<ul class="social_icons">
		        <li>&nbsp;</li>';
		    if(!empty($settings['facebook_check']))
		    echo'
		 	    <li class="facebook"><a href="', !empty($settings['facebook_text']) ? $settings['facebook_text'] : 'https://www.facebook.com ' ,'"><img src="', $settings['images_url'], '/social_icons/facebook.png" alt="', $txt['rs_facebook'], '" /></a></li>';
		    if(!empty($settings['twitter_check']))
		    echo'
			    <li class="twitter"><a href="', !empty($settings['twitter_text']) ? $settings['twitter_text'] : 'https://www.twitter.com' ,'"><img src="', $settings['images_url'], '/social_icons/twitter.png" alt="', $txt['rs_twitter'], '" /></a></li>';
		    if(!empty($settings['youtube_check']))
		    echo'
			    <li class="youtube"><a href="', !empty($settings['youtube_text']) ? $settings['youtube_text'] : 'https://www.youtube.com' ,'"><img src="', $settings['images_url'], '/social_icons/youtube.png" alt="', $txt['rs_youtube'], '" /></a></li>';
		    if(!empty($settings['rss_check']))
		    echo'
			    <li class="rss"><a href="', !empty($settings['rss_text']) ? $settings['rss_text'] : '?action=.xml;type=rss' ,'"><img src="', $settings['images_url'], '/social_icons/rss.png" alt="', $txt['rs_rss'], '" /></a></li>';
	echo'
		</ul>';		
	}
		echo'
		</td>
	  </tr>
	</table>
	</div></div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			', ($link_num == count($context['linktree']) - 1) ? '<li class="last"><div class="lt_wrapper"><div class="lt_last"></div><div class="lt_midle">' : '<li><div class="lt_wrapper"><div class="lt_last"></div><div class="lt_midle">', '';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '';

		echo '
			</div><div class="lt_first"></div></div></li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}


// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="menu">
			<div id="search">
				<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="', $txt['search'], '..." onfocus="if (this.value == \'', $txt['search'], '...\') this.value = \'\';" onblur="if (this.value == \'\') this.value = \'', $txt['search'], '...\';" class="input_text" />&nbsp;
					<input type="hidden" name="advanced" value="0" />';
	            if (!empty($context['current_topic']))
		            echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	            elseif (!empty($context['current_board']))
		            echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
	    echo '
		        </form>
				<div id="colorpicker">
					<ul class="colorpicker">
						<li><a href="#"><img src="'. $settings['images_url'] .'/colorpicker.png" alt="" />&nbsp;</a>
							<ul class="subcolor">
								<li><a href="'.$scripturl.'?variant=default">Default</a></li>
								<li><a href="'.$scripturl.'?variant=green">Green</a></li>
								<li><a href="'.$scripturl.'?variant=red">Red</a></li>				
							</ul>						
						</li>
					</ul>
				</div>
	        </div>
			<ul>';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						', $button['title'], '
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										', $grandchildbutton['title'], '
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>