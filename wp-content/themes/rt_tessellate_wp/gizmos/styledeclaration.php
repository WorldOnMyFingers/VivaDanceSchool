<?php
/**
 * @version   1.2 February 11, 2016
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrygizmo' );
gantry_import( 'core.utilities.gantrymobiledetect' );

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoStyleDeclaration extends GantryGizmo {

	var $_name = 'styledeclaration';
	
	function isEnabled(){
		global $gantry;
		$menu_enabled = $this->get('enabled');

		if (1 == (int)$menu_enabled) return true;
		return false;
	}

	function query_parsed_init() {
		global $gantry;
		$browser = $gantry->browser;
		$detect  = new GantryMobileDetect();

		// Logo
		$css = $this->buildLogo();

		// Less Variables
		$lessVariables = array(
			'accent-color1'                     => $gantry->get('accent-color1',                    '#E35614'),
			'accent-color2'                     => $gantry->get('accent-color2',                    '#338DE0'),
			'accent-color3'                     => $gantry->get('accent-color3',                    '#338DE0'),
			'accent-color4'                     => $gantry->get('accent-color4',                    '#338DE0'),

			'headermode-type'                   => $gantry->get('headermode-type',                  'static'),

			'headersurround-bgcolor1'           => $gantry->get('headersurround-bgcolor1',          '#262B3F'),
			'headersurround-bgcolor2'           => $gantry->get('headersurround-bgcolor2',          '#A9B8EF'),
			'headersurround-type'               => $gantry->get('headersurround-type',              'preset1'),


			'header-textcolor'                  => $gantry->get('header-textcolor',                 '#72698A'),
			'header-background'                 => $gantry->get('header-background',                '#1D182C'),
			'header-type'                       => $gantry->get('header-type',                      'preset1'),

			'headerbottom-gap'                  => $gantry->get('headerbottom-gap',                 '160'),

			'top-textcolor'                     => $gantry->get('top-textcolor',                    '#FFFFFF'),
			'top-background'                    => $gantry->get('top-background',                   'transparent'),

			'showcase-textcolor'                => $gantry->get('showcase-textcolor',               '#72698A'),
			'showcase-background'               => $gantry->get('showcase-background',              '#1D182C'),

			'featuresurround-background'        => $gantry->get('featuresurround-background',       '#F7F1F5'),

			'feature-textcolor'                 => $gantry->get('feature-textcolor',                '#1D182C'),
			'feature-background'                => $gantry->get('feature-background',               '#FFFFFF'),

			'bodytopsurround-background'        => $gantry->get('bodytopsurround-background',       '#1D182C'),

			'mainbody-overlay'                  => $gantry->get('mainbody-overlay',                 'light'),
			'mainbodysurround-background'       => $gantry->get('mainbodysurround-background',      '#EFE9ED'),
			'mainbody-background'               => $gantry->get('mainbody-background',              '#FFFFFF'),

			'bodybottomsurround-background'     => $gantry->get('bodybottomsurround-background',    '#01ACDE'),

			'extensionsurround-background'      => $gantry->get('extensionsurround-background',     '#1D182C'),

			'footersurround-background'         => $gantry->get('footersurround-background',        '#F7F1F5'),

			'extension-textcolor'               => $gantry->get('extension-textcolor',              '#FFFFFF'),
			'extension-background'              => $gantry->get('extension-background',             '#E35614'),

			'bottom-textcolor'                  => $gantry->get('bottom-textcolor',                 '#FFFFFF'),
			'bottom-background'                 => $gantry->get('bottom-background',                '#1C1D1F'),

			'footer-textcolor'                  => $gantry->get('footer-textcolor',                 '#808080'),
			'footer-background'                 => $gantry->get('footer-background',                '#1C1D1F')
		);

		// Custom Background Images
		$positions	= array('headersurround-customheadersurround-image');
		$source		= "";

		foreach ($positions as $position) {
			$data = $gantry->get($position, false) ? json_decode(str_replace("'", '"', $gantry->get($position))) : false; 
			if ($data) $source = $data->path;
			$lessVariables[$position] = $data ? 'url(' . $source . ')' : 'none';
		}

		// Section Background Images

		$gantry->addInlineStyle($css);   

		$gantry->addLess('global.less', 'master.css', 7, $lessVariables);

		$this->_disableRokBoxForiPhone();

		if ($gantry->get('layout-mode')=="responsive") {
			$gantry->addLess('mediaqueries.less');
			$gantry->addLess('grid-flexbox-responsive.less');
			$gantry->addLess('menu-dropdown-direction.less');
		}
		if ($gantry->get('layout-mode')=="960fixed")   $gantry->addLess('960fixed.less');
		if ($gantry->get('layout-mode')=="1200fixed")  $gantry->addLess('1200fixed.less');

		// RTL
		if ($gantry->get('rtl-enabled') && is_rtl()) $gantry->addLess('rtl.less', 'rtl.css', 8, $lessVariables);

		// Demo Styling
		if ($gantry->get('demo')) $gantry->addLess('demo.less', 'demo.css', 9, $lessVariables);

		// Chart Script
		if ($gantry->get('chart-enabled'))  {
			$gantry->addScript('chart.js');
			if ($gantry->browser->name != 'ie') {
				$gantry->addLess('canvas.less');
			}
		}

		// Animated Header
		if (!$detect->isTablet() && !$detect->isMobile() && $gantry->get('headermode-type') == 'animated') {
			$this->animatedHeader();
		}

		// Add inline css from the Custom CSS field
		$gantry->addInlineStyle($gantry->get('customcss'));
	}

	function animatedHeader(){
		global $gantry;
		$browser = $gantry->browser;

		// load fss script, defaults and config
		$gantry->addScript('fss.js');
		$gantry->addScript('fss-defaults.js');
		$gantry->addScript('fss-config.js');

		$script = "window.addEvent('domready', function(){
			this.RENDERER = '".($browser == 'chrome' ? 'webgl' : 'canvas')."';
			if (typeof this.MESH != 'undefined'){
				this.MESH.ambient = '".$gantry->get('headersurround-bgcolor1')."';
				this.MESH.diffuse = '".$gantry->get('headersurround-bgcolor2')."';
			}
			if (typeof this.LIGHT != 'undefined'){
				this.LIGHT.ambient = '".$gantry->get('headersurround-bgcolor1')."';
				this.LIGHT.diffuse = '".$gantry->get('headersurround-bgcolor2')."';
			}
		});";

		$gantry->addInlineScript($script);
	}

	function buildLogo(){
		global $gantry;

		if ($gantry->get('logo-type')!="custom") return "";

		$source = $width = $height = "";

		$logo = str_replace("&quot;", '"', str_replace("'", '"', $gantry->get('logo-custom-image')));
		$data = json_decode($logo);

		if (!$data){
			if (strlen($logo)) $source = $logo;
			else return "";
		} else {
			$source = $data->path;
		}

		if(!is_ssl()) {
			if(substr($source, 0, 5) == 'https') {
				$source = str_replace('https', 'http', $source);
			}
		} else {
			if(substr($source, 0, 5) != 'https') {
				$source = str_replace('http', 'https', $source);
			}
		}

		$baseUrl = trailingslashit(site_url());

		if (substr($baseUrl, 0, strlen($baseUrl)) == substr($source, 0, strlen($baseUrl))){
			$file = trailingslashit(ABSPATH) . substr($source, strlen($baseUrl));
		} else {
			$file = trailingslashit(ABSPATH) . $source;
		}

		if (isset($data->width) && isset($data->height)){
			$width = $data->width;
			$height = $data->height;
		} else {
			$size = @getimagesize($file);
			$width = $size[0];
			$height = $size[1];
		}

		$source = str_replace(' ', '%20', $source);

		$output = "";
		$output .= "#rt-logo {background: url(".$source.") 50% 0 no-repeat !important;}"."\n";
		$output .= "#rt-logo {width: ".$width."px;height: ".$height."px;}"."\n";

		$file = preg_replace('/\//i', DIRECTORY_SEPARATOR, $file);

		return (file_exists($file)) ? $output : '';
	}

	function _disableRokBoxForiPhone() {
		global $gantry;

		if ($gantry->browser->platform == 'iphone' || $gantry->browser->platform == 'android') {
			$gantry->addInlineScript("window.addEvent('domready', function() {\$\$('a[rel^=rokbox]').removeEvents('click');});");
		}
	}
}