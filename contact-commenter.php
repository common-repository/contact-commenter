<?php

	/*
	Plugin Name: Contact Commenter
	Plugin URI: http://www.moallemi.ir/en/blog/2009/09/20/contact-commenter/
	Description: This Plugin lets you send email messages to individual or a group of commenters. 
	Version: 0.8
	Author: Reza Moallemi
	Author URI: http://www.moallemi.ir/blog
	*/

	load_plugin_textdomain('contact-commenter', NULL, dirname(plugin_basename(__FILE__)) . "/languages");

	add_action('admin_menu', 'contact_commenter_menu');

	function contact_commenter_menu() 
	{
		add_submenu_page('edit-comments.php', 'Contact Commenter Options', __('Contact Commenter', 'contact-commenter'), 10, 'email-form' , 'contact_commenter_options');
	}

	function get_contact_commenter_options()
	{
		$contact_commenter_options = array();
		$auad_save_options = get_option('contact_commenter_options');
		if (!empty($auad_save_options))
		{
			foreach ($auad_save_options as $key => $option)
			$contact_commenter_options[$key] = $option;
		}
		update_option('contact_commenter_options', $contact_commenter_options);
		return $contact_commenter_options;
	}

	function contact_commenter_options()
	{
		
		if ($_POST["to"] != "") {
			$subject = $_POST["subject"];
			$name = $_POST["name"];
			$from = $_POST["from"];
			$body = $_POST["body"];
			$body = str_replace('\"', '"', $body);
			$cc = $_POST["cc"];
			$bcc = $_POST["bcc"];
			$reply = $_POST["reply"];
			$to = preg_split("/,/", $_POST["to"], -1, PREG_SPLIT_NO_EMPTY);
			$sent = "";
			foreach ($to as $to) {
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8;\r\n";
				$headers .= 'From: ' . $name . ' <' . $from . ">\r\n";
				$headers .= 'Cc: ' . $cc .  "\r\n";
				$headers .= 'Bcc: ' . $bcc .  "\r\n";
				$headers .= 'Reply-To: ' . $reply .  "\r\n"; 
				wp_mail($to,$subject,$body,$headers);
				$sent .= $to . '<br />';
			}
			echo "<div class='updated fade'><b>";
			_e('Email was sent to:', 'contact-commenter');
			echo "</b><br />".$sent."</div>";
		}

		?>
		<div class="wrap">
		<?php if(function_exists('screen_icon')) screen_icon(); ?>
		<?php 
			global $current_user;
			get_currentuserinfo();
		?>
		<h2><?php _e('Contact Commenter Form', 'contact-commenter'); ?>
		</h2>
		<form id="form" method="post">

		<style>
		td {padding:5px;}
		.address tr { background-color:#FFF;}
		.address tr:hover {background-color:#FFC;}
		.address {overflow:auto; display:block; border:solid 1px #f0f0f0;}
		.cc-input {width:250px; direction:ltr;}
		#edButtonPreview, #edButtonHTML {
		background-color:#F1F1F1;
		border-color:#DFDFDF;
		color:#999999;
		float:left;
		margin:2px 5px 0 0;
		}
		#editor-toolbar .active {
		background-color:#E9E9E9;
		border-bottom-color:#E9E9E9;
		color:#333333;
		}
		#body {width: 100%;}
		</style>
			<table >
				<tr>
					<td><?php _e('Name:', 'contact-commenter'); ?></td>
					<td><input name="name" class="cc-input" style="direction:rtl;" id="name" value="<?php echo $current_user->display_name; ?>" /> <?php _e('(Required)', 'contact-commenter'); ?></td>
				</tr>
				<tr>
					<td><?php _e('From:', 'contact-commenter'); ?></td>
					<td><input name="from" class="cc-input" id="from" value="<?php echo $current_user->user_email; ?>" /> <?php _e('(Required)', 'contact-commenter'); ?> <small><?php _e('e.g. someone@example.com', 'contact-commenter'); ?></small></td>
				</tr>
				<tr>
					<td valign="top"><?php _e('To:', 'contact-commenter'); ?></td>
					<td>
						<table class="widefat">
						<thead>
							<tr >
								<th><input type="checkbox" onclick="selectall()"/></th>
								<th width="23%"><b><?php _e('Commenter', 'contact-commenter'); ?></b></th>
								<th width="40%"><b><?php _e('Email', 'contact-commenter'); ?></b></th>
								<th width="40%"><b><?php _e('URL', 'contact-commenter'); ?></b></th>
								<th width="5%"><b><?php _e('Comment Count', 'contact-commenter'); ?></b></th>
							</tr>
						  </thead>
						</table>
						<table height="200" class="widefat address" id="address">
						<?php
						global $wpdb;
						$count = 0;
						$db = $wpdb->get_results("select comment_author, comment_author_email, comment_author_url, COUNT(comment_ID) AS cnt from $wpdb->comments GROUP BY comment_author_email ORDER BY cnt DESC");
						foreach ($db as $db):?>

						  <tr>
							<td><input type="checkbox" value="<?php echo $db->comment_author_email;?>" name=""/></td>
							<td width="23%"><?php echo $db->comment_author;?></td>
							<td width="30%" dir="ltr"><?php echo $db->comment_author_email;?></td>
							<td width="40%" dir="ltr"><a target="_blank" href="<?php echo $db->comment_author_url;?>"><?php echo $db->comment_author_url;?></a></td>
							<td width="5%"><?php echo $db->cnt;?></td>
						  </tr>

						  <?php endforeach;?>

						</table><br />
						<small><b><?php echo mysql_affected_rows();?></b> <?php _e('People Wrote commentes on your blog.', 'contact-commenter'); ?></small>
					</td>
				</tr>
				<tr>
					<td><?php _e('Subject:', 'contact-commenter'); ?></td><td><input style="direction:rtl;" class="cc-input" name="subject" id="subject" style="font-size:20px" size="40"/> <?php _e('(Required)', 'contact-commenter'); ?></td>
				</tr>
				<tr>
					<td valign="top"><?php _e('Body:', 'contact-commenter'); ?></td>
					<td>
						<div >
						<?php the_editor('', 'body'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php _e('Reply-to:', 'contact-commenter'); ?> </td><td><input class="cc-input" name="reply" id="reply" /> </td>
				</tr>
				<tr>
					<td><?php _e('Cc:', 'contact-commenter'); ?></td><td><input class="cc-input" name="cc" id="cc" /></td>
				</tr>
				<tr>
					<td><?php _e('Bcc:', 'contact-commenter'); ?></td><td><input class="cc-input" name="bcc" id="bcc" /></td>
				</tr>
			</table>
		<input type="hidden" id="to" name="to" />
		<div><p>
		<input class="button-primary" type="submit" value="<?php _e('Send Emails', 'contact-commenter'); ?>" />
		</p></div>
		<hr />
						<div>
							<h4><?php _e('My other plugins for wordpress:', 'contact-commenter'); ?></h4>
							<ul>
								<li><font color="red"><b> - <?php _e('Google Reader Stats ', 'contact-commenter'); ?></b></font>
									(<a href="http://wordpress.org/extend/plugins/google-reader-stats/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="http://www.moallemi.ir/blog/1389/04/12/%d9%86%d8%b3%d8%ae%d9%87-%d8%ac%d8%af%db%8c%d8%af-%d8%a2%d9%85%d8%a7%d8%b1-%da%af%d9%88%da%af%d9%84-%d8%b1%db%8c%d8%af%d8%b1-%d9%88%d8%b1%d8%af%d9%be%d8%b1%d8%b3-3-%d9%84%d8%a7%db%8c%da%a9/"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
								<li><font color="red"><b> - <?php _e('Likekhor ', 'contact-commenter'); ?></b></font>
									(<a href="http://wordpress.org/extend/plugins/wp-likekhor/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="http://www.moallemi.ir/blog/1389/04/30/%D9%85%D8%B9%D8%B1%D9%81%DB%8C-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%84%D8%A7%DB%8C%DA%A9-%D8%AE%D9%88%D8%B1-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
								<li><b>- <?php _e('Google Transliteration ', 'contact-commenter'); ?></b> 
									(<a href="http://wordpress.org/extend/plugins/google-transliteration/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/10/10/google-transliteration-for-wordpress/', 'contact-commenter'); ?>"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
								<li><b>- <?php _e('Advanced User Agent Displayer ', 'contact-commenter'); ?></b>
									(<a href="http://wordpress.org/extend/plugins/advanced-user-agent-displayer/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/09/20/advanced-user-agent-displayer/', 'contact-commenter'); ?>"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
								<li><b>- <?php _e('Behnevis Transliteration ', 'contact-commenter'); ?></b> 
									(<a href="http://wordpress.org/extend/plugins/behnevis-transliteration/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="http://www.moallemi.ir/blog/1388/07/25/%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%86%D9%88%DB%8C%D8%B3%D9%87-%DA%AF%D8%B1%D8%AF%D8%A7%D9%86-%D8%A8%D9%87%D9%86%D9%88%DB%8C%D8%B3-%D8%A8%D8%B1%D8%A7%DB%8C-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/"><?php _e('More Information', 'contact-commenter'); ?></a> )
								</li>
								<li><b>- <?php _e('Comments On Feed ', 'contact-commenter'); ?></b> 
									(<a href="http://wordpress.org/extend/plugins/comments-on-feed/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/12/18/comments-on-feed-for-wordpress/', 'contact-commenter'); ?>"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
								<li><b>- <?php _e('Feed Delay ', 'contact-commenter'); ?></b> 
									(<a href="http://wordpress.org/extend/plugins/feed-delay/"><?php _e('Download', 'contact-commenter'); ?></a> | 
									<a href="<?php _e('http://www.moallemi.ir/en/blog/2010/02/25/feed-delay-for-wordpress/', 'contact-commenter'); ?>"><?php _e('More Information', 'contact-commenter'); ?></a>)
								</li>
							</ul>
						</div>
		</form>


		<script>
		var checked = false;
		function selectall() {
			checked = !checked;
			input = document.getElementById("address").getElementsByTagName("input");
			for (i=0;i<input.length;i++) {
				input[i].checked = checked;
			}
		}
		form = document.getElementById("form");
		form.onsubmit = function () {
			if( document.getElementById("name").value == "" || 
				document.getElementById("from").value == "" ||
				document.getElementById("subject").value == "" )
			{
				alert('<?php _e('Please fill in all required fields.', 'contact-commenter'); ?>');
				return false;
			}
			to = "";
			address = document.getElementById("address").getElementsByTagName("input");
			for (i=0;i<address.length;i++) {
				if (address[i].checked) to+=address[i].value+",";
			}
			input = document.getElementById("to");
			input.value = to;
		}
		</script>



		</div>
				<?php
	}
	
	$currbasename = (isset($_GET['page'])) ? $_GET['page'] : ''; 
	if ($currbasename == 'email-form') 
		add_filter('admin_head', 'cc_showTinyMCE');
	
	function cc_showTinyMCE()
	{
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'jquery-color' );
		wp_print_scripts('editor');
		if (function_exists('add_thickbox')) add_thickbox();
		wp_print_scripts('media-upload');
		if (function_exists('wp_tiny_mce')) wp_tiny_mce();
		wp_admin_css();
		wp_enqueue_script('utils');
		do_action("admin_print_styles-post-php");
		do_action('admin_print_styles');
	}
	
	function cc_admin_private_text($text){
		global $comment;
		
		$comment_type = strtolower(trim($comment->comment_type));
		if($comment_type === 'pingback' || $comment_type === 'trackback')
			return $text;
		unset($comment_type);

		$text .= '<p>[ <a href="javascript:void(0)" onclick="cc_private_reply(event,\'' . $comment->comment_author_email . '\');">'. __('Private Reply','contact-commenter') . '</a> ]</p>';

		return $text;
	}
	

	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'contact_commenter_links' );
	
	
	if(is_admin())
	{
		require_once (ABSPATH . WPINC . '/pluggable.php');
		global $current_user;
		get_currentuserinfo();
		if($current_user->user_level >= 8)
		{
			add_action('admin_footer', 'cc_admin_form_private', 9999);
			add_filter('comment_text', 'cc_admin_private_text', 9999);
		}
	}
	
	function cc_admin_form_private()
	{
	?>
	<form id="privatereply" method="post" action="<?php echo get_option('siteurl'); ?>/wp-content/plugins/contact-commenter/contact-commenter-mail.php" style="display:none" onsubmit="return cc_private_send()">
		<p>
		<input style="width:350px;" name="ccp_title" id="ccp_title" type="text" value="<?php _e('Email Title', 'contact-commenter'); ?>" onclick="if(document.getElementById('ccp_title').value == '<?php _e('Email Title', 'contact-commenter'); ?>') document.getElementById('ccp_title').value = '';"/> <br />
		<textarea id="ccp_comment" name="ccp_comment" style="margin-top:1em;" cols="50" rows="5"></textarea><br />
		<input name="submitpcomment" id="submitpcomment" value="<?php _e('Post Private Reply', 'contact-commenter'); ?>" type="submit" />
		<input name="cancel" id="cancel" value="<?php _e('Cancel', 'contact-commenter'); ?>" type="button" onclick="javascript:document.getElementById('ccp_reply_email').value='';document.getElementById('privatereply').style.display='none'" />
		<input type="hidden" id="ccp_reply_email" name="ccp_reply_email" value="" />
		</p>
	</form>
	<script type="text/javascript" src="<?php echo get_option('siteurl')."/wp-content/plugins/contact-commenter/contact-commenter.js.php"; ?>"></script>
	<?php
	}
	
	function contact_commenter_links($links)
	{ 
		$settings_link = '<a href="options-general.php?page=contact-commenter">'.__('Settings', 'contact-commenter').'</a>';
		array_unshift($links, $settings_link); 
		return $links; 
	}

?>
