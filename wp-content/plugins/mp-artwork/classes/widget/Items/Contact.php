<?php
/**
 * Contact  widget class
 *
 */
require_once 'Default.php';

class MP_Artwork_Plugin_Widget_Contact extends MP_Artwork_Plugin_Widget_Default {

	public function __construct() {
		$this->setClassName( 'mp_artwork_contact_info' );
		$this->setName( __( 'Contact Info', 'mp-artwork' ) );
		$this->setDescription( __( 'Contact', 'mp-artwork' ) );
		$this->setIdSuffix( 'mp_artwork_contact' );
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title               = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$contact_info_text_1 = empty( $instance['contact_info_text_1'] ) ? '' : $instance['contact_info_text_1'];

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}
		?>
		<?php if ( ! ( empty( $contact_info_text_1 ) ) ) { ?>
			<div class="info-list-address">
				<ul class=" info-list">
					<li><?php echo $contact_info_text_1; ?></li>
				</ul>
			</div>
		<?php } ?>


		<?php
		echo $after_widget;
	}

	public
	function update(
		$new_instance, $old_instance
	) {
		$instance                        = $old_instance;
		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['contact_info_text_1'] = wp_kses( $new_instance['contact_info_text_1'],array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'b' => array(),
			'strong' => array(),
			'p' => array(),
			'ol' => array(),
			'ul' => array(),
			'li' => array(),
			'i' => array(),
		) );

		return $instance;
	}

	public
	function form(
		$instance
	) {
		$title               = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$contact_info_text_1 = isset( $instance['contact_info_text_1'] ) ? wp_kses( $instance['contact_info_text_1'],array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'b' => array(),
			'strong' => array(),
			'p' => array(),
			'ol' => array(),
			'ul' => array(),
			'li' => array(),
			'i' => array(),
		) ) : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mp-artwork' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
		<p><label
				for="<?php echo $this->get_field_id( 'contact_info_text_1' ); ?>"><?php _e( 'Contact Information:', 'mp-artwork' ); ?></label>
			<textarea class="widefat" rows="6" cols="20"
			          id="<?php echo $this->get_field_id( 'contact_info_text_1' ); ?>"
			          name="<?php echo $this->get_field_name( 'contact_info_text_1' ); ?>"><?php echo $contact_info_text_1; ?></textarea>
		</p>


		<?php
	}

}

add_action( 'widgets_init', create_function( '', 'return register_widget( "MP_Artwork_Plugin_Widget_Contact" );' ) );
