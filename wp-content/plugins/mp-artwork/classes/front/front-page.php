<?php

/*
  Class MP_Artwork_Front_Page
 * 
 * add actions for front page
 * 
 */

class MP_Artwork_Front_Page {

	private $prefix;
	public $postTypeSlug = 'works';

	public function __construct( $prefix ) {
		$this->prefix       = $prefix;
		$this->postTypeSlug = $this->posttype_name_sanitize_text( get_option( $this->get_prefix() . 'post_type_slug', 'works' ) );
		add_action( $this->get_prefix() . 'front_page', array( $this, 'get_html' ) );
	}

	/**
	 * Get prefix.
	 *
	 * @access public
	 * @return sting
	 */
	private function get_prefix() {
		return $this->prefix . '_';
	}

	public function get_header() {

		if ( ( get_theme_mod( 'custom_logo' ) && get_theme_mod( $this->get_prefix().'display_logo' , true)  )|| (get_header_textcolor() != 'blank') ) :
		?>
		<div class="site-header">
			<div class="container">
				<div class="site-logo">
					<?php if ( get_theme_mod( 'custom_logo' ) && get_theme_mod( $this->get_prefix().'display_logo' , true)  ) { ?>
						<div class="header-logo ">
							<?php the_custom_logo(); ?>
						</div>
					<?php }

					if (get_header_textcolor() != 'blank') : ?>

					<div class="site-description">
						<a class="home-link" href="<?php echo esc_url( home_url('/') ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<h1 class="site-title<?php if ( ! get_bloginfo( 'description' ) ) : ?> empty-tagline<?php endif; ?>"><?php bloginfo( 'name' ); ?></h1>
						<p class="site-tagline"><?php if ( get_bloginfo( 'description' ) ) : ?><?php bloginfo( 'description' ); ?><?php endif; ?></p>
						</a>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		endif;
	}

	public function get_default_front_content() {
		?>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-dark work-wrapper-cover work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg1.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'Woman In Gallery', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-light work-wrapper-cover work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg2.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'The Mona Lisa', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-light work-wrapper-cover work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg3.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'The Louvre Museum', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-light work-wrapper-cover work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg4.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'Lorem ipsum dolor', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-light work-wrapper-cover work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg5.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'Lorem ipsum dolor', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<article class="page-wrapper">
			<div class="work-wrapper work-wrapper-light work-wrapper-bg"
			     style="background-image: url(<?php echo MP_ARTWORK_PLUGIN_PATH; ?>images/works/bg6.jpg);">
				<div class="page-content">
					<div class="entry-header">
						<h2 class="entry-title h4">
							<a href="#" rel="bookmark"><?php _e( 'Lorem ipsum dolor', 'mp-artwork' ); ?></a>
						</h2>
					</div>
					<div class="entry entry-content">
						<p><?php _e( 'Aenean aliquam volutpat ipsum at cursus. Quisque porttitor lectus ac cursus imperdiet. Donec tempor ligula vel auctor venenatis. Vestibulum vehicula auctor dictum.', 'mp-artwork' ); ?></p>
						<a href="#" class="button"><?php _e( 'View', 'mp-artwork' ); ?></a>
					</div>
				</div>
			</div>
		</article>
		<div class="clearfix"></div>
		<?php
	}

	public function get_html() {
		$this->get_header();
		$paged = '';
		if ( is_front_page() ) {
			$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
		} else {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		}
		$this_post_slug = $this->postTypeSlug;
		$args           = array(
			'post_type' => $this_post_slug,
			'paged'     => $paged
		);
		$works          = new WP_Query( $args );
		?>

		<?php
		if ( $works->have_posts() ) {
			?>
			<div class="work-blog home-work-blog">
				<?php
				while ( $works->have_posts() ) {
					$works->the_post();
					mp_artwork_plugin_get_template_part( 'content-home', get_post_format() );
				}
				?>
			</div>
			<div class="clearfix"></div>

			<div class="hidden">
				<div class="older-works">
					<?php next_posts_link( '&laquo; Older Entries', $works->max_num_pages ) ?>
				</div>
				<?php previous_posts_link( 'Newer Entries &raquo;', $works->max_num_pages ) ?>
			</div>
			<?php wp_reset_postdata(); ?>
			<?php
		} else { ?>
			<div class="work-blog home-work-blog">
				<?php $this->get_default_front_content(); ?>
			</div>
			<?php
		}
	}

	private function posttype_name_sanitize_text( $txt ) {
		$txt = strip_tags( $txt, '' );
		$txt = preg_replace( "/[^a-zA-Z0-9-]+/", "", $txt );
		$txt = substr( strtolower( $txt ), 0, 19 );

		return wp_kses_post( force_balance_tags( $txt ) );
	}

}
