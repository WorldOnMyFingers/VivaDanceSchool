<?php
//about theme info
add_action( 'admin_menu', 'nataraj_dance_studio_gettingstarted' );
function nataraj_dance_studio_gettingstarted() {    	
	add_theme_page( esc_html__('About Theme', 'nataraj-dance-studio'), esc_html__('About Theme', 'nataraj-dance-studio'), 'edit_theme_options', 'nataraj_dance_studio_guide', 'nataraj_dance_studio_mostrar_guide');   
}

// Add a Custom CSS file to WP Admin Area
function nataraj_dance_studio_admin_theme_style() {
   wp_enqueue_style('custom-admin-style', get_template_directory_uri() . '/inc/getting-started/getting-started.css');
}
add_action('admin_enqueue_scripts', 'nataraj_dance_studio_admin_theme_style');

//guidline for about theme
function nataraj_dance_studio_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'nataraj-dance-studio' );

?>

<div class="wrapper-info">
	<div class="col-left">
		<div class="intro">
			<h3><?php esc_html_e( 'Welcome to Nataraj Dance Studio WordPress Theme', 'nataraj-dance-studio' ); ?> <span>Version: <?php echo esc_html($theme['Version']);?></span></h3>
		</div>
		<div class="started">
			<hr>
			<div class="free-doc">
				<div class="lz-4">
					<h4><?php esc_html_e( 'Start Customizing', 'nataraj-dance-studio' ); ?></h4>
					<ul>
						<span><?php esc_html_e( 'Go to', 'nataraj-dance-studio' ); ?> <a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e( 'Customizer', 'nataraj-dance-studio' ); ?> </a> <?php esc_html_e( 'and start customizing your website', 'nataraj-dance-studio' ); ?></span>
					</ul>
				</div>
				<div class="lz-4">
					<h4><?php esc_html_e( 'Support', 'nataraj-dance-studio' ); ?></h4>
					<ul>
						<span><?php esc_html_e( 'Send your query to our', 'nataraj-dance-studio' ); ?> <a href="<?php echo esc_url( NATARAJ_DANCE_STUDIO_SUPPORT ); ?>" target="_blank"> <?php esc_html_e( 'Support', 'nataraj-dance-studio' ); ?></a></span>
					</ul>
				</div>
			</div>
			<p><?php esc_html_e( 'Nataraj Dance Studio is a bold, powerful, visually impressive and stylish WordPress dance theme for dance clubs and studios, dance classes, martial art training classes, zumba classes, aerobics classes, yoga centres and other physical training institutes. It is a fantastic theme with so much potential to satisfy visitors with unending possibilities of designing. This theme is fully responsive looking gorgeous on varying screen sizes without breaking. It is cross-browser compatible, translation ready, SEO friendly and supports RTL writing. You can do profound customization through theme customizer to change its colour, background, logo, menu, images etc. Large size sliders and banners will help you give the website an attractive look. It has a beautifully designed gallery to show your talented work. The theme is feathery light and loads with a lightning fast speed. It is rigorously tested with different plugins to ensure its smooth compatibility with third party plugins. Social media icons will ease the process of sharing your content on different networking platforms.', 'nataraj-dance-studio')?></p>
			<hr>			
			<div class="col-left-inner">
				<h3><?php esc_html_e( 'Get started with Free Dance Theme', 'nataraj-dance-studio' ); ?></h3>
				<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/customizer-image.png" alt="" />
			</div>
		</div>
	</div>
	<div class="col-right">
		<div class="col-left-area">
			<h3><?php esc_html_e('Premium Theme Information', 'nataraj-dance-studio'); ?></h3>
			<hr>
		</div>
		<div class="centerbold">
			<a href="<?php echo esc_url( NATARAJ_DANCE_STUDIO_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'nataraj-dance-studio'); ?></a>
			<a href="<?php echo esc_url( NATARAJ_DANCE_STUDIO_BUY_NOW ); ?>"><?php esc_html_e('Buy Pro', 'nataraj-dance-studio'); ?></a>
			<a href="<?php echo esc_url( NATARAJ_DANCE_STUDIO_PRO_DOCS ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'nataraj-dance-studio'); ?></a>
			<hr class="secondhr">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/nataraja-dance-studio2.jpg" alt="" />
		</div>
		<h3><?php esc_html_e( 'PREMIUM THEME FEATURES', 'nataraj-dance-studio'); ?></h3>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon01.png" alt="" />
			<h4><?php esc_html_e( 'Banner Slider', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon02.png" alt="" />
			<h4><?php esc_html_e( 'Theme Options', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon03.png" alt="" />
			<h4><?php esc_html_e( 'Custom Innerpage Banner', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon04.png" alt="" />
			<h4><?php esc_html_e( 'Custom Colors and Images', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon05.png" alt="" />
			<h4><?php esc_html_e( 'Fully Responsive', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon06.png" alt="" />
			<h4><?php esc_html_e( 'Hide/Show Sections', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon07.png" alt="" />
			<h4><?php esc_html_e( 'Woocommerce Support', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon08.png" alt="" />
			<h4><?php esc_html_e( 'Limit to display number of Posts', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon09.png" alt="" />
			<h4><?php esc_html_e( 'Multiple Page Templates', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon10.png" alt="" />
			<h4><?php esc_html_e( 'Custom Read More link', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon11.png" alt="" />
			<h4><?php esc_html_e( 'Code written with WordPress standard', 'nataraj-dance-studio'); ?></h4>
		</div>
		<div class="lz-6">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/icon12.png" alt="" />
			<h4><?php esc_html_e( '100% Multi language', 'nataraj-dance-studio'); ?></h4>
		</div>
	</div>
</div>
<?php } ?>