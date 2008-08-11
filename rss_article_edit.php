<?php
# --- BEGIN PLUGIN META ---
$plugin=array(
'name'=>'rss_article_edit',
'version'=>'0.1',
'author'=>'Rob Sable',
'author_uri'=>'http://www.wilshireone.com/',
'description'=>'Add edit article links to your public site.',
'type'=>'1',
);
# --- END PLUGIN META ---
# --- BEGIN PLUGIN CODE ---

if ('admin' === @txpinterface) 
	{
	add_privs('editlink','1,2,3,4,5,6');

	// Add a new tab under 'extensions' called 'edit link', for the 'editlink' event
	register_tab("extensions", "editlink", "edit link");

	// 'rss_admin_editlink' will be called to handle the 'editlink' event
	register_callback("rss_admin_editlink", "editlink");
	}

function rss_admin_editlink($event, $step) 
	{
	global $rss_ae_cookie;
	include(txpath . '/include/txp_prefs.php');
	
	if (!isset($rss_ae_cookie)) 
		{
		$rss_ae_cookie = "rss_article_edit";
		$rs = safe_insert('txp_prefs', "name='rss_ae_cookie', val='$rss_ae_cookie', prefs_id='1'");
		}
	
	if (gps("add")) 
		{
		safe_update("txp_prefs", "val = '".addslashes(ps('rss_ae_cookie'))."'","name = 'rss_ae_cookie' and prefs_id ='1'");
		setcookie($rss_ae_cookie, $rss_ae_cookie, time()+31536000, "/");
		header("Location: index.php?event=editlink");
		} 
	else if (gps("rem"))
		{
		safe_update("txp_prefs", "val = '".addslashes(ps('rss_ae_cookie'))."'","name = 'rss_ae_cookie' and prefs_id ='1'");
		setcookie($rss_ae_cookie, $rss_ae_cookie, time()-3600, "/");
		header("Location: index.php?event=editlink");
		}
	
	pagetop("Edit Link");
	
	$aeset = isset($_COOKIE[$rss_ae_cookie]) ? "" : " not";
	
	$tdaStyle = ' style="text-align:right;vertical-align:middle"';
	echo form(startTable("list").
	tr(tdcs(hed("Add/Remove Public Site Article Edit Link",1),2)).
	tr(
		tda(graf('Cookie '.$rss_ae_cookie.' is'.$aeset.' set.', ' align="center"'), ' colspan="2"')
	).
	tr(
		tda(gTxt('Cookie Name:'), ' style="text-align:right;vertical-align:middle"').tda(text_input("rss_ae_cookie",$rss_ae_cookie,'20'), ' ')
	).
	tr(
		tda(graf(fInput("submit","add",gTxt("Add Edit Link"),"publish").fInput("submit","rem",gTxt("Remove Edit Link"),"publish").eInput("editlink"), ' align="center"'), ' colspan="2"')
	).
	endTable());	
	}

function rss_article_edit($atts,$thing="") 
	{
	global $thisarticle, $rss_ae_cookie, $prefs;
	
	extract(lAtts(array(
		'hidelive' => '',	# Set to non-empty string to hide links on live sites.
		'prefix' => '',
		'suffix' => ''
		),$atts)
	);

	$hidelive = !empty($hidelive) && ('live'===$prefs['production_status']);

	return (isset($_COOKIE[$rss_ae_cookie]) && !$hidelive) ? $prefix.'<a href="'.hu.'textpattern/index.php?event=article&amp;step=edit&amp;ID='.$thisarticle['thisid'].'">'.parse($thing).'</a>'.$suffix : '';
	}

# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
<p>
h1. Public Site Article Edit Link</p>

	<p>This plugin allows you to add an edit link for articles to you public website that can only be seen by you.  Clicking the link will bring you into the <span class="caps">TXP</span> admin interface article editing view for that article allowing you to edit articles without having to search for them in the article listing.</p>

	<p>In order to use the plugin:</p>

	<ol>
		<li>Navigate to the extensions -> edit link tab</li>
		<li>You have the option to change the cookie name that will be used</li>
		<li>Click the Add or Remove buttons to set or expire the cookie</li>
		<li>Add the rss_editlink tag to your article form (e.g., <code>&#60;txp:rss_article_edit&#62;Edit&#60;/txp:rss_article_edit&#62;</code>)</li>
		<li>Navigate to an article on your public site and click the edit link to be taken to the content -> write tab for that article.</li>
	</ol>
# --- END PLUGIN HELP ---
-->
<?php
}
?>