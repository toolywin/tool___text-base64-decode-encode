javascript:
	// detect if user select a text
	var text = '' + 
	(window.getSelection ? window.getSelection() : 
		(document.getSelection ? document.getSelection() : document.selection.createRange().text));

	// trim spaces
	text = text.trim();

	// if no selected text, ask user to enter the text
	var p = !text||text=='' ? prompt('_lang{Please enter the decoded text to convert into normal string}') : text;

	// if length of input reachs max, alert to use post method
	if( p.length>1950 ) {
		if( true==confirm('_lang{The tool could not process this input directly, please access the website to re-enter this input to process again}') )
			window.open('https://[=PSZ_APP_SITE_DOMAIN=]/[=TOOL_SLUG=].html?lang=[=CURRENT_LANGUAGE_CODE=]');
		void(0);
		exit;
	}

	// all ok, open tool in new tab
	window.open('https://[=PSZ_APP_SITE_DOMAIN=]/[=TOOL_SLUG=].html?lang=[=CURRENT_LANGUAGE_CODE=]&utm_source=shortcut&code=' + p);

	void(0); // this void prevents direction of current page
	exit;