
<?php
/**
 * @version   1.2 February 11, 2016
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('ABSPATH') or die('Restricted access');

global $gantry;

$gantry->addBodyClass( 'menu-comingsoon' );

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));
?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
	<head>
		<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
		<meta name="viewport" content="width=960px">
		<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
		<meta name="viewport" content="width=1200px">
		<?php else : ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php endif; ?>

		<?php
		$gantry->displayHead();

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

		$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
		$gantry->addLess('comingsoon.less', 'comingsoon.css', 4, $lessVariables);

		// Scripts
		if ($gantry->browser->name == 'ie'){
			if ($gantry->browser->shortversion == 8){
				$gantry->addScript('html5shim.js');
				$gantry->addScript('placeholder-ie.js');
			}
			if ($gantry->browser->shortversion == 9){
				$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
				$gantry->addScript('placeholder-ie.js');
			}
		}
		if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');

		// Simple Counter
		$gantry->addScript('simplecounter.js');
		$gantry->addInlineScript("
			window.addEvent('load', function(){ 
				var counter = new SimpleCounter(
					'rt-comingsoon-counter',
					/* Year (full year), Month (0 to 11), Day (1, 31) */
					/* For example: Date(2016,10,1) means 1 November 2020 */			
					new Date('".$gantry->get('comingsoon-year')."','".$gantry->get('comingsoon-month')."','".$gantry->get('comingsoon-date')."'),
					{lang : {      
						d:{single:'"._r( 'Day' )."',plural:'"._r( 'Days' )."'}, 		//days
						h:{single:'"._r( 'Hour' )."',plural:'"._r( 'Hours' )."'},     	//hours
						m:{single:'"._r( 'Minute' )."',plural:'"._r( 'Minutes' )."'}, 	//minutes
						s:{single:'"._r( 'Second' )."',plural:'"._r( 'Seconds' )."'} 	//seconds
					}
				});
			});
		");
		?>
	</head>
	<body id="rt-comingsoon-page" <?php echo $gantry->displayBodyTag(); ?>>
		<div id="rt-page-surround">
			<?php /** Begin Header Surround **/ ?>
			<header id="rt-header-surround">
				<div class="rt-container">
					<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
					<div id="rt-header">
						<div class="rt-flex-container">
							<div class="rt-comingsoon-body">
								<div class="rt-logo-block rt-comingsoon-logo">
									<a id="rt-logo" href="<?php echo home_url(); ?>"></a>
								</div>

								<div class="rt-comingsoon-title rt-big-title rt-center">
									<div class="module-title">
										<h2 class="title"><?php _re( 'Our Website is Coming Soon' ); ?></h2>
									</div>
								</div>

								<p class="rt-comingsoon-message">
									<?php _re( 'Tessellate supports a simple coming soon or offline style page with a time counter. It has been specifically styled to match the theme. This feature can be enabled in Admin Dashboard &rarr; Tessellate Theme &rarr; Gizmos &rarr; Coming Soon Page. Please visit <a href="http://www.rockettheme.com/">Tessellate tutorials</a> for more information.' ); ?>
								</p>

								<div class="rt-counter-block">
									<div id="rt-comingsoon-counter"></div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<?php /** End Header **/ endif; ?>
				</div>
			</header>
			<?php /** End Header Surround **/ ?>

			<?php /** Begin Main Section **/ ?>
			<section id="rt-mainbody-surround">
				<div class="rt-container">
					<?php /** Begin Extension **/ ?>
					<div id="rt-extension">
						<?php if ($gantry->get('email-subscription-enabled')) : ?>
							<div class="rt-subscription-form">
								<h2 class="rt-subscription-title">
									<?php _re( 'Get Notified When We Release' ); ?>
								</h2>
								<form class="rt-comingsoon-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $gantry->get('feedburner-uri'); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
									<input type="text" placeholder="<?php _re( 'Email Address' ); ?>" class="inputbox" name="email">
									<input type="hidden" value="<?php echo $gantry->get('feedburner-uri'); ?>" name="uri"/>
									<input type="hidden" name="loc" value="en_US"/>
									<input type="submit" name="Submit" class="readon" value="<?php _re( 'Subscribe' ); ?>" />
								</form>
							</div>
							<div class="clear"></div>
						<?php endif; ?>
						<div class="clear"></div>
					</div>
					<?php /** End Extension **/ ?>
				</div>
			</section>
			<?php /** End Main Section **/ ?>

			<?php /** Begin Footer Section **/ ?>
			<footer id="rt-footer-surround">
				<div class="rt-container">
					<?php /** Begin Footer **/ ?>
					<div id="rt-footer">
						<div class="rt-authorized-form">
							<h2 class="rt-authorized-form-title"><?php _re('Authorized Login'); ?></h2>

							<p class="rt-authorized-login-message">
								<?php _re('This gizmo hides your WordPress site behind the Coming Soon page with its Countdown timer. You can still access the frontend of the site by logging in as an administrator below. You can customize this message in the Tessellate theme language file.'); ?>
							</p>

							<?php if(!is_super_admin()): ?>
								<form class="rt-authorized-login-form" action="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>" method="post" id="rt-form-login">
									<input name="log" id="username" class="inputbox" type="text" placeholder="<?php _re('User Name'); ?>" />
									<input type="password" name="pwd" class="inputbox" placeholder="<?php _re('Password'); ?>" />
									<input type="hidden" name="rememberme" class="inputbox" value="yes" id="remember" />
									<input type="submit" name="Submit" class="readon" value="<?php _re('Log in'); ?>" />				
								</form>
							<?php endif; ?>
							<?php if(is_super_admin()): ?>
								<form class="rt-authorized-login-form" id="rt-form-login">
									<a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>" class="readon" title="<?php _re('Log out'); ?>"><?php _re('Log out'); ?></a>
								</form>	
							<?php endif; ?>
							<div class="clear"></div>
						</div>
					</div>
					<?php /** End Footer **/ ?>
					<?php /** Begin Copyright **/ ?>
					<div id="rt-copyright">
						<div class="rt-flex-container">
							<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
					<?php /** End Copyright **/ ?>
				</div>
			</footer>
			<?php /** End Footer Surround **/ ?>
		</div>
		<?php $gantry->displayFooter(); ?>
	</body>
</html>
<?php
$gantry->finalize();
?>