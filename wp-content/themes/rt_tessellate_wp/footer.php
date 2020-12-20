<?php
/**
 * @version   1.2 February 11, 2016
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry; ?>					
					<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard', null, ob_get_clean()); ?>
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