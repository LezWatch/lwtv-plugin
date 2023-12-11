<?php
/**
 * User Profiles
 */

namespace LWTV\Features;

class User_Profiles {

	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'extra_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'extra_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_extra_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_profile_fields' ) );
		add_filter( 'user_contactmethods', array( $this, 'user_contactmethods' ) );

		// Rest API
		register_meta(
			'user',
			'jobrole',
			array(
				'type'         => 'string',
				'show_in_rest' => true, // this is the key part
			)
		);
		register_meta(
			'user',
			'twitter',
			array(
				'type'         => 'string',
				'show_in_rest' => true, // this is the key part
			)
		);
		add_action( 'rest_api_init', array( $this, 'rest_api_add_user_field' ), 10, 2 );
	}

	public function rest_api_add_user_field() {
		register_rest_field(
			'user',
			'lez_user_favourite_shows',
			array(
				'get_callback' => function ( $user, $field_name, $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
					return get_user_meta( $user['id'], $field_name, true );
				},
				'schema'       => null,
			)
		);
	}

	public function user_contactmethods() {
		// Add moar contact methods and reorder:
		$methods['twitter']   = 'Twitter username (without @ so "mytwitter")';
		$methods['facebook']  = 'Facebook profile URL';
		$methods['instagram'] = 'Instagram profile URL';
		$methods['tumblr']    = 'Tumblr profile URL (ex. https://my-site.tumblr.com )';
		$methods['tiktok']    = 'TikTok URL (ex. https://tiktok.com/@myname)';
		$methods['mastodon']  = 'Mastodon URL (ex. https://mastodon.site/@myname )';
		$methods['bluesky']   = 'BlueSky (ex. https://bsky.app/profile/myname.bsky.social )';

		return $methods;
	}

	public function extra_profile_fields( $user ) {
		?>
		<style>
		.yoast-settings {
			display: none;
		}
		h2#yoast-seo-schema {
			visibility: hidden;
		}
		h2#yoast-seo-schema:before {
			visibility: visible;
			content: 'LezWatch.TV Customizations';
		}
		</style>

		<table class="form-table">

			<?php
			if ( current_user_can( 'update_core' ) ) {
				?>
				<tr>
					<th><label for="jobrole">Job Role</label></th>
					<td>
						<input type="text" name="jobrole" id="jobrole" value="<?php echo esc_attr( get_the_author_meta( 'jobrole', $user->ID ) ); ?>" class="regular-text" /><br />
						<span class="description">Job Role (i.e. Editor etc)</span>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<th><label for="pronouns">Pronouns</label></th>
				<td>
					<input type="text" name="gender" id="gender" value="<?php echo esc_attr( get_the_author_meta( 'pronouns', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description">Preferred Pronouns</span>
				</td>
			</tr>
			<tr>
				<th><label for="gender">Gender</label></th>
				<td>
					<input type="text" name="gender" id="gender" value="<?php echo esc_attr( get_the_author_meta( 'gender', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description">Gender Identity</span>
				</td>
			</tr>
			<tr>
				<th><label for="sexuality">Sexuality</label></th>
				<td>
					<input type="text" name="sexuality" id="sexuality" value="<?php echo esc_attr( get_the_author_meta( 'sexuality', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description">Sexuality</span>
				</td>
			</tr>

		</table>
		<?php
	}

	public function save_extra_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		// phpcs:disable
		update_user_meta( $user_id, 'jobrole', sanitize_text_field( $_POST['jobrole'] ) );
		update_user_meta( $user_id, 'gender', sanitize_text_field( $_POST['gender'] ) );
		update_user_meta( $user_id, 'sexuality', sanitize_text_field( $_POST['sexuality'] ) );
		update_user_meta( $user_id, 'pronouns', sanitize_text_field( $_POST['pronouns'] ) );
		// phpcs:enable
	}
}
