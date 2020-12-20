<?php
/**
 * @version   $Id: item.php 22315 2014-07-22 14:00:19Z arifin $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 */
$itemIcon 		= $item->getParam('lists_item_icon');

?>
<?php if($parameters->get('lists_enable_accordion')): ?>
	<li <?php if (!$parameters->get('lists_enable_accordion') || $index == 0): ?>class="active" <?php endif;?>data-lists-item>
		<?php if ($item->custom_can_show_title): ?>
		<h4 class="sprocket-lists-title<?php if ($parameters->get('lists_enable_accordion')): ?> padding<?php endif; ?>" data-lists-toggler>
			<?php if ($item->custom_can_have_link): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
				<?php if($itemIcon) : ?>
					<span class="rt-icon-left <?php echo $itemIcon ;?>"></span>
				<?php endif; ?>
				<?php echo $item->getTitle();?>
			<?php if ($item->custom_can_have_link): ?></a><?php endif; ?>
			<span class="indicator"><span>+</span></span>
		</h4>
		<?php endif; ?>
		<span class="sprocket-lists-item" data-lists-content>
			<span class="sprocket-padding">
				<?php if ($item->getPrimaryImage()) :?>
				<span class="sprocket-lists-image">
					<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" alt="" />
				</span>
				<?php endif; ?>
				<span class="sprocket-lists-text">
					<span class="sprocket-lists-desc <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
						<?php echo $item->getText(); ?>
					</span>
					<?php if ($item->getPrimaryLink()) : ?>
					<span class="readon-wrapper <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
						<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
					</span>
					<?php endif; ?>
				</span>
			</span>
		</span>
	</li>
<?php endif; ?>

<?php if (!$parameters->get('lists_enable_accordion')): ?>
	<li <?php if (!$parameters->get('lists_enable_accordion') || $index == 0): ?>class="active" <?php endif;?>data-lists-item>
		<span class="sprocket-lists-item" data-lists-content>
			<span class="sprocket-padding">
				<?php if ($item->getPrimaryImage()) :?>
				<span class="sprocket-lists-image">
					<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" alt="" />
				</span>
				<?php endif; ?>
				<span class="sprocket-lists-text">
					<?php if ($item->custom_can_show_title): ?>

					<span class="sprocket-lists-infos <?php if (($parameters->get('lists_article_details') == 'author') or ($parameters->get('lists_article_details') == '1')) echo "author-enabled"; ?> <?php if (($parameters->get('lists_article_details') == 'date') or ($parameters->get('lists_article_details') == '1')) echo "date-enabled"; ?>">
						<?php if (($parameters->get('lists_article_details') == 'author') or ($parameters->get('lists_article_details') == '1')) :?>
							<span class="author"><?php echo $item->getAuthor(); ?> / </span>
						<?php endif; ?>
						<?php if (($parameters->get('lists_article_details') == 'date') or ($parameters->get('lists_article_details') == '1')) :?>
						<span class="date">
							<?php if(($date = date_create($item->getDate())) !== false ){
							echo $item->getDate();
							} ?>
						</span>
						<?php endif; ?>
					</span>

					<span class="sprocket-lists-title" data-lists-toggler>
						<?php if ($item->custom_can_have_link): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
							<?php if($itemIcon) : ?>
								<span class="rt-icon-left <?php echo $itemIcon ;?>"></span>
							<?php endif; ?>
							<?php echo $item->getTitle();?>
						<?php if ($item->custom_can_have_link): ?></a><?php endif; ?>
					</span>
					<?php endif; ?>
					<span class="sprocket-lists-desc <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
						<?php echo $item->getText(); ?>
					</span>
					<?php if ($item->getPrimaryLink()) : ?>
					<span class="readon-wrapper <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
						<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
					</span>
					<?php endif; ?>
				</span>
			</span>
		</span>
	</li>
<?php endif; ?>