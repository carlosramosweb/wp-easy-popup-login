<?php
//error_reporting(0);
/*---------------------------------------------------------
Plugin Name: WP Easy Popup Login
Author: carlosramosweb
Author URI: https://criacaocriativa.com
Donate link: https://donate.criacaocriativa.com
Description: Esse plugin é uma versão BETA. Sistema para logar via popup modal para Aluno e Professor no WordPress.
Text Domain: wp-easy-popup-login
Domain Path: /languages/
Version: 1.0.0
Requires at least: 3.5.0
Tested up to: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
------------------------------------------------------------*/

/*
 * Exit if the file is accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Easy_Popup_Login' ) ) {	
	class WP_Easy_Popup_Login {
		//..

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init_functions' ) );
		}
		//=>

		public function init_functions() {
			add_action( 'ocean_before_outer_wrap', array( $this, 'popup_login' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'popup_login_style_script' ) );
			add_filter( 'xoo_aff_add_fields', array( $this, 'add_new_fields' ), 10, 2 );

			add_action( 'wp_ajax_popup_login_signon', array( $this, 'popup_login_signon' ) );
			add_action( 'wp_ajax_nopriv_popup_login_signon', array( $this, 'popup_login_signon' ) );

			add_action( 'wp_ajax_popup_login_lostpw_user', array( $this, 'popup_login_lostpw_user' ) );
			add_action( 'wp_ajax_nopriv_popup_login_lostpw_user', array( $this, 'popup_login_lostpw_user' ) );

			add_action( 'wp_ajax_popup_login_insert_user', array( $this, 'popup_login_insert_user' ) );
			add_action( 'wp_ajax_nopriv_popup_login_insert_user', array( $this, 'popup_login_insert_user' ) );

			add_action( 'wp_ajax_popup_login_logout', array( $this, 'popup_login_logout' ) );
			add_action( 'wp_ajax_nopriv_popup_login_logout', array( $this, 'popup_login_logout' ) );
		}
		//=>

		public function popup_login_style_script() {
			wp_enqueue_style( 'xoo-el-fonts', plugin_dir_url( __FILE__ ) . 'assets/css/xoo-el-fonts.css', array() );
			wp_enqueue_style( 'xoo-aff-style', plugin_dir_url( __FILE__ ) . 'assets/css/xoo-aff-style.css', array() );
			wp_enqueue_style( 'xoo-el-style', plugin_dir_url( __FILE__ ) . 'assets/css/xoo-el-style.css', array() );
			wp_enqueue_script( 'xoo-el-scrollbar', plugin_dir_url( __FILE__ ) . 'assets/js/smooth-scrollbar.js', array('jquery') );
			wp_enqueue_script( 'xoo-el-script', plugin_dir_url( __FILE__ ) . 'assets/js/xoo-el-script.js', array('jquery') );
			wp_enqueue_script( 'xoo-el-recaptcha', '//www.google.com/recaptcha/api.js?hl=pt-BR', array(), '3', true );
		}
		//=>

		public function popup_login_logout( ) { 
			wp_logout();
			echo 1;
			exit();
		}
		//=>

		public function popup_login_lostpassword( $user_id ) { 
			if ( isset( $user_id ) && $user_id > 0 ){

				$site_url 	= esc_url( get_option( 'siteurl' ) );
				$site_name	= get_option( 'blogname' );
				$site_email	= get_option( 'admin_email' );
				$user_info 	= get_userdata( $user_id );
				$password 	= wp_generate_password();
				wp_set_password( $password, $user_id );

				$user_login = $user_info->user_login;
      			$first_name = $user_info->first_name;
      			$last_name 	= $user_info->last_name;
      			$email 		= $user_info->user_email;

				$to      = $email;
				$subject = 'Redefinição de Senha - ' . $site_name . '';

				$message = 'Você solicitou uma redefinição de senha para a seguinte conta:<br/><br/>' . "\r\n";
				$message .= 'Segue sua nova senha abaixo:<br/><br/>' . "\r\n";
				$message .= 'Usuário: ' . $user_login . '<br/>' . "\r\n";
				$message .= 'E-mail: ' . $email . '<br/>' . "\r\n";
				$message .= '<strong>Nova Senha:</strong> ' . $password . ' <br/><br/>' . "\r\n";
				$message .= '<a href="' . $site_url . '" alt="Acessar o site">Acessar o site</a><br/><br/>' . "\r\n";
				$message .= '-<br/>Atenciosamente,<br/> ' . $site_name . '<br/> ' . $site_url . '' . "\r\n";

				$headers[] = 'From: ' . $site_name . ' <' . $site_email . '>';
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers[] = 'X-Mailer: PHP/' . phpversion();

				if ( wp_mail( $to, $subject, $message, $headers ) ){
					echo 1;
					exit();
				} else {
					echo 0;
					exit();
				}

			}
		}
		//=>

		public function popup_login_lostpw_user() {

			if ( isset( $_POST ) ){
				if ( isset( $_POST['user_login'] ) && $_POST['user_login'] != "" ){

					$userlogin 	= sanitize_text_field( $_POST['user_login'] );

					if ( strstr( $userlogin, '@' ) ) {
						$email 	= sanitize_email( $_POST['user_login'] );
						$exists = email_exists( $email );
					} else {
						$username 	= sanitize_user( $_POST['user_login'] );
						$exists 	= username_exists( $username );
					}

					if ( $exists > 0 ) {
						$send_password = $this->popup_login_lostpassword( $exists );
						if ( $send_password ) {
					    	echo 1;
							exit();
						} else {
					    	echo "not_send_password";
							exit();
						}
					} else {
					    echo "not_exist_user";
						exit();
					}
			    }
			}

		}
		//=>

		public function popup_login_signon() {

			if ( isset( $_POST ) ){
				if ( isset( $_POST['username'] ) && $_POST['username'] != "" && 
					isset( $_POST['password'] ) && $_POST['password'] != "" ){

					$username 	= sanitize_text_field( $_POST['username'] );
					$password 	= sanitize_text_field( $_POST['password'] );
					$rememberme = sanitize_text_field( $_POST['rememberme'] );
					$redirect 	= esc_url_raw( $_POST['redirect'] );

					if ( strstr( $username, '@' ) ) {
						$username = sanitize_email( $_POST['username'] );
					} else {
						$username = sanitize_user( $_POST['username'] );
					}

					$rememberme = ( 1 == $rememberme ) ? true : false;

				    $creds = array(
				        'user_login'    => $username,
				        'user_password' => $password,
				        'remember'      => $rememberme
				    );
				 
				    $user = wp_signon( $creds, is_ssl() );
				 
				    if ( $user > 0 && ! is_wp_error( $user ) ) {
				        echo 1;
						exit();
				    } else {
				    	if ( $user->errors['invalid_username']['0'] != "" ) {
				    		echo "username";
				    		exit();
				    	} else if ( $user->errors['incorrect_password']['0'] != "" ) {
				    		echo "password";
				    		exit();
				    	} else {
				    		echo 0;
				    		exit();
				    	}
				    }
			    }
			}

		}
		//=>

		public function popup_login_insert_user() {

			if ( isset( $_POST ) ){
				if ( isset( $_POST['first_name'] ) && $_POST['first_name'] != "" && 
					isset( $_POST['last_name'] ) && $_POST['last_name'] != "" && 
					isset( $_POST['user_email'] ) && $_POST['user_email'] != "" ){

					$roles 		= sanitize_text_field( $_POST['roles'] );
					$first_name = sanitize_text_field( $_POST['first_name'] );
					$last_name 	= sanitize_text_field( $_POST['last_name'] );
					$user_email = sanitize_text_field( $_POST['user_email'] );
					$user_state = sanitize_text_field( $_POST['user_state'] );
					$user_city 	= sanitize_text_field( $_POST['user_city'] );
					$redirect 	= esc_url_raw( $_POST['redirect'] );

					$bar_front 	= ( 'aluno' == $roles ) ? false : true;
					$user_login = strstr( $user_email, '@', true );	
					$password 	= wp_generate_password();

					$userdata = array(
					    'first_name'            => $first_name,
					    'last_name'             => $last_name,
					    'user_login' 			=> $user_login,
					    'user_email' 			=> $user_email,
					    'user_pass'  			=> $password,
					    'show_admin_bar_front' 	=> $bar_front,
					    'role' 					=> $roles,
					);			
					 
					$user_id = wp_insert_user( $userdata );

					if ( $roles == "professor_plano_gratis" ) {
						update_user_meta( $user_id, 'billing_country', 'BR' );
						$updated_state 	= update_user_meta( $user_id, 'billing_state', $user_state );
						$updated_city 	= update_user_meta( $user_id, 'billing_city', $user_city );						
					}

			    	$user_signon = true;
			    	if ( $user_id > 0 && $user_signon == true ) {
					    $creds = array(
					        'user_login'    => $user_email,
					        'user_password' => $password,
					        'remember'      => true
					    );						 
					    wp_signon( $creds, is_ssl() );
					}
				 
				    if ( $user_id > 0 && ! is_wp_error( $user_id ) ) {
				        echo 1;
						exit();
				    } else {
				    	if ( $user_id->errors['existing_user_login']['0'] != "" ) {
				    		echo "existing_user";
				    		exit();
				    	} else {
				    		echo 0;
				    		exit();
				    	}
				    }
			    }
			}

		}
		//=>

		public function popup_login() { ?>
			<div class="xoo-el-container xoo-el-popup-active" style="visibility: hidden; display: none;">
			    <div class="xoo-el-opac"></div>
			    <div class="xoo-el-modal">
			        <div class="xoo-el-inmodal">
			            <span class="xoo-el-close xoo-el-icon-cancel-circle"></span>
			            <div class="xoo-el-wrap">
			                <div class="xoo-el-sidebar"></div>
			                <div class="xoo-el-srcont">
			                    <div class="xoo-el-main">
			                        <?php $this->get_popup_login_form(); ?>
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
			<style type="text/css">
				.xoo-el-inmodal { max-width: 800px; max-height: 600px; height: auto !important; top: 20px; }
				.xoo-el-header { padding: 0px; }
				.xoo-el-section { padding: 0; padding-left: 0; }
				.xoo-el-srcont { background-color: #ffffff; }
				.xoo-el-main { padding: 40px 20px 0px; }
				.xoo-el-main, 
				.xoo-el-main a, 
				.xoo-el-main label { color: #000000; }
				.xoo-el-form-container ul.xoo-el-tabs li.xoo-el-active { background-color: #000000; color: #ffffff; }
				.xoo-el-form-container ul.xoo-el-tabs li { background-color: #eeeeee; color: #000000; }
				.xoo-aff-group { margin-bottom: 30px; width: 100%; }
				.xoo-aff-input-group .xoo-aff-input-icon { background-color: #eee; color: #555; max-width: 40px; min-width: 40px; border: 1px solid #ccc; border-right: 0; font-size: 14px; }
				.xoo-aff-group input[type="text"], 
				.xoo-aff-group input[type="password"], 
				.xoo-aff-group input[type="email"], 
				.xoo-aff-group input[type="number"], 
				.xoo-aff-group select { border-bottom-left-radius: 0; border-top-left-radius: 0; }
				.xoo-aff-group input[type="text"], 
				.xoo-aff-group input[type="password"], 
				.xoo-aff-group input[type="email"], 
				.xoo-aff-group input[type="number"], 
				.xoo-aff-group select, 
				.xoo-aff-group select + .select2 { background-color: #fff; color: #777; }
				.xoo-el-form-container button.btn.button.xoo-el-action-btn { background-color: #000000; color: #ffffff; width: 100%; font-weight: 600; font-size: 15px; padding: 20px; }
				.xoo-el-form-container .xoo-el-txt-align-center { text-align: center; }
				.xoo-el-form-container .xoo-el-notice { margin: 20px 0; width: 100%; background-color: #e2401c; color: #fff; border: none; border-left: 10px solid rgba(0,0,0,0.15); font-size: 14px; padding: 15px 20px; }
				.xoo-el-form-container .xoo-el-loading { position: absolute; width: 100%; height: 100%; top: 0; left: 0; text-align: center; z-index: 99998; background-color: #FFF; opacity: 0.7; }
				.xoo-el-form-container .xoo-el-loading img { position: absolute; top: 50%; left: 50%; z-index: 99999; }
			</style>
			<?php if ( ! is_user_logged_in() ) { ?>
			<script type="text/javascript">
				jQuery( document ).ready( function($){
					$( ".xoo-el-logout" ).attr( "style", "display: none;" );
				})
			</script>
			<?php } ?>
			<?php
		}
		//=>

		public function get_popup_login_form() { 
			global $wp, $wp_query;
			$current_slug = add_query_arg( array(), $wp->request ); 
			$current_url = esc_url( site_url( $current_slug ) );
			?>

		<?php //do_action( 'woocommerce_before_customer_login_form' ); ?>

		   <div class="xoo-el-form-container xoo-el-form-popup">

		   		<?php if ( is_user_logged_in() ) { ?>
		   		<input type="hidden" class="xoo-el-user-id" value="<?php echo get_current_user_id(); ?>">
		   		<?php } ?>

				<div class="xoo-el-loading" style="display: none;">
					<img src="<?php echo plugins_url( '/assets/images/loading.gif', __FILE__ ); ?>" alt="loading" class="xoo-el-img-loading">
				</div>
		      <div class="xoo-el-header">
		         <ul class="xoo-el-tabs">
		            <li class="xoo-el-login-tgr xoo-el-active">Login</li>
		            <li class="xoo-el-reg-tgr">Registrar-se</li>
		         </ul>
		      </div>
		      <div class="xoo-el-section xoo-el-active" id="xoo-el-form-login">
		         <div class="xoo-el-fields">
		            <div class="xoo-el-notice xoo-el-form-login" style="display: none;"></div>
		            <form class="xoo-el-action-form xoo-el-form-login" id="xoo-el-action-login" action="javascript:;">

		            	<?php do_action( 'woocommerce_login_form_start' ); ?>	

		               <div class="xoo-aff-group">
		                  <div class="xoo-aff-input-group">
		                  	<span class="xoo-aff-input-icon far fa-user"></span>
		                  	<input type="text" class="xoo-aff-text xoo-el-username" name="log" placeholder="Usuário / Email" value="" size="20" autocomplete="email" required="">
		                  </div>
		               </div>

		               <div class="xoo-aff-group">
		                  <div class="xoo-aff-input-group">
		                  	<span class="xoo-aff-input-icon fas fa-key"></span>
		                  	<input type="password" class="xoo-aff-password xoo-el-password" name="pwd" placeholder="Senha" value="" size="20" required="">
		                  </div>
		               </div>

		               <div class="xoo-aff-group xoo-el-login-btm-fields">		               	
		                  <label class="xoo-el-form-label">
		                  	<input type="checkbox" name="rememberme" class="xoo-el-rememberme" value="forever" checked="1">
		                  	<span>Lembrar-me</span>
		                  </label>

		                  <a href="javascript:;" class="xoo-el-lostpw-tgr xoo-el-lostpw">Esqueceu a senha?</a>
		               </div>

		               <input type="hidden" name="_xoo_el_form" value="login">
		               <input type="hidden" name="ajaxurl" class="xoo-el-ajax-url" value="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		               <input type="hidden" class="xoo-el-redirect" value="<?php echo $current_url; ?>">
		               <button type="submit" class="button btn xoo-el-action-btn xoo-el-login-btn">Entrar</button>

		               <?php do_action( 'woocommerce_login_form_end' ); ?>

		            </form>
		         </div>
		      </div>

		      <?php //do_action( 'woocommerce_after_customer_login_form' ); ?>

			<div class="xoo-el-section" id="xoo-el-form-lostpw">
				<div class="xoo-el-fields">
				<div class="xoo-el-notice xoo-el-form-lostpw" style="display: none;"></div>

					<form class="xoo-el-action-form xoo-el-form-lostpw" id="xoo-el-action-lostpw" action="javascript:;">

						<span class="xoo-el-form-txt xoo-el-txt-align-center">
							<strong>Perdeu sua senha?</strong><br/>
							Por favor, insira seu nome de usuário ou endereço de e-mail.<br/>
							Você receberá um link para criar uma nova senha por e-mail.
						</span>

						<div class="xoo-aff-group user-login-cont">
							<div class="xoo-aff-input-group">
								<span class="xoo-aff-input-icon far fa-user"></span>
								<input type="text" class="xoo-aff-required xoo-aff-text xoo-el-lostpw-user-login" name="user_login" placeholder="Usuário / Email" value="" required="">
							</div>
						</div>

		               <input type="hidden" name="_xoo_el_form" value="lostPassword">
		               <input type="hidden" name="ajaxurl" class="xoo-el-lostpw-ajax-url" value="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
						<button type="submit" class="button btn xoo-el-action-btn xoo-el-lostpw-btn">Enviar email</button>

					</form>

				</div>
			</div>

		      <div class="xoo-el-section" id="xoo-el-form-register">
		         <div class="xoo-el-fields">
		            <div class="xoo-el-notice xoo-el-form-register" style="display: none;"></div>

		            <form class="xoo-el-action-form xoo-el-form-register" id="xoo-el-action-register" action="javascript:;">
		               <div class="xoo-aff-fields">
		                  <div class="xoo-aff-group one">
		                     <div class="xoo-aff-input-group">
		                     	<span class="xoo-aff-input-icon fas fa-user-friends"></span>
		                     	<select class="xoo-aff-select xoo-el-reg-user-roles" name="xoo_el_reg_user_roles">
		                     		<option value="aluno">Aluno</option>
		                     		<option value="professor_plano_gratis">Professor</option>
		                     	</select>
		                     </div>
		                  </div>
		                  <div class="xoo-aff-group onehalf">
		                     <div class="xoo-aff-input-group">
		                     	<span class="xoo-aff-input-icon far fa-user"></span>
		                     	<input type="text" class="xoo-aff-text xoo-el-reg-fname" name="xoo_el_reg_fname" placeholder="Nome" value="" required="">
		                     </div>
		                  </div>
		                  <div class="xoo-aff-group onehalf" style="width: 50%;margin-right: 0;">
		                     <div class="xoo-aff-input-group">
		                     	<span class="xoo-aff-input-icon far fa-user"></span>
		                     	<input type="text" class="xoo-aff-text xoo-el-reg-lname" name="xoo_el_reg_lname" placeholder="Sobrenome" value="" required="">
		                     </div>
		                  </div>
		                  <div class="xoo-aff-group one">
		                     <div class="xoo-aff-input-group">
		                     	<span class="xoo-aff-input-icon fas fa-at"></span>
		                     	<input type="email" class="xoo-aff-email xoo-el-reg-email" name="xoo_el_reg_email" placeholder="Email" value="" autocomplete="email" required="">
		                     </div>
		                  </div>

		                  <div class="xoo-aff-group-professor" style="display: none;">
			                  <div class="xoo-aff-group onehalf">
			                     <div class="xoo-aff-input-group">
			                     	<span class="xoo-aff-input-icon fas fa-map-marked-alt"></span>
			                     	<select class="xoo-aff-select xoo-el-reg-state" name="xoo_el_reg_state">
			                     		<option value="">Selecione um estado</option>
										<option value="AC">Acre</option>
										<option value="AL">Alagoas</option>
										<option value="AP">Amapá</option>
										<option value="AM">Amazonas</option>
										<option value="BA">Bahia</option>
										<option value="CE">Ceará</option>
										<option value="DF">Distrito Federal</option>
										<option value="ES">Espírito Santo</option>
										<option value="GO">Goiás</option>
										<option value="MA">Maranhão</option>
										<option value="MT">Mato Grosso</option>
										<option value="MS">Mato Grosso do Sul</option>
										<option value="MG">Minas Gerais</option>
										<option value="PA">Pará</option>
										<option value="PB">Paraíba</option>
										<option value="PR">Paraná</option>
										<option value="PE">Pernambuco</option>
										<option value="PI">Piauí</option>
										<option value="RJ">Rio de Janeiro</option>
										<option value="RN">Rio Grande do Norte</option>
										<option value="RS">Rio Grande do Sul</option>
										<option value="RO">Rondônia</option>
										<option value="RR">Roraima</option>
										<option value="SC">Santa Catarina</option>
										<option value="SP">São Paulo</option>
										<option value="SE">Sergipe</option>
										<option value="TO">Tocantins</option>
			                     	</select>
			                     </div>
			                  </div>
			                  <div class="xoo-aff-group onehalf" style="width: 50%;margin-right: 0;">
			                     <div class="xoo-aff-input-group">
			                     	<span class="xoo-aff-input-icon fas fa-map-marker-alt"></span>
			                     	<input type="text" class="xoo-aff-text xoo-el-reg-city" name="xoo_el_reg_city" placeholder="Cidade" value="">
			                     </div>
			                  </div>
		                  </div>

		                  <div class="xoo-aff-group one">
		                  	<div class="g-recaptcha" data-sitekey="6LdYvJwaAAAAAMwH-LYaIYVZORy3iJ7igrtHVwF9"></div>
		                  </div>
						   
						   <?php echo do_shortcode('[wpcaptcha wc]' ); ?>

		                  <div class="xoo-aff-group one">
		                     <div class="xoo-aff-checkbox_single">
		                     	<label>
		                     		<input type="checkbox" name="xoo_el_reg_terms" class="xoo-aff-checkbox_single" value="yes"  required="">Eu aceito os <a href="#" target="_blank"> Termos de Serviço e Políticas de Privacidade </a>
		                     	</label>
		                     </div>
		                  </div>

		               </div>

		               <input type="hidden" class="xoo-el-reg-ajax-url" value="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		               <input type="hidden" class="xoo-el-reg-redirect" value="<?php echo $current_url; ?>">
		               <button type="submit" class="button btn xoo-el-action-btn xoo-el-register-btn">Registrar-se</button>
		            </form>
		         </div>
		      </div>
		   </div>
			<?php
		}
		//=>

	}
	new WP_Easy_Popup_Login();
}