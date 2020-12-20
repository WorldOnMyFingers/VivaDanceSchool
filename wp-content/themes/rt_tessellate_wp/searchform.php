<?php
/**
 * @version   1.2 February 11, 2016
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry;
?>

<form role="search" method="get" id="searchform" class="" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search rt-suspend">
		<input type="text" class="inputbox rt-suspend" name="s" id="mod-search-searchword" onfocus="if (this.value=='<?php esc_attr_e( 'Search...' ); ?>') this.value='';" onblur="if (this.value=='') this.value='<?php esc_attr_e( 'Search...' ); ?>';" value="<?php echo wp_kses( get_search_query(), null ); ?>" />
		<input type="submit" class="button rt-suspend" id="searchsubmit" value="<?php esc_attr_e( 'Go' ); ?>" />
	</div>
</form>