<?php
/*
 * @package Inwave Athlete
 * @version 1.0.0
 * @created Mar 31, 2015
 * @author Inwavethemes
 * @email inwavethemes@gmail.com
 * @website http://inwavethemes.com
 * @support Ticket https://inwave.ticksy.com/
 * @copyright Copyright (c) 2015 Inwavethemes. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

/**
 * Description of iw_contact
 *
 * @Developer duongca
 */
if (!class_exists('Inwave_IW_Contact')) {

    class Inwave_IW_Contact {

        function __construct() {

            add_action('vc_before_init', array($this, 'heading_init'));
            add_shortcode('inwave_iw_contact', array($this, 'inwave_iw_contact_shortcode'));
            add_action('wp_ajax_nopriv_sendMessageContact', array($this, 'sendMessageContact'));
            add_action('wp_ajax_sendMessageContact', array($this, 'sendMessageContact'));
        }

        function heading_init() {

            // Add banner addon
            vc_map(array(
                'name' => 'IW Contact',
                'description' => __('Show contact form', 'inwavethemes'),
                'base' => 'inwave_iw_contact',
                // 'icon' => 'icon-wpb-inwavethemes',
                'category' => 'InwaveThemes',
                'params' => array(
					array(
                        'type' => 'textfield',
                        "holder" => "div",
                        "heading" => __("Receiver Email", "inwavethemes"),
                        "value" => "",
                        "param_name" => "receiver_email",
                        "description" => __('If not specified, Admin E-mail Address in General setting will be used', "inwavethemes")
                    ),
                    array(
                        'type' => 'textfield',
                        "holder" => "div",
                        "heading" => __("Title", "inwavethemes"),
                        "value" => "",
                        "param_name" => "title",
                        "description" => __('Title of iw_contact block.', "inwavethemes")
                    ),
                    array(
                        'type' => 'textfield',
                        "holder" => "div",
                        "heading" => __("Sub Title", "inwavethemes"),
                        "value" => "",
                        "param_name" => "sub_title",
                        "description" => __('Sub Title of iw_contact block.', "inwavethemes")
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Show name", "inwavethemes"),
                        "param_name" => "show_name",
                        "description" => __("Show field name on form", 'inwavethemes'),
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Show email", "inwavethemes"),
                        "param_name" => "show_email",
                        "description" => __("Show field email on form", 'inwavethemes'),
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Show message", "inwavethemes"),
                        "param_name" => "show_message",
                        "description" => __("Show field message on form", 'inwavethemes'),
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
					array(
                        "type" => "dropdown",
                        "heading" => __("Show captcha", "inwavethemes"),
                        "param_name" => "show_captcha",
                        "description" => __("Show captach field", 'inwavethemes'),
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Show GDPR", "inwavethemes"),
                        "param_name" => "show_gdpr",
                        "description" => __("Show GDPR field", 'inwavethemes'),
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Show with map", "inwavethemes"),
                        "param_name" => "show_with_map",
                        "description" => __("Show contact form with map", 'inwavethemes'),
                        'default' => 'yes',
                        "value" => array(
                            'Yes' => 'yes',
                            'No' => 'no',
                        ),
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Extra Class", "inwavethemes"),
                        "param_name" => "class",
                        "description" => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', "inwavethemes")
                    )
                )
            ));
        }

        // Shortcode handler function for list Icon
        function inwave_iw_contact_shortcode($atts, $content = null) {
            $output = $answer = $question = $class = '';
            extract(shortcode_atts(array(
				'receiver_email' => '',
                'title' => '',
                'sub_title' => '',
                'show_name' => 'yes',
                'show_email' => 'yes',
                'show_message' => 'yes',
				'show_captcha' => 'yes',
                'show_gdpr' => 'yes',
                'show_with_map' => 'yes',
                'style' => '',
                'class' => ''
                            ), $atts));
            return $this->htmlBoxIW_ContactRender($receiver_email,$title, $sub_title, $show_name, $show_email, $show_message, $show_with_map, $class,$style, $show_captcha, $show_gdpr);
        }

        function htmlBoxIW_ContactRender($receiver_email,$title, $sub_title, $show_name, $show_email, $show_message, $show_with_map, $class,$style, $show_captcha, $show_gdpr) {
            ob_start();
            global $smof_data;
			$current_user = wp_get_current_user();
			$current_name = '';
			$current_email = '';
			if($current_user->ID){
				$current_name = $current_user->display_name ;
				$current_email = $current_user->user_email ;
			}
			if($style =='widget'):
			?>
			<form name="" method="post" class="main-contact-form">
			<div class="info"><?php echo $title ?></div>
			<div class="email">
				<?php if ($show_name == 'yes'): ?>
					<input type="text" value="<?php echo $current_name ?>" placeholder="<?php echo __('Your Name', 'inwavethemes'); ?>" required="required" class="inputbox" name="name">
					<?php
				endif;
				if ($show_email == 'yes'): ?>
					<input type="email" title="E-mail" value="<?php echo $current_email ?>" name="email" class="inputbox" required="required"  placeholder="<?php echo __('Your Email', 'inwavethemes'); ?>">
				<?php endif; 
				if ($show_message == 'yes'):?>
					<textarea placeholder="<?php echo __('Your Message', 'inwavethemes'); ?>" rows="8" class="inputbox" required="required" name="message"></textarea>  
				<?php endif; ?>
				<input name="action" type="hidden" value="sendMessageContact">
				<input name="mailto" type="hidden" value="<?php echo $receiver_email; ?>">
				<button class="button" title="Submit" type="submit"><i class="fa fa-arrow-right"></i></button>
			</div>
			<div class="contact-ajax-overlay">
			
				<span class="ajax-contact-loading"><i class="fa fa-spinner fa-spin fa-2x"></i></span>
			</div>
			<div class="contact-ajax-message"></div>
			</form>
			<?php
			else:
            ?>
            <div class="contact-submit<?php echo $show_with_map == 'yes' ? '' : ' no-map' ?>">
                <?php if ($show_with_map == 'yes'): ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12">
                            <?php endif; ?>
                            <div class="contact">
                                <div class="contact-ajax-overlay">
                                    <span class="ajax-contact-loading"><i class="fa fa-spinner fa-spin fa-2x"></i></span>
                                </div>
                                <?php
                                if ($title) {
                                    echo '<h4>' . $title . '</h4>';
                                }
                                ?>
                                <div class="headding-bottom"></div>
                                <form method="post" name="contact-form" class="main-contact-form row" id="main-contact-form">
                                    <?php if ($show_name == 'yes'): ?>
                                        <div class="form-group col-md-12">
                                            <input type="text" value="<?php echo $current_name ?>" placeholder="<?php echo __('Your Name', 'inwavethemes'); ?>" required="required" class="control" name="name">
                                        </div>
                                        <?php
                                    endif;
                                    if ($show_email == 'yes'):
                                        ?>
                                        <div class="form-group col-md-12">
                                            <input type="email" value="<?php echo $current_email ?>" placeholder="<?php echo __('Your Email', 'inwavethemes'); ?>" required="required" class="control" name="email">
                                        </div>
                                        <?php
                                    endif;
                                    if ($show_message == 'yes'):
                                        ?>
                                        <div class="form-group col-md-12">
                                            <textarea placeholder="<?php echo __('Your Message', 'inwavethemes'); ?>" rows="8" class="control" required="required" id="message" name="message"></textarea>
                                        </div>  
                                    <?php endif; ?>
									<?php if ($show_captcha == 'yes'):
										$rand_captcha = $this->getCaptchaCode();
                                    ?>
                                        <div class="form-group col-md-12 capcha-group">
                                            <input required="required" placeholder="<?php echo __('Captcha', 'inwavethemes'); ?>" name="captcha" value="" class="captcha control"/> 
                                            <label data-value="<?php echo $rand_captcha; ?>" class="captcha-view"><?php echo $rand_captcha; ?></label>
                                            <span class="reload-captcha"><i class="fa fa-spin"></i></span>
                                        </div>
                                        <style>
                                            .contact-submit input.captcha{width: 150px;}
                                            .contact-submit .captcha-view{background:url("http://4.bp.blogspot.com/-EEMSa_GTgIo/UpAgBQaE6-I/AAAAAAAACUE/jdcxZVXelzA/s1600/ca.png") repeat center center scroll; color:#ff0000; font-size:18px; height:35px; line-height:33px;padding:0 15px; margin:0 0 0 10px;}
                                        </style>
                                    <?php endif; ?>
                                    <?php if ($show_gdpr == 'yes'):?>
                                    <div class="form-group col-md-12">
                                        <input value="1" type="checkbox" required="required" id="gdpr" class="iwjmb-checkbox " name="gdpr" checked="checked">
                                        <?php 
                                            $gdpr_link_before = ( isset( $smof_data['gdpr_link'] ) && $smof_data['gdpr_link'] ) ? '<a class="theme-color" href="'.$smof_data['gdpr_link'].'">' : '<label for="gdpr">';
                                            $gdpr_link_after = ( isset( $smof_data['gdpr_link'] ) && $smof_data['gdpr_link'] ) ? '</a>' : '</label>';
                                            if ( isset( $smof_data['gdpr_text'] ) && $smof_data['gdpr_text'] ) { 
                                                echo $gdpr_link_before;
                                                echo $smof_data['gdpr_text']; 
                                                echo $gdpr_link_after;
                                            }
                                        ?>
                                        <?php ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="form-group form-submit col-md-12">
                                        <input name="action" type="hidden" value="sendMessageContact">
										<input name="mailto" type="hidden" value="<?php echo $receiver_email; ?>">
                                        <button class="btn-submit" name="submit" type="submit"><i class="fa fa-envelope-o"></i>
                                            <?php echo __('Send Message', 'inwavethemes'); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php if ($show_with_map == 'yes'): ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php
			endif;
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

		function getCaptchaCode() {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 5; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
		
        //Ajax iwcSendMailTakeCourse
        function sendMessageContact() {
            $result = array();
            $result['success'] = false;
            $mailto = isset($_POST['mailto'])? $_POST['mailto'] : '';
            if(!$mailto){
                $mailto = get_option('admin_email');
            }

            $email = isset($_POST['email'])? $_POST['email'] : '';
            $name = isset($_POST['name'])? $_POST['name'] : '';
            $headers = "From: [".$name."] <" . $email . "> \r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
            $message = isset($_POST['message'])? $_POST['message'] : '';
			if(!$message){
				// email subscription
				$title = __('Email subscription', 'inwavethemes') . ' ['. $email.']';
				$text = __('Hi Admin,', 'inwavethemes')."\n" . __('This email was sent from "Email subscription" form', 'inwavethemes') . "\n" 
						. __('Name', 'inwavethemes') . ': ' . $name . "\n" 
						. __('Email', 'inwavethemes') . ': ' . $email . "\n";
			}else{
				// contact message
				$title = __('Email from Contact', 'inwavethemes') . ' ['. $email.']';
				$text = __('Hi Admin,', 'inwavethemes')."\n" . __('This email was sent from "CONTACT" form', 'inwavethemes') . "\n" 
						. __('Name', 'inwavethemes') . ': ' . $name . "\n" 
						. __('Email', 'inwavethemes') . ': ' . $email . "\n" 
						. __('Message', 'inwavethemes') . ': ' . $message . '';
			}
		if (wp_mail($mailto, $title, $text, $headers)) {
			$result['success'] = true;
			$result['message'] = __('Thank you for your submission, we will contact you soon!', 'inwavethemes');
		} else {
			$result['message'] = __('Can\'t send email, please try again', 'inwavethemes');
		}
		echo json_encode($result);
		exit();
        }

    }

}

new Inwave_IW_Contact();
if (class_exists('WPBakeryShortCode')) {

    class WPBakeryShortCode_Inwave_IW_Contact extends WPBakeryShortCode {
        
    }

}
