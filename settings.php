<?php
if( !defined('PSZAPP_INIT') ) exit;

// each log type must be unique number
$PSZ_LOG_TEXT_BASE64_ENCODE = 510;
$PSZ_LOG_TEXT_BASE64_DECODE = 511;

/********************************
Required Files:
	index.php		: where to process your tool
	settings.php	: global settings of your tool
	logos			: folder to contain various logos of your tool
		16.png			: to display on the browser's title bar
		180.png			: tool logo
	sharing.jpg		: used to sharing on socials

$tool_settings		:	Unchangable variable name
	Version			:	Required
	Name			:	Required
	Description		:	Required
	Keyword			:	optional
	Developer		:	Required
		Name			:	Required
		Contact			:	Required, website or email
		Source			:	optional, links to open source sites
		Donate			:	optional, links to donations
			Paypal			:	link to paypal donation
			BTC				:	link to BTC donation
			Ethereum		:	link to ETH donation
	Date			:	Required, created date; format: Y-MM-d, 2022-11-17

	Changelog		:	optional - used to store changelog
********************************/

$tool_settings = [
	'Version'     => '1.1.0',
	'Name'        => __('Text & Base64 Decode and Encode'),
	'Description' => __('Convert any text-based input to base64 format and vice vera: a link, a video or an image, even the remote URLs or your own uploadable files. Then give results to your friends directly with their own languages by sharable links or download to store in your private place.'),
	'Keyword'     => __('text to base64 converter, text file to base64 javascript, text to base64 online, encode text to base64 python, image file to base64 linux, text to base64 c#, text to base64 java, text to base64 nodejs, text to base64 angular, text to base64 powershell, text to base64 java, string to base64, string to base64 swift, string to flutter, string to golang, encrypt string to base64 react'),
	'Developer'   => [
		'Name'    	=> 'PreScriptZ.com',
		'Contact' 	=> 'https://www.prescriptz.com/',
		'Source'	=> [
			'GitHub'    => 'https://github.com/toolywin/tool___text-base64-decode-encode',
		],
		'Donate'  	=> [
			'Paypal'   => 'https://www.paypal.me/PREScriptZ',
			'BTC'      => 'https://blockchain.info/address/1FNvqxG5T6P5UFtLvq5hdGir6LnS1zJQ6m',
			'Ethereum' => 'https://etherscan.io/address/0x85469855fd24498418e58ff9ad0298f0c498b4e8',
			'LTC'      => 'https://live.blockcypher.com/ltc/address/LY6ADMcfUejoeExifh2ngMXpHM5z8CXxuq',
		],
	],
	'Date'      => '2023-01-15', // created date
	'Changelog' => [
		'2023-01-18'	=> [
			'v1.1.0',
			[
				__('Added') => [
					__('Top Google Searches'),
				],
			]
		],
	],
];