<?php
/**
 * Socials widget class
 *
 */
require_once 'Default.php';

class MP_Artwork_Plugin_Widget_Socials extends MP_Artwork_Plugin_Widget_Default {

	public function __construct() {
		$this->setClassName( 'mp_artwork_widget_socials' );
		$this->setName( __( 'Socials', 'mp-artwork' ) );
		$this->setDescription( __( 'Socials', 'mp-artwork' ) );
		$this->setIdSuffix( 'mp_artwork_socials' );
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title            = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$facebook_link    = empty( $instance['facebook_link'] ) ? '' : esc_url( $instance['facebook_link'] );
		$twitter_link     = empty( $instance['twitter_link'] ) ? '' : esc_url( $instance['twitter_link'] );
		$linkedin_link    = empty( $instance['linkedin_link'] ) ? '' : esc_url( $instance['linkedin_link'] );
		$google_plus_link = empty( $instance['google_plus_link'] ) ? '' : esc_url( $instance['google_plus_link'] );
		$instagram_link   = empty( $instance['instagram_link'] ) ? '' : esc_url( $instance['instagram_link'] );
		$pinterest_link   = empty( $instance['pinterest_link'] ) ? '' : esc_url( $instance['pinterest_link'] );
		$tumblr_link      = empty( $instance['tumblr_link'] ) ? '' : esc_url( $instance['tumblr_link'] );
		$youtube_link     = empty( $instance['youtube_link'] ) ? '' : esc_url( $instance['youtube_link'] );
		$rss_link         = empty( $instance['rss_link'] ) ? '' : esc_url( $instance['rss_link'] );
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}
		?>
		<?php if ( !( empty( $facebook_link )) || !(empty( $twitter_link )) || !(empty( $linkedin_link )) || !(empty( $google_plus_link )) || !(empty( $instagram_link )) || !(empty( $pinterest_link )) || !(empty( $tumblr_link )) || !(empty( $youtube_link )) || !(empty( $rss_link ) ) ): ?>
			<div class="social-profile">
				<?php if ( ! empty( $facebook_link ) ): ?>
					<a href="<?php echo $facebook_link; ?>" class="button-facebook"
					   title="<?php _e( 'Facebook', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-facebook"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $twitter_link ) ): ?>
					<a href="<?php echo $twitter_link; ?>" class="button-twitter"
					   title="<?php _e( 'Twitter', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-twitter"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $linkedin_link ) ): ?>
					<a href="<?php echo $linkedin_link; ?>" class="button-linkedin"
					   title="<?php _e( 'LinkedIn', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-linkedin"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $google_plus_link ) ): ?>
					<a href="<?php echo $google_plus_link; ?>" class="button-google"
					   title="<?php _e( 'Google +', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-google-plus"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $instagram_link ) ): ?>
					<a href="<?php echo $instagram_link; ?>" class="button-instagram"
					   title="<?php _e( 'Instagram', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-instagram"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $pinterest_link ) ): ?>
					<a href="<?php echo $pinterest_link; ?>" class="button-pinterest"
					   title="<?php _e( 'Pinterest', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-pinterest"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $tumblr_link ) ): ?>
					<a href="<?php echo $tumblr_link; ?>" class="button-tumblr"
					   title="<?php _e( 'Tumblr', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-tumblr"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $youtube_link ) ): ?>
					<a href="<?php echo $youtube_link; ?>" class="button-youtube"
					   title="<?php _e( 'Youtube', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-youtube"></i></a>
				<?php endif; ?>
				<?php if ( ! empty( $rss_link ) ): ?>
					<a href="<?php echo $rss_link; ?>" class="button-rss"
					   title="<?php _e( 'Rss', 'mp-artwork' ); ?>"
					   target="_blank"><i class="fa fa-rss"></i></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['facebook_link']    = esc_url( $new_instance['facebook_link'] );
		$instance['twitter_link']     = esc_url( $new_instance['twitter_link'] );
		$instance['linkedin_link']    = esc_url( $new_instance['linkedin_link'] );
		$instance['google_plus_link'] = esc_url( $new_instance['google_plus_link'] );
		$instance['instagram_link']   = esc_url( $new_instance['instagram_link'] );
		$instance['pinterest_link']   = esc_url( $new_instance['pinterest_link'] );
		$instance['tumblr_link']      = esc_url( $new_instance['tumblr_link'] );
		$instance['youtube_link']     = esc_url( $new_instance['youtube_link'] );
		$instance['rss_link']         = esc_url( $new_instance['rss_link'] );

		return $instance;
	}

	public function form( $instance ) {
		$title            = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$facebook_link    = isset( $instance['facebook_link'] ) ? esc_url( $instance['facebook_link'] ) : '';
		$twitter_link     = isset( $instance['twitter_link'] ) ? esc_url( $instance['twitter_link'] ) : '';
		$linkedin_link    = isset( $instance['linkedin_link'] ) ? esc_url( $instance['linkedin_link'] ) : '';
		$google_plus_link = isset( $instance['google_plus_link'] ) ? esc_url( $instance['google_plus_link'] ) : '';
		$instagram_link   = isset( $instance['instagram_link'] ) ? esc_url( $instance['instagram_link'] ) : '';
		$pinterest_link   = isset( $instance['pinterest_link'] ) ? esc_url( $instance['pinterest_link'] ) : '';
		$tumblr_link      = isset( $instance['tumblr_link'] ) ? esc_url( $instance['tumblr_link'] ) : '';
		$youtube_link     = isset( $instance['youtube_link'] ) ? esc_url( $instance['youtube_link'] ) : '';
		$rss_link         = isset( $instance['rss_link'] ) ? esc_url( $instance['rss_link'] ) : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'facebook_link' ); ?>"><?php _e( 'Facebook link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'facebook_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'facebook_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $facebook_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'twitter_link' ); ?>"><?php _e( 'Twitter link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'twitter_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'twitter_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $twitter_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'linkedin_link' ); ?>"><?php _e( 'Linkedin link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'linkedin_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'linkedin_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $linkedin_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'google_plus_link' ); ?>"><?php _e( 'Google+ link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'google_plus_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'google_plus_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $google_plus_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'instagram_link' ); ?>"><?php _e( 'Instagram link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'instagram_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'instagram_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $instagram_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'pinterest_link' ); ?>"><?php _e( 'Pinterest link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'pinterest_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'pinterest_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $pinterest_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'tumblr_link' ); ?>"><?php _e( 'Tumblr link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tumblr_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'tumblr_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $tumblr_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'youtube_link' ); ?>"><?php _e( 'Youtube link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'youtube_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'youtube_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $youtube_link ); ?>"/></p>
		<p><label
				for="<?php echo $this->get_field_id( 'rss_link' ); ?>"><?php _e( 'Rss link:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'rss_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'rss_link' ); ?>" type="text"
			       value="<?php echo esc_attr( $rss_link ); ?>"/></p>


		<?php
	}

}

add_action( 'widgets_init', create_function( '', 'return register_widget( "MP_Artwork_Plugin_Widget_Socials" );' ) );
