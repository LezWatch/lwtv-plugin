<?php
/**
 * Gravity Forms Approvals, Mini Version
 *
 * Forked from https://wordpress.org/plugins/gravityformsapprovals/
 * because it's no longer maintained, and is missing some important things.
 *
 * Version 1.0.0
 */

// Make sure Gravity Forms is active and already loaded.
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

// If the other plugin is active, we deactivate.
if ( defined( 'GF_APPROVALS_VERSION' ) && is_plugin_active( 'gravityformsapprovals/approvals.php' ) ) {
	deactivate_plugins( 'gravityformsapprovals/approvals.php' );
}


// The Add-On Framework is not loaded by default.
// Use the following function to load the appropriate files.
GFForms::include_feed_addon_framework();

class LWTV_GF_Approvals extends GFFeedAddOn {

	// The following class variables are used by the Framework.
	// They are defined in GFAddOn and should be overridden.

	// The version number is used for example during add-on upgrades.
	protected $_version = '1.0.0';

	// The Framework will display an appropriate message on the plugins page if necessary
	protected $_min_gravityforms_version = '2.4';

	// A short, lowercase, URL-safe unique identifier for the add-on.
	// This will be used for storing options, filters, actions, URLs and text-domain localization.
	protected $_slug = 'lwtv-gf-approvals';

	// Relative path to the plugin from the plugins folder.
	protected $_path = 'lwtv-plugin/plugins/gravity-forms/class-gf-approvals.php';

	// Full path the the plugin.
	protected $_full_path = __FILE__;

	// Title of the plugin to be used on the settings page, form settings and plugins page.
	protected $_title = 'Gravity Forms Approvals';

	// Short version of the plugin title to be used on menus and other places where a less verbose string is useful.
	protected $_short_title = 'Approvals';

	// ------------ Permissions -----------

	/**
	 * @var array Members plugin integration. List of capabilities to add to roles.
	 */
	protected $_capabilities = array(
		'lwtv-gf-approvals_form_settings',
		'lwtv-gf-approvals_uninstall',
	);

	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the form settings
	 */
	protected $_capabilities_form_settings = 'lwtv-gf-approvals_form_settings';

	/**
	 * @var string|array A string or an array of capabilities or roles that can uninstall the plugin
	 */
	protected $_capabilities_uninstall = 'lwtv-gf-approvals_uninstall';


	private static $_instance = null;

	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new LWTV_GF_Approvals();
		}

		return self::$_instance;
	}

	public function init_admin() {
		parent::init_admin();
		add_action( 'gform_entry_detail_sidebar_before', array( $this, 'entry_detail_approval_box' ), 10, 2 );

		add_filter( 'gform_notification_events', array( $this, 'add_notification_event' ) );
		add_filter( 'gform_entries_field_value', array( $this, 'filter_gform_entries_field_value' ), 10, 4 );

		if ( GFAPI::current_user_can_any( 'gravityforms_edit_entries' ) ) {
			add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup' ) );
		}
	}

	public function init_frontend() {
		parent::init_frontend();
		add_filter( 'gform_disable_registration', array( $this, 'disable_registration' ), 10, 4 );
	}

	// Registers the dashboard widget
	public function dashboard_setup() {
		wp_add_dashboard_widget( 'lwtv_gf_approvals_dashboard', 'Forms Pending Approval', array( $this, 'dashboard' ) );
	}

	/**
	 * Override the feed_settings_field() function and return the configuration for the Feed Settings.
	 * Updating is handled by the Framework.
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		$accounts = get_users();

		$account_choices = array(
			array(
				'label' => 'None',
				'value' => '',
			),
		);
		foreach ( $accounts as $account ) {
			$account_choices[] = array(
				'label' => $account->display_name,
				'value' => $account->ID,
			);
		}

		return array(
			array(
				'title'  => 'Approver',
				'fields' => array(
					array(
						'name'  => 'description',
						'label' => 'Description',
						'type'  => 'text',
					),
					array(
						'name'    => 'approver',
						'label'   => 'Approver',
						'type'    => 'select',
						'choices' => $account_choices,
					),
					array(
						'name'           => 'condition',
						'tooltip'        => 'Build the conditional logic that should be applied to this feed before it can be processed.',
						'label'          => 'Condition',
						'type'           => 'feed_condition',
						'checkbox_label' => 'Enable Condition for this approver',
						'instructions'   => 'Require approval from this user if',
					),
				),
			),
		);
	}

	/**
	 * Adds columns to the list of feeds.
	 *
	 * setting name => label
	 *
	 * @return array
	 */
	public function feed_list_columns() {
		return array(
			'description' => 'Description',
			'approver'    => 'Approver',
		);
	}

	public function get_column_value_approver( $item ) {
		if ( ! isset( $item['meta']['approver'] ) ) {
			return '';
		}

		$user = get_user_by( 'id', $item['meta']['approver'] );
		return $user ? $user->display_name : $item['meta']['approver'];
	}

	/**
	 * Fires after form submission only if conditions are met.
	 *
	 * @param $feed
	 * @param $entry
	 * @param $form
	 */
	public function process_feed( $feed, $entry, $form ) {
		$approver = absint( $feed['meta']['approver'] );

		gform_update_meta( $entry['id'], 'approval_status_' . $approver, 'pending' );
	}

	/**
	 * Entry meta data is custom data that's stored and retrieved along with the entry object.
	 * For example, entry meta data may contain the results of a calculation made at the time of the entry submission.
	 *
	 * To add entry meta override the get_entry_meta() function and return an associative array with the following keys:
	 *
	 * label
	 * - (string) The label for the entry meta
	 *
	 * is_numeric
	 * - (boolean) Used for sorting
	 *
	 * is_default_column
	 * - (boolean) Default columns appear in the entry list by default. Otherwise the user has to edit the
	 *             columns and select the entry meta from the list.
	 *
	 * update_entry_meta_callback
	 * - (string | array) The function that should be called when updating this entry meta value
	 *
	 * filter
	 * - (array) An array containing the configuration for the filter used on the results pages,
	 *           the entry list search and export entries page.
	 *           The array should contain one element: operators. e.g. 'operators' => array('is', 'isnot', '>', '<')
	 *
	 *
	 * @param array $entry_meta An array of entry meta already registered with the gform_entry_meta filter.
	 * @param int   $form_id    The Form ID
	 *
	 * @return array The filtered entry meta array.
	 */
	public function get_entry_meta( $entry_meta, $form_id ) {
		$feeds        = $this->get_feeds( $form_id );
		$has_approver = false;
		foreach ( $feeds as $feed ) {
			if ( ! $feed['is_active'] ) {
				continue;
			}

			// User ID of approver
			$approver  = absint( $feed['meta']['approver'] );
			$user_info = get_user_by( 'id', $approver );

			$display_name = $user_info ? $user_info->display_name : $approver;

			$entry_meta[ 'approval_status_' . $approver ] = array(
				'label'             => 'Approval Status: ' . $display_name,
				'is_numeric'        => false,
				'is_default_column' => false, // this column will not be displayed by default on the entry list
				'filter'            => array(
					'operators' => array( 'is', 'isnot' ),
					'choices'   => array(
						array(
							'value' => 'pending',
							'text'  => 'Pending',
						),
						array(
							'value' => 'approved',
							'text'  => 'Approved',
						),
						array(
							'value' => 'rejected',
							'text'  => 'Rejected',
						),
					),
				),
			);

			// Set has approver.
			$has_approver = true;

		}
		if ( $has_approver ) {
			$entry_meta['approval_status'] = array(
				'label'                      => 'Approval Status',
				'is_numeric'                 => false,
				'update_entry_meta_callback' => array( $this, 'update_approval_status' ),
				'is_default_column'          => true, // this column will be displayed by default on the entry list
				'filter'                     => array(
					'operators' => array( 'is', 'isnot' ),
					'choices'   => array(
						array(
							'value' => 'pending',
							'text'  => 'Pending',
						),
						array(
							'value' => 'approved',
							'text'  => 'Approved',
						),
						array(
							'value' => 'rejected',
							'text'  => 'Rejected',
						),
					),
				),
			);
		}

		return $entry_meta;
	}

	/**
	 * The target of update_entry_meta_callback.
	 *
	 * @param string $key   The entry meta key
	 * @param array  $entry The Entry Object
	 * @param array  $form  The Form Object
	 *
	 * @return string
	 */
	public function update_approval_status( $key, $entry, $form ) {
		// Default is pending
		$return = 'pending';

		// Auto Reject Spam
		if ( isset( $entry['status'] ) && 'spam' === strtolower( $entry['status'] ) ) {
			$return = 'rejected';
		}

		return $return;
	}

	/**
	 * Builds the Entry Approval box.
	 */
	public function entry_detail_approval_box( $form, $entry ) {
		global $current_user;

		if ( ! isset( $entry['approval_status'] ) ) {
			return;
		}

		if ( isset( $_POST['gf_approvals_status'] ) && check_admin_referer( 'gf_approvals' ) ) {
			$new_status = sanitize_text_field( $_POST['gf_approvals_status'] );

			// Update Meta
			gform_update_meta( $entry['id'], 'approval_status_' . $current_user->ID, $new_status );
			gform_update_meta( $entry['id'], 'approval_date_' . $current_user->ID, time() );
			gform_update_meta( $entry['id'], 'approval_date', time() );

			$entry[ 'approval_status_' . $current_user->ID ] = $new_status;

			// Base Info
			$entry_approved = false;
			$entry_rejected = false;
			$approver_array = array();

			foreach ( $this->get_feeds( $form['id'] ) as $feed ) {
				if ( $feed['is_active'] && $this->is_feed_condition_met( $feed, $form, $entry ) ) {
					// If anyone who is an approval approves or rejects, allow it.
					// Source: https://wordpress.org/support/topic/multiple-approvers-fix/
					$all_approvers = ( ! is_array( $feed['meta']['approver'] ) ) ? array( $feed['meta']['approver'] ) : $feed['meta']['approver'];
					foreach ( $all_approvers as $approver ) {
						if ( ! empty( $entry[ 'approval_status_' . $approver ] ) ) {
							if ( 'approved' === $entry[ 'approval_status_' . $approver ] ) {
								$approver_array[ $approver ]['approved'] = true;
							} elseif ( 'approved' !== $entry[ 'approval_status_' . $approver ] ) {
								$approver_array[ $approver ]['approved'] = false;
							} elseif ( 'rejected' === $new_status ) {
								$approver_array[ $approver ]['rejected'] = true;
							}

							// Build array
							$approver_array[ $approver ] = array(
								'approved' => $entry_approved,
								'rejected' => $entry_rejected,
							);
						}
					}
				}
			}

			foreach ( $approver_array as $approver ) {
				if ( true === $approver['approved'] ) {
					$entry_approved = true;
				}

				if ( true === $approver['rejected'] ) {
					$entry_rejected = true;
				}
			}

			if ( $entry_rejected ) {
				gform_update_meta( $entry['id'], 'approval_status', 'rejected' );
				$entry['approval_status'] = 'rejected';
				do_action( 'gform_approvals_entry_rejected', $entry, $form );
			} elseif ( $entry_approved ) {
				gform_update_meta( $entry['id'], 'approval_status', 'approved' );
				$entry['approval_status'] = 'approved';
				do_action( 'gform_approvals_entry_approved', $entry, $form );
			}

			$notifications_to_send = GFCommon::get_notifications_to_send( 'form_approval', $form, $entry );
			foreach ( $notifications_to_send as $notification ) {
				GFCommon::send_notification( $notification, $form, $entry );
			}
		}

		$status       = 'Pending';
		$approve_icon = '<i class="fa fa-check" style="color:green"></i>';
		$reject_icon  = '<i class="fa fa-times" style="color:red"></i>';
		if ( 'approved' === $entry['approval_status'] ) {
			$status = 'Approved ' . $approve_icon;
		} elseif ( 'rejected' === $entry['approval_status'] ) {
			$status = 'Rejected ' . $reject_icon;
		}

		// To Do: Add in Post Box for warning?
		?>
		<div class="postbox">
			<h3><?php echo 'Approval Status: ' . wp_kses_post( $status ); ?></h3>

			<div style="padding:10px;">
				<ul>
					<?php
					$has_been_reviewed        = false;
					$current_user_is_approver = false;
					foreach ( $this->get_feeds( $form['id'] ) as $feed ) {
						if ( $feed['is_active'] ) {
							$approver = $feed['meta']['approver'];
							if ( $feed['is_active'] && $this->is_feed_condition_met( $feed, $form, $entry ) ) {
								$user_info = get_user_by( 'id', $approver );
								$status    = $entry[ 'approval_status_' . $approver ];
								if ( false === $status ) {
									$status = 'pending';
								} elseif ( 'pending' !== $status ) {
									$has_been_reviewed = true;
								}
								if ( false === $status || 'pending' === $status ) {
									if ( $current_user->ID === $approver ) {
										$current_user_is_approver = true;
									}
								}

								if ( $has_been_reviewed && 'pending' === $status ) {
									$status = 'N/A';
								} elseif ( $has_been_reviewed && 'pending' !== $status ) {
									// For some reason this isn't always being set???
									gform_update_meta( $entry['id'], 'approval_status', $status );
									echo '<li>' . esc_html( ucfirst( $status ) ) . ' by ' . esc_html( $user_info->display_name ) . '</li>';
								}
							}
						}
					}

					// We only need one person to approve.
					if ( $has_been_reviewed ) {
						add_action( 'gform_entrydetail_update_button', array( $this, 'remove_entrydetail_update_button' ), 10 );
					}
					?>
				</ul>
				<div>
					<?php
					// If we have not yet been approved, we'll want this:
					if ( ! $has_been_reviewed ) {
						?>
							<?php wp_nonce_field( 'gf_approvals' ); ?>
							<button name="gf_approvals_status" value="approved" type="submit" class="button">
								<?php echo wp_kses_post( $approve_icon ); ?> Approve
							</button>
							<button name="gf_approvals_status" value="rejected" type="submit" class="button">
								<?php echo wp_kses_post( $reject_icon ); ?> Reject
							</button>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Displays the Dashboard UI
	 */
	public static function dashboard() {

		$search_criteria['status']          = 'active'; // No Spam :)
		$search_criteria['field_filters'][] = array(
			'key'   => 'approval_status',
			'value' => 'pending',
		);

		$entries = GFAPI::get_entries( 0, $search_criteria );

		if ( ! empty( $entries ) && count( $entries ) > 0 ) {
			?>
			<table class="widefat" cellspacing="0" style="border:0px;">
				<thead>
				<tr>
					<td><i>Form</i></td>
					<td><i>User/IP</i></td>
					<td><i>Submission Date</i></td>
				</tr>
				</thead>

				<tbody class="list:user user-list">
				<?php

				$get_ip = ( new LWTV_Gravity_Forms() )->check_ip_location( $entry['ip'] );
				foreach ( $entries as $entry ) {
					$form      = GFAPI::get_form( $entry['form_id'] );
					$user      = get_user_by( 'id', (int) $entry['created_by'] );
					$url_entry = sprintf( 'admin.php?page=gf_entries&view=entry&id=%d&lid=%d', $entry['form_id'], $entry['id'] );
					$url_entry = sanitize_url( admin_url( $url_entry ) );
					?>
					<tr>
						<td>
							<?php
							echo '<a href="' . esc_url( $url_entry ) . '">' . esc_html( $form['title'] ) . '</a>';
							?>
						</td>
						<td>
							<?php
							if ( 0 !== (int) $entry['created_by'] ) {
								echo esc_html( $user->display_name ) . ' ';
							} else {
								echo 'Anonymous ';
							}
							echo '(' . esc_html( $get_ip['full'] ) . ')';
							?>
						</td>
						<td>
							<?php
							echo '<a href="' . esc_url( $url_entry ) . '">' . esc_html( $entry['date_created'] ) . '</a>';
							?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>

			<?php
		} else {
			?>
			<div>All forms currently approved.</div>
			<?php
		}
	}

	public function remove_entrydetail_update_button( $button ) {
		return 'This entry has been reviewed and can no longer be edited';
	}

	public function add_notification_event( $events ) {
		$events['form_approval'] = 'Form is approved or rejected';
		return $events;
	}

	public function disable_registration( $is_disabled, $form, $entry, $fulfilled ) {
		$feeds = $this->get_feeds( $form['id'] );
		if ( empty( $feeds ) ) {
			return false;
		}

		//check status to decide if registration should be stopped
		if ( isset( $entry['approval_status'] ) && 'approved' === $entry['approval_status'] ) {
			//disable registration
			return false;
		} else {
			return true;
		}
	}

	public function filter_gform_entries_field_value( $value, $form_id, $field_id, $entry ) {
		$translated_value = $value;
		if ( 'approval_status' === $field_id ) {
			$translated_value = ucfirst( $value );
		}
		return $translated_value;
	}
}

GFAddOn::register( 'LWTV_GF_Approvals' );
