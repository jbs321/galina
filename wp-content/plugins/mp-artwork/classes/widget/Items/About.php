<?php
/**
 * About widget class
 *
 */
require_once 'Default.php';

class MP_Artwork_Plugin_Widget_About extends MP_Artwork_Plugin_Widget_Default {

	public function __construct() {
		$this->setClassName( 'mp_artwork_widget_about' );
		$this->setName( __( 'About Us', 'mp-artwork' ) );
		$this->setDescription( __( 'About us', 'mp-artwork' ) );
		$this->setIdSuffix( 'mp_artwork_about' );
		parent::__construct();
	}

	public function img_url( $instance ) {
		global $wpdb;
		$image_src = ( ! empty( $instance['image_uri'] ) ) ? esc_url( $instance['image_uri'] ) : '';
		if ( ! empty( $image_src ) ):
			$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
			$id    = $wpdb->get_var( $query );
			if ( is_null( $id ) ):
				return $image_src;
			endif;
			$image_uri = wp_get_attachment_image_src( $id, array( 200, 200 ) );

			return $image_uri[0];
		endif;

		return '';
	}

	public function get_upload_image( $instance ) {
		echo '<img class="custom_media_image" src="';
		if ( ! empty( $instance['image_uri'] ) ) :
			echo $instance['image_uri'];
		endif;
		echo '" style="margin:0;padding:0;max-width:100%;';
		if ( ! empty( $instance['image_uri'] ) ) :
			echo 'display:block;';
		else:
			echo 'display:none;';
		endif;
		echo '" />';
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title       = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$logo        = true;
		$description = true;
		$site_title  = true;
		$image_uri   = $this->img_url( $instance );
		if ( count( $instance ) && isset( $instance['description'] ) ) {
			$description = $instance['description'] === null ? true : $instance['description'];
		}
		if ( count( $instance ) && isset( $instance['site_title'] ) ) {
			$site_title = $instance['site_title'] === null ? true : $instance['site_title'];
		}
		if ( count( $instance ) && isset( $instance['logo'] ) ) {
			$logo = $instance['logo'] === null ? true : $instance['logo'];
		}
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}
		?>
		<?php if ( $logo || $description || $site_title ): ?>
			<div class="site-logo">
				<a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"
				   title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">

					<?php if ( $logo && ! empty( $image_uri ) ) { ?>
						<div class="header-logo "><img
								src="<?php echo $image_uri; ?>"
								alt="<?php bloginfo( 'name' ); ?>">
						</div>
					<?php } ?>

					<?php if (  $description || $site_title ) : ?>
						<div class="site-description">
							<?php if ( $site_title ) { ?>
								<h2 class="site-title <?php if ( ! get_bloginfo( 'description' ) ) : ?>empty-tagline<?php endif; ?>"><?php bloginfo( 'name' ); ?></h2>
							<?php } ?>
							<?php if ( get_bloginfo( 'description' ) && $description ) { ?>
								<p class="site-tagline"><?php bloginfo( 'description' ); ?></p>
							<?php } ?>
						</div>
					<?php endif; ?>
				</a>
			</div>
		<?php endif; ?>
		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['logo']        = isset( $new_instance['logo'] );
		$instance['site_title']  = isset( $new_instance['site_title'] );
		$instance['description'] = isset( $new_instance['description'] );
		$instance['image_uri']   = esc_url( $new_instance['image_uri'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title    = strip_tags( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/></p>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'image_uri' ) ); ?>"><?php _e( 'Logo:', 'mp-artwork' ); ?></label><br/>
			<?php $this->get_upload_image( $instance ); ?>
			<input type="text" class="widefat custom_media_url"
			       name="<?php echo esc_attr( $this->get_field_name( 'image_uri' ) ); ?>"
			       id="<?php echo esc_attr( $this->get_field_id( 'image_uri' ) ); ?>" value="<?php
			if ( ! empty( $instance['image_uri'] ) ): echo esc_attr( $instance['image_uri'] );
			endif;
			?>" style="margin-top:5px;">
			<input type="button" class="button button-primary theme_media_button" id="custom_media_button"
			       name="<?php echo esc_attr( $this->get_field_name( 'image_uri' ) ); ?>"
			       value="<?php _e( 'Upload Image', 'mp-emmet' ); ?>"
			       style="margin-top:5px;"/>
		</p>
		<p><input id="<?php echo $this->get_field_id( 'logo' ); ?>"
		          name="<?php echo $this->get_field_name( 'logo' ); ?>"
		          type="checkbox" <?php checked( isset( $instance['logo'] ) ? $instance['logo'] : true ); ?> />&nbsp;<label
				for="<?php echo $this->get_field_id( 'logo' ); ?>"><?php _e( 'Show logo', 'mp-artwork' ); ?></label></p>
		<p><input id="<?php echo $this->get_field_id( 'site_title' ); ?>"
		          name="<?php echo $this->get_field_name( 'site_title' ); ?>"
		          type="checkbox" <?php checked( isset( $instance['site_title'] ) ? $instance['site_title'] : true ); ?> />&nbsp;<label
				for="<?php echo $this->get_field_id( 'site_title' ); ?>"><?php _e( 'Show site title', 'mp-artwork' ); ?></label>
		</p>
		<p><input id="<?php echo $this->get_field_id( 'description' ); ?>"
		          name="<?php echo $this->get_field_name( 'description' ); ?>"
		          type="checkbox" <?php checked( isset( $instance['description'] ) ? $instance['description'] : true ); ?> />&nbsp;<label
				for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Show description', 'mp-artwork' ); ?></label>
		</p>
		<?php
	}

}

add_action( 'widgets_init', create_function( '', 'return register_widget( "MP_Artwork_Plugin_Widget_About" );' ) );
