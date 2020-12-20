<?php
/**
 * @version   $Id: copyright.php 60342 2014-01-03 17:12:22Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetCopyright", "init"));

class GantryWidgetCopyright extends GantryWidget
{
	var $short_name = 'copyright';
	var $wp_name = 'gantry_copyright';
	var $long_name = 'Gantry Copyright';
	var $description = 'Gantry Copyright Widget';
	var $css_classname = 'widget_gantry_copyright';
	var $width = 200;
	var $height = 400;

	static function init()
	{
		register_widget("GantryWidgetCopyright");
	}

	function render_widget_open( $args, $instance ) {
    }
    
    function render_widget_close( $args, $instance ) {
    }
    
    function pre_render( $args, $instance ) {
    }
    
    function post_render( $args, $instance ) {
    }

	function render($args, $instance) {
	/** @global $gantry Gantry */
	global $gantry;

		ob_start(); 
		?>

		<?php 

		$text = $instance['text'];
		$text = str_replace( '%YEAR%', date( 'Y' ), $text );
		$text = str_replace( '%year%', date( 'y' ), $text );

		?>

		<div id="<?php echo $this->id; ?>" class="rt-copyright-content rt-block <?php echo $this->css_classname; ?> widget">
			<?php echo $text; ?>
		</div>
		
		<?php
		echo ob_get_clean();
	}
}
