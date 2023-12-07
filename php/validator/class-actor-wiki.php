<?php
/*
 * Validation: Actor Wiki For LezWatch.TV
 *
 * CURRENTLY NOT USED
 */

namespace LWTV\Validator;

class Actor_Wiki {
	/**
	 * Do some juggling to see how actors compare to Wikidata
	 *
	 * @var [type]
	 */
	public static function make() {
		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = lwtv_plugin()->check_actors_wikidata();

		/*
			Instead of looping through all, let's do something else.

			1. Select actor from drop down
			2. Check THAT actor.
			3. Output results

			$json_it = will be the actor ID

		 */

		?>
		<div class="lwtv-tools-container">

			<p>Pick an actor you want to check:</p>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
				<input type="hidden" name="action" value="lwtv_data_check_wikidata_actors">
				<select id="actor_id" name="actor">
					<?php
					foreach ( $items as $id => $name ) {
						echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
				<?php wp_nonce_field( 'lwtv_data_check_wikidata_actors', 'lwtv_data_check_wikidata_actors_nonce', false ); ?>
				<input type="hidden" name="_wp_http_referer" value="<?php echo esc_url_raw( $redirect ); ?>">
				<?php submit_button( 'Check' ); ?>
			</form>

			<!-- Form needs to output HERE -->
		</div>

		<?php
	}
}
