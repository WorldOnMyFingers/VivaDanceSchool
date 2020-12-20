<?php 
/**
 * Backend booking dialogs
 * @version 1.7.10
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<div id='bookacti-change-booking-state-dialog' class='bookacti-backend-dialog bookacti-bookings-dialog' style='display:none;' title='<?php echo esc_html__( 'Change booking state', 'booking-activities' ); ?>'>
	<form id='bookacti-change-booking-state-form'>
		<?php
		// Display nonce field
		wp_nonce_field( 'bookacti_change_booking_state', 'nonce_change_booking_state', false );
		?>
		
		<p class='bookacti-dialog-intro' ><?php esc_html_e( 'Pick the desired booking state:', 'booking-activities' ); ?></p>
		<div>
			<label for='bookacti-select-booking-state' ><?php esc_html_e( 'Booking state', 'booking-activities' ); ?></label>
			<select name='select-booking-state' id='bookacti-select-booking-state' >
				<?php
				$booking_state_labels = bookacti_get_booking_state_labels();
				$allowed_booking_states = apply_filters( 'bookacti_booking_states_you_can_manually_change', array( 'delivered', 'booked', 'pending', 'cancelled', 'refund_requested', 'refunded' ) );
				foreach( $allowed_booking_states as $state_key ) {
					$state_label = ! empty( $booking_state_labels[ $state_key ][ 'label' ] ) ? $booking_state_labels[ $state_key ][ 'label' ] : $state_key;
					echo '<option value="' . esc_attr( $state_key ) . '" >' . $state_label . '</option>';
				}
				?>
			</select>
		</div>
		<div>
			<label for='bookacti-send-notifications-on-state-change' ><?php esc_html_e( 'Send notifications', 'booking-activities' ); ?></label>
			<?php 
				$args = array(
					'type'	=> 'checkbox',
					'name'	=> 'send-notifications-on-state-change',
					'id'	=> 'bookacti-send-notifications-on-state-change',
					'value'	=> 0,
					'tip'	=> __( 'Whether to notify the customer of the booking status change.', 'booking-activities' )
				);
				bookacti_display_field( $args );
			?>
		</div>
		<div>
			<label for='bookacti-select-payment-status' ><?php esc_html_e( 'Payment status', 'booking-activities' ); ?></label>
			<select name='select-payment-status' id='bookacti-select-payment-status' >
				<?php
				$payment_status = bookacti_get_payment_status_labels();
				foreach( $payment_status as $payment_status_id => $payment_status_data ) {
					echo '<option value="' . esc_attr( $payment_status_id ) . '" >' . esc_html( $payment_status_data[ 'label' ] ) . '</option>';
				}
				?>
			</select>
		</div>
	</form>
</div>


<div id='bookacti-change-booking-quantity-dialog' class='bookacti-backend-dialog bookacti-bookings-dialog' style='display:none;' title='<?php echo esc_html__( 'Change booking quantity', 'booking-activities' ); ?>'>
	<form id='bookacti-change-booking-quantity-form'>
		<?php
		// Display nonce field
		wp_nonce_field( 'bookacti_change_booking_quantity', 'nonce_change_booking_quantity', false );
		?>
		<p class='bookacti-dialog-intro' ><?php esc_html_e( 'Input the desired booking quantity:', 'booking-activities' ); ?></p>
		<?php
			$booking_qty_fields = apply_filters( 'bookacti_change_booking_quantity_dialog_fields', array(
				'quantity' => array(
					'type'	=> 'number',
					'name'	=> 'new_quantity',
					'title'	=> esc_html__( 'Quantity', 'booking-activities' ),
					'id'	=> 'bookacti-new-quantity',
					'value'	=> 1,
					'tip'	=> esc_html__( 'New total quantity. In case of booking groups, the quantity of all the bookings of the group will be updated.', 'booking-activities' )
				)
			));
			bookacti_display_fields( $booking_qty_fields );
		?>
		<p class='bookacti-irreversible-action'>
			<span class='dashicons dashicons-warning'></span>
			<span><?php esc_html_e( 'The new quantity will be enforced. No checks and no further actions will be performed.', 'booking-activities' ); ?></span>
		</p>
	</form>
</div>


<div id='bookacti-delete-booking-dialog' class='bookacti-backend-dialog bookacti-bookings-dialog' style='display:none;' title='<?php echo esc_html__( 'Delete a booking', 'booking-activities' ); ?>'>
	<form id='bookacti-delete-booking-dialog-content'>
		<input type='hidden' name='action' value='bookactiDeleteBooking'/>
		<input type='hidden' name='booking_id' value='0'/>
		<input type='hidden' name='booking_type' value=''/>
		<?php wp_nonce_field( 'bookacti_delete_booking', 'nonce_delete_booking' ); ?>
		<p class='bookacti-dialog-intro bookacti-delete-single-booking-description' >
			<?php esc_html_e( 'Are you sure to delete this booking permanently?', 'booking-activities' ); ?>
		</p>
		<p class='bookacti-irreversible-action'>
			<span class='dashicons dashicons-warning'></span>
			<span><?php esc_html_e( 'This action cannot be undone.', 'booking-activities' ); ?></span>
		</p>
		<p class='bookacti-dialog-intro bookacti-delete-booking-group-description' style='display:none;'>
			<?php esc_html_e( 'All the bookings included in this booking group will also be delete.', 'booking-activities' ); ?>
		</p>
		<?php do_action( 'bookacti_delete_booking_form_after' ); ?>
	</form>
</div>


<div id='bookacti-export-bookings-dialog' class='bookacti-backend-dialog bookacti-bookings-dialog' style='display:none;' title='<?php esc_html_e( 'Export bookings to CSV', 'booking-activities' ); ?>'>
	<form id='bookacti-export-bookings-form'>
		<?php wp_nonce_field( 'bookacti_export_bookings_url', 'nonce_export_bookings_url', false ); ?>
		<p>
			<?php esc_html_e( 'This will export all the bookings of the current list (filters applied).', 'booking-activities' ); ?>
		</p>
		<div id='bookacti-columns-to-export-container' class='bookacti-items-container' data-type='columns-to-export'>
			<label for='bookacti-select-columns-to-export' class='bookacti-fullwidth-label' class='bookacti-items-container' data-type='participants-fields' >
				<?php 
					esc_html_e( 'Columns to export (ordered)', 'booking-activities' ); 
					bookacti_help_tip( esc_html__( 'Add the columns you want to export in the order they will be displayed.', 'booking-activities' ) );
				?>
			</label>
			<div class='bookacti-add-items-container'>
				<select id='bookacti-add-columns-to-export-selectbox' class='bookacti-add-new-items-select-box' >
				<?php
					$columns			= bookacti_get_bookings_export_columns();
					$selected_columns	= array_intersect_key( $columns, array_flip( bookacti_get_bookings_export_default_columns() ) );
					foreach( $columns as $column_name => $column_title ) {
						$disabled = isset( $selected_columns[ $column_name ] ) ? 'disabled style="display:none;"' : '';
						echo '<option value="' . $column_name . '" title="' . htmlentities( esc_attr( $column_title ), ENT_QUOTES ) . '" ' . $disabled . '>' . $column_title . '</option>';
					}
				?>
				</select>
				<button type='button' id='bookacti-add-columns-to-export' class='bookacti-add-items' ><?php esc_html_e( 'Add', 'booking-activities' ); ?></button>
			</div>
			<div class='bookacti-items-list-container' >
				<select name='columns[]' id='baaf-columns-to-export-selectbox' class='bookacti-items-select-box' multiple>
				<?php
					foreach( $selected_columns as $column_name => $column_title ) {
						echo '<option value="' . $column_name . '" title="' . htmlentities( esc_attr( $column_title ), ENT_QUOTES ) . '">' . $column_title . '</option>';
					}
				?>
				</select>
				<button type='button' id='baaf-remove-columns-to-export' class='bookacti-remove-items' ><?php esc_html_e( 'Remove selected', 'booking-activities' ); ?></button>
			</div>
		</div>
		<?php
			$per_page = intval( get_user_meta( get_current_user_id(), 'bookacti_bookings_per_page', true ) );
			$export_fields = apply_filters( 'bookacti_export_bookings_dialog_fields', array(
				'per_page' => array(
					'type'	=> 'number',
					'name'	=> 'per_page',
					'title'	=> esc_html__( 'Limit', 'booking-activities' ),
					'id'	=> 'bookacti-select-export-limit',
					'value'	=> $per_page ? $per_page : $bookings_list_table->get_rows_number_per_page(),
					'tip'	=> esc_html__( 'Maximum number of bookings to export. You may need to increase your PHP max execution time if this number is too high.', 'booking-activities' )
				),
				'export_groups' => array(
					'type'		=> 'select',
					'name'		=> 'export_groups',
					'title'		=> esc_html__( 'How to export the groups?', 'booking-activities' ),
					'id'		=> 'bookacti-select-export-groups',
					'options'	=> array(
						'groups' => esc_html__( 'One single row per group', 'booking-activities' ),
						'bookings' => esc_html__( 'One row for each booking of the group', 'booking-activities' )
					),
					'tip'		=> esc_html__( 'Choose how to export the grouped bookings. Do you want to export all the bookings of the group, or only the group as a single row?', 'booking-activities' )
				)
			));
			bookacti_display_fields( $export_fields );
		?>
		<div id='bookacti-export-bookings-url-container' style='display:none;'>
			<p><strong><?php esc_html_e( 'Secret address in CSV format', 'booking-activities' ); ?></strong></p>
			<div class='bookacti_export_url'>
				<div class='bookacti_export_url_field' ><input type='text' id='bookacti_export_bookings_url_secret' value='' readonly onfocus='this.select();' /></div>
				<div class='bookacti_export_button' ><input type='button' value='<?php esc_html( _ex( 'Export', 'action', 'booking-activities' ) ); ?>' /></div>
			</div>
			<p>
				<small>
					<?php esc_html_e( 'Visit this address to get a CSV export of your bookings (according to filters and settings above), or use it as a dynamic URL feed to synchronize with other apps.', 'booking-activities' ); ?>
				</small>
			</p>
			<p class='bookacti-warning'>
				<span class='dashicons dashicons-warning' ></span>
				<small>
					<?php 
						esc_html_e( 'This link provides real-time data. However, some apps may synchronize only every 24h, or more.', 'booking-activities' ); 
						echo ' ';
					?>
					<strong>
					<?php
						esc_html_e( 'That\'s why your changes won\'t be applied in real time on your synched apps.', 'booking-activities' ); 
					?>
					</strong>
				</small>
			</p>
			<p class='bookacti-warning'>
				<span class='dashicons dashicons-warning' ></span>
				<small>
					<?php 
						esc_html_e( 'Only share this address with those you trust to see all your bookings details.', 'booking-activities' );
						echo ' ';
						esc_html_e( 'You can reset your secret key with the "Reset" button below. This will nullify the previously generated export links.', 'booking-activities' );
					?>
				</small>
			</p>
		</div>
		<?php do_action( 'bookacti_export_bookings_after' ); ?>
	</form>
</div>


<?php 
do_action( 'bookacti_backend_bookings_dialogs' );