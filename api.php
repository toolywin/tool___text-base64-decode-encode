<?php
if( !defined('PSZAPP_INIT') ) exit;
header('Content-Type: application/json; charset=utf-8');

$no_api = [
	'status'  => 0,
	'message' => __('This tool currently does not support the GET method'),
];

// not POST
if( !$is_POST )
	die(json_encode($no_api));

$input = $code = '';

// detect inputs
if( isset($_POST['input']) && trim($_POST['input'])!='' )
	$input = trim($_POST['input']);
if( isset($_POST['code']) && trim($_POST['code'])!='' )
	$code = trim($_POST['code']);

if( $input=='' && $code=='' )
{
	$no_api['message'] = __('Missing input');
	die(json_encode($no_api));
}

$return_api = [
	'status'  => 1,
	'message' => '',
];

// load global var
include_once("$PSZ_DIR_TOOL/$slug/settings.php");

/** 
* predefined functions
* */
// $api=true; increase usage count only, do not log content
// function _log_tool($log_type, $user_id = 1, $tool_id, $input_type, $input, $api = false)

if( $input!='' )
{
	// fetch content if URL and paramter content
	// or process as text
	$code = is_url($input) && isset($_POST['content']) && $_POST['content']=='fetch' ? file_get_contents($input) : $input;

	if( $code==false )
	{
		$no_api['message'] = __('Invalid input or could not process your data');
		die(json_encode($no_api));
	}

	// covert to hex
	$code = base64_encode($input);

	if( isset($_POST['safe']) && (bool)$_POST['safe']==true )
		$code = rtrim(strtr($code, '+/', '-_'), '=');

	$return_api['result'] = $code;

	// log counts only, do not log content, do not save defult values
	if( !in_array($input, [__('your plain text you would like to encode'), 'URL', 'https://raw.githubusercontent.com/toolywin/tools/main/README.md']) )
		_log_tool($PSZ_LOG_TEXT_BASE64_ENCODE, 0, $tool_id, '', $input, true);
}
else if( $code!='' ) // decode
{
	$input = is_url($code) ? file_get_contents($code) : $code;
	$input = base64_decode(strtr($input, '._-', '+/+'));

	if( $input==false )
	{
		$no_api['message'] = __('Invalid input or could not process your data');
		die(json_encode($no_api));
	}

	$return_api['result'] = $input;

	// log counts only, do not log content, do not save defult values
	// if returned result is not defaults then log
	if( !is_bot() && !in_array($input, [__('your encoded data'), 'URL', 'https://raw.githubusercontent.com/toolywin/tools/main/text-hex-converter/README.HEX.md']) )
		_log_tool($PSZ_LOG_TEXT_BASE64_DECODE, 0, $tool_id, '', $code, true);
}

// return result
die(json_encode($return_api));
