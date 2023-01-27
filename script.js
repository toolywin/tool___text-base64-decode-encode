"use strict";

document.addEventListener("DOMContentLoaded", function(e) {
	// show favorite icon if was in list
	// you do not need to run if use same button with class btn_mark_fav with child icon <i> tag
	/*if( localStorage.getItem("tool_fav")!==null && localStorage.getItem("tool_fav")!='' ) {
		var tool_fav = localStorage.getItem("tool_fav").split(',');
		if( $('.btn_mark_fav').length>0 && tool_fav.includes($('.btn_mark_fav').data('tool-id').toString()) ) {
			$('.btn_mark_fav i').attr('class', FA_CLASS + ' fa-heart-circle-minus fa_class_switcher fs-1 text-danger');
			changeTooltip(document.querySelector('.btn_mark_fav'), L_FAV_REMOVE);
		}
		$('#count_fav').text( tool_fav.length-2 );
    }*/


    // invalid hex code
    if( typeof is_INVALID_INPUT_CODE !='undefined' && is_INVALID_INPUT_CODE )
    {
		Swal.fire({
		    text: L_INVALID_ENCRYPTED_CODE,
		    icon: "error",
		    buttonsStyling: false,
		    confirmButtonText: L_CLOSE,
		    customClass: {
		        confirmButton: "btn btn-danger"
		    }
		});
    }
    // highlight result
    else
    {
	    var highlight_element = '';
	    if( $('#preview').length>0 )
	    	highlight_element = '#preview';
	    else
	    {
	    	var param = new URLSearchParams(window.location.search);

	    	if( param.has('input') && param.get('input')!='' )
	    		highlight_element = '#code';
	    	else if( param.has('code') && param.get('code')!='' )
	    		highlight_element = '#input';
	    }
	    if( highlight_element!='' )
	    	$(highlight_element).pulsate({color:'#fff'});
	}

	// dynamically change decoded text
	$('#lines, #safe').on('change', function(e) {
		if( $('#input').val().trim()=='' ) return false;

		var input = $('#input').val().trim().split('\n'),
			lines = $('#lines').is(':checked'),
			safe = $('#safe').is(':checked');

		/*// add spaces
		code = chunk_split(code, 2, ' ').trim();

		// explode
		code = code.split(' ');*/

		input.forEach(function(v, index) {
			input[index] = v.base64Encode();
		});

		if( safe )
			for(var i=0; i<input.length; i++)
				input[i] = input[i].replace(/\+/g, '-').replace(/\//g, '_').replace(/\=+$/, '');

		var d = input.join( lines ? '\n' : '');

/*		if( safe )
			d = d.replace(/-/g, '+').replace(/_/g, '/');*/

		$('#code').val( d.trim() ).trigger('change');
	});

	// trigger if live mode ON
	$(document).on('change keyup', '#input, #code', function(e) {
		if( $('#live_mode').is(':checked') )
		{
			var name = null, destination;
			if( $(this).attr('id')=='input' && $('#input').val().trim()!='' )
			{
				name = 'input';
				destination = 'code';
			}
			else if( $('#code').val().trim()!='' )
			{
				name = 'code';
				destination = 'input';
			}

			if( name==null )
				return false;

			var source = $('#' + name).val().trim().split('\n'),
				lines = $('#lines').is(':checked'),
				safe = $('#safe').is(':checked');

			source.forEach(function(v, index) {
				source[index] = name=='input' ? v.base64Encode() : v.base64Decode();
			});

			if( safe )
				source.forEach(function(v, index) {
					source[index] = v.replace(/\+/g, '-').replace(/\//g, '_').replace(/\=+$/, '')
				});

			$('#' + destination).val( source.join(lines ? '\n' : '') );
		}
	});

	// before form submitted
	$(document).on('submit', '#form_tool', function(e) {
		if( $(this).find("[type=submit]:focus" ).attr('id')=='encode' )
			$('#code').val('');
		else
			$('#input').val('');

		if( $('#input').val().trim()=='' && $('#code').val().trim()=='' )
		{
			Swal.fire({
			    text: L_ENTER_INPUT,
			    icon: "error",
			    buttonsStyling: false,
			    confirmButtonText: L_CLOSE,
			    customClass: {
			        confirmButton: "btn btn-danger"
			    }
			});
			return false;
		}

		// if length of inputs too large, change to POST
		if( $('#code').val().length>1950 || $('#input').val().length>1950 )
			$(this).attr('method', 'POST');
	});

	/*
	// use select if we have many options of 1 parameter
	$(document).on('click', '#rfc', function() {
		$('#rfc').prop('checked', false).val( 0 );
		$('#rfc_label').text( $('#rfc_label').data('rfc-default-label') );
	});

	setTimeout(function() {
		let menu = KTMenu.getInstance( document.querySelector("#rfc_custom") );
		menu.on("kt.menu.link.clicked", function(e) {
		    if( jQuery.inArray($(e).data('rfc'), [2045, 3548, 4648]) !== -1 ) {
		    	$('#rfc').prop('checked', true).val( $(e).data('rfc') );
		    	$('#rfc_label').text( $(e).text() );
		    }
		});
	}, 500);*/
});