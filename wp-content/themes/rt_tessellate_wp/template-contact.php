<?php
/**
 * Template Name: Contact Form
 */
/**
 * @version   1.2 February 11, 2016
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry;

$gantry->addBodyClass( 'menu-contact-us' );

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
<?php /* Head */
	$gantry->displayHead();
?>
<?php /* Force IE to Use the Most Recent Version */ if ($gantry->browser->name == 'ie') : ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php endif; ?>

<?php
	$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
	if ($gantry->browser->name == 'ie'){
		if ($gantry->browser->shortversion == 8){
			$gantry->addScript('html5shim.js');
			$gantry->addScript('canvas-unsupported.js');
			$gantry->addScript('placeholder-ie.js');
		}
		if ($gantry->browser->shortversion == 9){
			$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
			$gantry->addScript('placeholder-ie.js');
		}
	}
	if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
?>
</head>
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-page-surround">
		<?php /** Begin Header Surround **/ if ($gantry->countModules('header') or $gantry->countModules('drawer') or $gantry->countModules('top') or $gantry->countModules('showcase') or $gantry->countModules('breadcrumb')) : ?>
		<header id="rt-header-surround">
		    <div id="rt-fss-container" class="rt-fss-container">
		      	<div id="rt-fss-output" class="rt-fss-container"></div>
		    </div>
			<div class="rt-container">
				<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
				<div id="rt-header">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('header','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Header **/ endif; ?>
				<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
				<div id="rt-drawer">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('drawer','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Drawer **/ endif; ?>
				<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
				<div id="rt-top">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('top','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Top **/ endif; ?>
				<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
				<div id="rt-showcase">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Showcase **/ endif; ?>
				<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
				<div id="rt-breadcrumbs">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Breadcrumbs **/ endif; ?>
			</div>
		</header>
		<?php /** End Header Surround **/ endif; ?>

		<?php /** Begin FullWidthTop **/ if ($gantry->countModules('fullwidthtop')) : ?>
		<div id="rt-fullwidthtop">
			<?php echo $gantry->displayModules('fullwidthtop','basic','standard'); ?>
			<div class="clear"></div>
		</div>
		<?php /** End FullWidthTop **/ endif; ?>

		<?php /** Begin Feature Section **/ if ($gantry->countModules('feature') or $gantry->countModules('utility')) : ?>
		<section id="rt-feature-surround">
			<div class="rt-container">
				<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
				<div id="rt-feature">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('feature','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Feature **/ endif; ?>
				<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
				<div id="rt-utility">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('utility','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Utility **/ endif; ?>
			</div>
		</section>
		<?php /** End Feature Section **/ endif; ?>

		<?php /** Begin BodyTop Section **/ if ($gantry->countModules('expandedtop') or $gantry->countModules('maintop')) : ?>
		<section id="rt-bodytop-surround">
			<div class="rt-container">
				<?php /** Begin Expanded Top **/ if ($gantry->countModules('expandedtop')) : ?>
				<div id="rt-expandedtop">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('expandedtop','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Expanded Top **/ endif; ?>
				<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
				<div id="rt-maintop">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Main Top **/ endif; ?>
			</div>
		</section>
		<?php /** End BodyTop Section **/ endif; ?>

		<?php /** Begin Main Section **/ ?>
		<section id="rt-mainbody-surround">
			<div class="rt-container">
				<?php /** Begin Main Body **/ ?>
				<?php echo $gantry->displayMainbody('contactform','sidebar','standard','standard','standard','standard','standard'); ?>
				<?php /** End Main Body **/ ?>
			</div>
		</section>
		<?php /** End Main Section **/ ?>

		<?php /** Begin BodyBottom Section **/ if ($gantry->countModules('mainbottom') or $gantry->countModules('expandedbottom')) : ?>
		<section id="rt-bodybottom-surround">
			<div class="rt-container">
				<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
				<div id="rt-mainbottom">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Main Bottom **/ endif; ?>
				<?php /** Begin Expanded Bottom **/ if ($gantry->countModules('expandedbottom')) : ?>
				<div id="rt-expandedbottom">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('expandedbottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Expanded Bottom **/ endif; ?>
			</div>
		</section>
		<?php /** End BodyBottom Section **/ endif; ?>

		<?php /** Begin Extension Section **/ if ($gantry->countModules('extension')) : ?>
		<section id="rt-extension-surround">
			<div class="rt-container">
				<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
				<div id="rt-extension">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('extension','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Extension **/ endif; ?>
			</div>
		</section>
		<?php /** End Extension Section **/ endif; ?>

		<?php /** Begin FullWidthBottom **/ if ($gantry->countModules('fullwidthbottom')) : ?>
		<div id="rt-fullwidthbottom">
			<?php echo $gantry->displayModules('fullwidthbottom','basic','standard'); ?>
			<div class="clear"></div>
		</div>
		<?php /** End FullWidthBottom **/ endif; ?>

		<?php /** Begin Footer Section **/ if ($gantry->countModules('extension') or $gantry->countModules('bottom') or $gantry->countModules('footer') or $gantry->countModules('copyright')) : ?>
		<footer id="rt-footer-surround">
			<div class="rt-container">
				<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
				<div id="rt-bottom">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Bottom **/ endif; ?>
				<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
				<div id="rt-footer">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('footer','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Footer **/ endif; ?>
				<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
				<div id="rt-copyright">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Copyright **/ endif; ?>
			</div>
		</footer>
		<?php /** End Footer Surround **/ endif; ?>

		<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
		<div id="rt-debug">
			<div class="rt-flex-container">
				<?php echo $gantry->displayModules('debug','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Debug **/ endif; ?>

		<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
		<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
		<?php /** End Analytics **/ endif; ?>

		<?php /** Popup Login and Popup Module **/ ?>
		<?php echo $gantry->displayModules('login','login','popup'); ?>
		<?php echo $gantry->displayModules('popup','popup','popup'); ?>
		<?php /** End Popup Login and Popup Module **/ ?>
	</div>
	<?php $gantry->displayFooter(); ?>
</body>
</html>
<?php
$gantry->finalize();
?>