jQuery(document).ready(function($){

	$( ".xoo-el-login" ).on( "click", function() {
	  $( ".xoo-el-container" ).addClass( "xoo-el-popup-active" );
	  $( ".xoo-el-container" ).attr( "style", "visibility: visible; display: block;" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	  $( ".xoo-el-login-tgr" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-reg-tgr" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-register" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-lostpw" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-login" ).addClass( "xoo-el-active" );
	});

	$( ".xoo-el-reg" ).on( "click", function() {
	  $( ".xoo-el-container" ).addClass( "xoo-el-popup-active" );
	  $( ".xoo-el-container" ).attr( "style", "visibility: visible; display: block;" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	  $( ".xoo-el-reg-tgr" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-login-tgr" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-login" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-lostpw" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-register" ).addClass( "xoo-el-active" );
	});

	$( ".xoo-el-lostpw" ).on( "click", function() {
	  $( ".xoo-el-container" ).addClass( "xoo-el-popup-active" );
	  $( ".xoo-el-container" ).attr( "style", "visibility: visible; display: block;" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	  $( ".xoo-el-reg-tgr" ).removeClass( "xoo-el-active" );
	  $( ".xoo-el-login-tgr" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-login" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-lostpw" ).addClass( "xoo-el-active" );
	});

	$( ".xoo-el-icon-cancel-circle" ).on( "click", function() {
	  $( ".xoo-el-container" ).removeClass( "xoo-el-popup-active" );
	  $( ".xoo-el-container" ).attr( "style", "visibility: hidden; display: none;" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	});

	$( ".xoo-el-login-tgr" ).on( "click", function() {
	  $( ".xoo-el-login-tgr" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-reg-tgr" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-register" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-lostpw" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-login" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	});

	$( ".xoo-el-reg-tgr" ).on( "click", function() {
	  $( ".xoo-el-reg-tgr" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-login-tgr" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-login" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-lostpw" ).removeClass( "xoo-el-active" );
	  $( "#xoo-el-form-register" ).addClass( "xoo-el-active" );
	  $( ".xoo-el-notice" ).attr( "style", "display: none;" );
	});

	$( ".xoo-el-reg-user-roles" ).on( "change", function() {
		var user_roles = $( ".xoo-el-reg-user-roles" ).val();
		if ( user_roles == "professor_plano_gratis" ) {
	  		$( ".xoo-aff-group-professor" ).attr( "style", "display:block;" );
	  		$( ".xoo-el-reg-state" ).attr( "required", true );
	  		$( ".xoo-el-reg-city" ).attr( "required", true );
		} else {
			$( ".xoo-aff-group-professor" ).attr( "style", "display: none;" );
	  		$( ".xoo-el-reg-state" ).removeAttr( "required" );
	  		$( ".xoo-el-reg-city" ).removeAttr( "required" );
		}
	});
	//=>

	$( "#xoo-el-action-login" ).submit( function( event ) {
		$( ".xoo-el-notice.xoo-el-form-login" ).attr( "style", "display: none;" );
		$( ".xoo-el-loading" ).attr( "style", "display:block;" );

		var ajaxurl 	= $( ".xoo-el-ajax-url" ).val();
		var username 	= $( ".xoo-el-username" ).val();
		var password 	= $( ".xoo-el-password" ).val();
		var rememberme 	= $( ".xoo-el-rememberme" ).val();
		var redirect 	= $( ".xoo-el-redirect" ).val();

		jQuery.post( 
			ajaxurl,
			{
				'action'	: 'popup_login_signon',
				'username'	: username,
				'password'	: password,
				'rememberme': rememberme,
				'redirect'	: redirect,
			},
			function( data, status ){
				if ( status == "success" && data == 1 ) { 
					document.location.href = redirect;
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );

				} else if ( data == "username" ){
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).html( "Nome de usuário desconhecido." );

				} else if ( data == "password" ){
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).html( "A senha está incorreta para o usuário <strong>" + username + "</strong>." );

				} else {
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-login" ).html( data );
				}
		});

	});

	$( "#xoo-el-action-lostpw" ).submit(function( event ) {
		$( ".xoo-el-notice.xoo-el-form-lostpw" ).attr( "style", "display: none;" );
		$( ".xoo-el-loading" ).attr( "style", "display:block;" );

		var ajaxurl 	= $( ".xoo-el-lostpw-ajax-url" ).val();
		var userlogin 	= $( ".xoo-el-lostpw-user-login" ).val();

		jQuery.post( 
			ajaxurl,
			{
				'action'		: 'popup_login_lostpw_user',
				'user_login'	: userlogin,
			},
			function( data, status ){
				if ( status == "success" && data == 1 ) { 
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).attr( "style", "display:block; background-color: #05981b;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).html( "Um e-mail de redefinição de senha foi enviado para seu e-mail." );

				} else if ( data == "not_exist_user" ){
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).html( "O Usuário ou E-mail não existe!" );

				} else {
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-lostpw" ).html( data );
				}
		});
	});

	$( "#xoo-el-action-register" ).submit(function( event ) {
		$( ".xoo-el-notice.xoo-el-form-register" ).attr( "style", "display: none;" );
		$( ".xoo-el-loading" ).attr( "style", "display:block;" );

		var ajaxurl 			= $( ".xoo-el-ajax-url" ).val();
		var roles 				= $( ".xoo-el-reg-user-roles" ).val();
		var first_name 			= $( ".xoo-el-reg-fname" ).val();
		var last_name 			= $( ".xoo-el-reg-lname" ).val();
		var email 				= $( ".xoo-el-reg-email" ).val();
		var state 				= $( ".xoo-el-reg-state" ).val();
		var city 				= $( ".xoo-el-reg-city" ).val();
		var captcha 			= grecaptcha.getResponse();
		var redirect 			= $( ".xoo-el-reg-redirect" ).val();

		if ( captcha == "" ) {
			$( ".xoo-el-loading" ).attr( "style", "display: none;" );
			$( ".xoo-el-notice.xoo-el-form-register" ).attr( "style", "display:block;" );
			$( ".xoo-el-notice.xoo-el-form-register" ).html( "O Captcha não foi preenchido!" );
			return;
		}

		jQuery.post( 
			ajaxurl,
			{
				'action'	: 'popup_login_insert_user',
				'roles'		: roles,
				'first_name': first_name,
				'last_name'	: last_name,
				'user_email': email,
				'user_state': state,
				'user_city'	: city,
				'redirect'	: redirect,
			},
			function( data, status ){
				if ( status == "success" && data == 1 ) { 
					document.location.href = redirect;
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );

				} else if ( data == "existing_user" ){
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-register" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-register" ).html( "Este nome de Usuário/E-mail já existe!" );

				} else {
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
					$( ".xoo-el-notice.xoo-el-form-register" ).attr( "style", "display:block;" );
					$( ".xoo-el-notice.xoo-el-form-register" ).html( data );
				}
		});
	});

	$( ".xoo-el-logout" ).click(function( event ) {
		$( ".xoo-el-loading" ).attr( "style", "display:block;" );

		var ajaxurl  = $( ".xoo-el-ajax-url" ).val();
		var user_id  = $( ".xoo-el-user-id" ).val();
		var redirect = $( ".xoo-el-redirect" ).val();

		jQuery.post( 
			ajaxurl,
			{
				'action'	: 'popup_login_logout',
				'user_id'	: user_id,
				'redirect'	: redirect,
			},
			function( data, status ){
				if ( status == "success" && data == 1 ) { 
					document.location.href = redirect;
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );

				} else {
					$( ".xoo-el-loading" ).attr( "style", "display: none;" );
				}
		});
	});

})