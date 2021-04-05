<?php
/**
 * Name: Widgets for Affiliates
 */

class LWTV_Affilliate_Ads_Widgets extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 */
	protected $defaults;

	/**
	 * Constructor.
	 *
	 * Set the default widget options and create widget.
	 */
	public function __construct() {

		$this->defaults = array(
			'title'  => 'Affiliate Ads',
			'type'   => 'Random',
			'format' => 'wide',
		);
		$widget_ops     = array(
			'classname'   => 'lwtv-affiliates adwidget',
			'description' => 'Ad networks you can use on LezWatch.TV',
		);
		$control_ops    = array(
			'id_base' => 'lwtv-affiliates',
		);

		parent::__construct( 'lwtv-affiliates', 'LWTV Affiliate Ads', $widget_ops, $control_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput
		echo '<!-- BEGIN Affiliate Ads --><center>' . ( new LWTV_Affilliates() )->random( $instance['type'], $instance['format'] ) . '</center><!-- END Affiliate Ads -->';

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Update a particular instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['title'] = wp_strip_all_tags( $new_instance['title'] );

		// Types of ads
		// phpcs:ignore WordPress.PHP.StrictInArray
		$new_instance['type'] = ( in_array( $new_instance['type'], LWTV_Affilliates::$valid_types ) ) ? sanitize_html_class( $new_instance['type'] ) : 'random';

		// Ad format
		// phpcs:ignore WordPress.PHP.StrictInArray
		$new_instance['format'] = ( in_array( $new_instance['format'], LWTV_Affilliates::$valid_formats ) ) ? sanitize_html_class( $new_instance['format'] ) : 'wide';

		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title: </label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">Type of Ad (Source): </label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" class="widefat">
				<?php
				foreach ( LWTV_Affilliates::$valid_types as $type ) {
					echo '<option ' . selected( $instance['type'], $type ) . 'value="' . esc_attr( $type ) . '">' . esc_html( ucfirst( $type ) ) . '</option>';
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>">Format of Ad (Style): </label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>" class="widefat">
				<?php
				foreach ( LWTV_Affilliates::$valid_formats as $format ) {
					echo '<option ' . selected( $instance['format'], $format ) . 'value="' . esc_attr( $format ) . '">' . esc_html( ucfirst( $format ) ) . '</option>';
				}
				?>
			</select>
		</p>

		<?php
	}
}


// Register LWTV_Character widget
function register_lwtv_affiliate_widgets() {
	register_widget( 'LWTV_Affilliate_Ads_Widgets' );
}
add_action( 'widgets_init', 'register_lwtv_affiliate_widgets' );
