<?php
if( !defined('PSZAPP_INIT') ) exit;

// change tool settings
if( isset($_GET['act']) && $_GET['act']=='setting' )
{
	include_once('configurations.php');
	exit;
}

// input to encode, code to decode
$input = $code = '';
$check_multiline = $check_safe = false;
	
// API call by POST or user submit
if( $is_POST )
{
	//print_r($_POST);exit;
	if( isset($_POST['input']) && trim($_POST['input'])!='' )
		$input = trim($_POST['input']);
	if( isset($_POST['code']) && trim($_POST['code'])!='' )
		$code = trim($_POST['code']);
}
// direct use
else
{
	if( isset($_GET['input']) && trim($_GET['input'])!='' )
	{
		$input = trim($_GET['input']);
		$url_share .= "?input=$input";
	}
	else if( isset($_GET['code']) && trim($_GET['code'])!='' )
	{
		$code = trim($_GET['code']);
		$url_share .= "?code=$code";
	}

	// options
	if( isset($_GET['lines']) && (bool)$_GET['lines']==true )
	{
		$check_multiline = true;
		$url_share .= "&lines=1";
	}
	if( isset($_GET['safe']) && (bool)$_GET['safe']==true )
	{
		$check_safe = true;
		$url_share .= "&safe=1";
	}
}

//print_r($_GET);exit;

// string to base64
if( $input!='' )
{
	$code = $check_multiline ? explode(EOL, $input) : [$input];

	for($i=0; $i<count($code); $i++) {
		$code[$i] = base64_encode($code[$i]);
	}

	// https://stackoverflow.com/a/68297587/1884107
	if( $check_safe )
	{
		$check_safe = true;
		for($i=0; $i<count($code); $i++) {
			$code[$i] = rtrim(strtr($code[$i], '+/', '-_'), '=');
		}
	}

	// prepare output
	$code = implode($check_multiline ? EOL : '', $code);

	// prepend meta tags
	$page_title       = __('encoded') . " [" . substr($input, 0, 50) . "] " . __('to') . " Base64 - $page_title";
	$page_description = __('encoded') . " [$input] " . __('to') . " Base64. " . $tool_settings['Description'];

	// save log into db, do not save defult values
	if( !in_array($input, [__('your plain text you would like to encode'), 'URL', "https://raw.githubusercontent.com/toolywin/tool___$tool_slug/main/README.md"]) )
		$log_task = $PSZ_LOG_TEXT_BASE64_ENCODE;

}
else if( $code!='' ) // decode
{
	// prepend meta tags
	$page_title       = __('decoded') . ' ' . __('from [Base64 code] to text') . " - $page_title";
	$page_description = __('decoded') . ' ' . __('from [Base64 code] to text') . '. ' . $tool_settings['Description'];

	$input = is_url($code) ? file_get_contents($code) : $code;
	$input = base64_decode(strtr($input, '._-', '+/+'));

	// invalid base64 code, could not decode
	if( $input===FALSE )
		$invalid_input = true;

	// save log into db, do not save defult values
	else if( !in_array($input, [__('your encoded data'), 'URL']) || $code!="https://raw.githubusercontent.com/toolywin/tool___$tool_slug/main/README.enrypted.md" )
		$log_task = $PSZ_LOG_TEXT_BASE64_DECODE;
}

if( isset($log_task) && $log_task && !$is_bot )
{
	$input_type = 'message';

	// if input is url
	if( is_url($input) )
	{
		$input_type = 'link';
		// check img extension only, do not check header
		// set true to check header of image url, but consumes more time to return results
		if( is_image($input, false) )
			$input_type = 'image';
		else if( is_video($input) )
			$input_type = 'video';
		else if( is_audio($input) )
			$input_type = 'audio';
		else if( is_text_file($input) )
			$input_type = 'file';
	}
	else if( is_email($input) )
		$input_type = 'email';
	else if( strlen($input)>750 )
		$input_type = 'text';

	_log_tool($log_task, $user_session!=NULL?$user_session['id']:0, $tool_id, $input_type, $log_task==$PSZ_LOG_TEXT_BASE64_ENCODE?$input:$code);
}

// show preview if input is a link
if( $input!==FALSE && $input!='' && is_url($input) )
{
	include_once($PSZ_APP_root_dir . $PSZ_DIR_MORE . "/metadata.class.php");
	$meta = MetaData::fetch($input);
	$preview = true;

	$title = $summary = $img = '';
	if( isset($meta->{'og:title'}) )
		$title = $meta->{'og:title'};
	else if( isset($meta->{'twitter:title'}) )
		$title = $meta->{'twitter:title'};
	else if( isset($meta->title) )
		$title = $meta->title;

	if( isset($meta->{'og:description'}) )
		$summary = $meta->{'og:description'};
	else if( isset($meta->{'twitter:description'}) )
		$summary = $meta->{'twitter:description'};
	else if( isset($meta->description) )
		$summary = $meta->description;

	// if summary too long
	if( strlen($summary)>60 )
		$summary = substr($summary, 0, strpos($summary, ' ', 60)) . ' ...';

	if( isset($meta->{'og:image'}) )
		$img = $meta->{'og:image'};
	else if( isset($meta->{'twitter:image:src'}) )
		$img = $meta->{'twitter:image:src'};

	// if avail image
	if( $img!='' )
		$img = '<img src="' . $img . '" class="w-100px h-100px rounded-3 me-3" alt=""/>';

	$pTemplate->assign_vars([
		'PREVIEW_URL'     => $input,
		'PREVIEW_DOMAIN'  => get_site_domain($input),
		'PREVIEW_TITLE'   => $title,
		'PREVIEW_SUMMARY' => $summary,
		'PREVIEW_IMG'     => $img,
	]);
}

// show default example of encoding
$api_example_result = base64_encode(__('your plain text you would like to encode'));

// show default example of decoding
$api_example_encoded = base64_encode(__('your encoded data'));

// show usage logs
$t_settings = unserialize($db_tool_config['settings']);
$number = isset($t_settings['number']) ? $t_settings['number'] : 15;
if( NULL != ($rows=$db->_fetchAll($PSZ_TABLE_TOOL_USAGE_LOG, 'id, input_type, input, log_type, time', "tool_id=$tool_id AND private=0", "ORDER BY id DESC LIMIT 0,$number")) )
{
	$type = [
		$PSZ_LOG_TEXT_BASE64_ENCODE => __('encoded'),
		$PSZ_LOG_TEXT_BASE64_DECODE => __('decoded')
	];
	$input_type = [
		$PSZ_LOG_TEXT_BASE64_ENCODE => 'input=',
		$PSZ_LOG_TEXT_BASE64_DECODE => 'code='
	];
	$icon = [
		$PSZ_LOG_TEXT_BASE64_ENCODE => [
			'text'    => "text-slash",
			'message' => "message-slash",
			'image'   => "image-slash",
			'link'    => "link-slash",
			'video'   => "film-slash",
			'audio'   => "music-slash",
			'email'   => "eye-slash",
			'file'    => "file-slash",
		],
		$PSZ_LOG_TEXT_BASE64_DECODE => [
			'text'    => "text",
			'message' => "message",
			'image'   => "image",
			'link'    => "link",
			'video'   => "film",
			'audio'   => "music",
			'email'   => "eye",
			'file'    => "file",
		],
	];
	//print_r($icon);exit;
	$item = [
		'text'    => __('text'),
		'message' => __('message'),
		'image'   => __('picture'),
		'link'    => __('link'),
		'video'   => __('video'),
		'audio'   => __('audio'),
		'email'   => __('email'),
		'file'    => __('text file'),
	];
	foreach ($rows as $r)
	{
		//$r['log_type'] = $PSZ_LOG_TEXT_BASE64_DECODE;
		$pTemplate->assign_block_vars('log', [
			'ICON'       => $icon[$r['log_type']][$r['input_type']],
			'TYPE'       => $type[$r['log_type']],
			'ITEM'       => __('one') . ' ' . $item[$r['input_type']],
			'INPUT'      => $r['input'],
			'INPUT_PARA' => $input_type[$r['log_type']] . urlencode(trim($r['input'])),
			'TIME'       => time2str($r['time']),
			'TIME_ALT'   => date('d ', $r['time']) . __(date('M', $r['time'])) . date(', Y h:i:s', $r['time']),
		]);
	}
}

// common vars
$pTemplate->assign_vars([
	'INPUT'               => $input,
	'CODE'                => $code,
	'API_EXAMPLE_RESULT'  => $api_example_result,
	'API_EXAMPLE_ENCODED' => $api_example_encoded,
	'CHECK_MULTI_LINE'    => $check_multiline ? 'checked' : '',
	'CHECK_SAFE'          => $check_safe ? 'checked' : '',
]);

// do not compress example codes
$donot_compress = true;
$pContent .= $pTemplate->include_file($PSZ_DIR_TOOL . "/$tool_slug/main.html");

$pContent = $pTemplate->pparse($pContent, false); // keep global vars
//$pContent = $pTemplate->pparse($pContent, true, true, true); // this will compress all HTML code, even compress do_not_compress sections
?>