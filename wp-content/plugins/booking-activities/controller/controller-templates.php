<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// EVENTS
	/**
	 * AJAX Controller - Fetch events in order to display them
	 * @version 1.7.10
	 */
	function bookacti_controller_fetch_template_events() {
		
		$template_id = intval( $_POST['template_id'] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_fetch_template_events', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_read_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed || ! $template_id ) {
			bookacti_send_json_not_allowed( 'fetch_template_events' );
		}
		
		$event_id	= intval( $_POST[ 'event_id' ] );
		$interval	= bookacti_sanitize_events_interval( $_POST[ 'interval' ] );
		$interval[ 'past_events' ] = true;

		$events	= bookacti_fetch_events_for_calendar_editor( $template_id, $event_id, $interval );
		bookacti_send_json( array( 
			'status' => 'success', 
			'events' => $events[ 'events' ] ? $events[ 'events' ] : array(),
			'events_data' => $events[ 'data' ] ? $events[ 'data' ] : array()
		), 'fetch_template_events' );
	}
	add_action( 'wp_ajax_bookactiFetchTemplateEvents', 'bookacti_controller_fetch_template_events' );

	
	/**
	 * AJAX Controller - Add new event on calendar
	 * @version 1.7.10
	 */
	function bookacti_controller_insert_event() {
		$template_id = intval( $_POST['template_id'] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_event', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'insert_event' );
		}
			
		$activity_id		= intval( $_POST['activity_id'] );
		$event_title		= sanitize_text_field( stripslashes( $_POST['event_title'] ) );
		$event_start		= bookacti_sanitize_datetime( $_POST['event_start'] );
		$event_end			= bookacti_sanitize_datetime( $_POST['event_end'] );
		$event_availability	= intval( $_POST['event_availability'] );

		$event_id = bookacti_insert_event( $template_id, $activity_id, $event_title, $event_start, $event_end, $event_availability );

		if( ! $event_id ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_inserted' ), 'insert_event' );
		}
		
		$events = bookacti_fetch_events_for_calendar_editor( null, $event_id );

		do_action( 'bookacti_event_inserted', $event_id, $events );

		bookacti_send_json( array( 
			'status' => 'success', 
			'event_id' => $event_id,
			'event_data' => $events[ 'data' ][ $event_id ] ? $events[ 'data' ][ $event_id ] : array(),
		), 'insert_event' );
	}
	add_action( 'wp_ajax_bookactiInsertEvent', 'bookacti_controller_insert_event' );
	
	
	/**
	 * AJAX Controller - Move or resize an event, possibly while duplicating it
	 * @since 1.2.2 (was bookacti_controller_update_event)
	 * @version 1.7.10
	 */
	function bookacti_controller_move_or_resize_event() {
		$event_id       = intval( $_POST[ 'event_id' ] );
		$template_id	= bookacti_get_event_template_id( $event_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_move_or_resize_event', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'move_or_resize_event' );
		}
		
		$is_duplicated  = ! empty( $_POST[ 'is_duplicated' ] ) ? intval( $_POST[ 'is_duplicated' ] ) : 0;
		$filters		= bookacti_format_booking_filters( array( 'event_id' => $event_id, 'active' => 1 ) );
		$has_bookings	= bookacti_get_number_of_bookings( $filters );

		if( ! $is_duplicated && is_numeric( $has_bookings ) && $has_bookings > 0 ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'has_bookings' ), 'move_or_resize_event' );
		}

		$action			= $_POST[ 'action' ] === 'bookactiResizeEvent' ? 'resize' : 'move';
		$event_start    = bookacti_sanitize_datetime( $_POST[ 'event_start' ] );
		$event_end      = bookacti_sanitize_datetime( $_POST[ 'event_end' ] );
		$delta_days     = intval( $_POST[ 'delta_days' ] );
		$interval		= ! empty( $_POST[ 'interval' ] ) ? bookacti_sanitize_events_interval( $_POST[ 'interval' ] ) : array();

		// Maybe update grouped events if the event belong to a group
		if( ! $is_duplicated ) {
			bookacti_update_grouped_event_dates( $event_id, $event_start, $event_end, $action, $delta_days );
		}

		// Update the event
		$updated = bookacti_move_or_resize_event( $event_id, $event_start, $event_end, $action, $delta_days, $is_duplicated );

		if( $is_duplicated ) {
			$new_event_id = $updated;
			if( ! $new_event_id ) { 
				bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_updated' ), 'move_or_resize_event' ); 
			}
			
			$events		= bookacti_fetch_events_for_calendar_editor( null, $new_event_id, $interval );
			$exceptions	= bookacti_get_exceptions( null, $new_event_id );

			do_action( 'bookacti_event_duplicated', $event_id, $new_event_id, $events );

			bookacti_send_json( array( 
				'status'		=> 'success', 
				'event_id'		=> $new_event_id, 
				'events'		=> $events[ 'events' ] ? $events[ 'events' ] : array(),
				'event_data'	=> $events[ 'data' ][ $new_event_id ] ? $events[ 'data' ][ $new_event_id ] : array(),
				'exceptions'	=> $exceptions ), 'move_or_resize_event'
			);
			
		} else {
			if( $updated ){
				$events = bookacti_fetch_events_for_calendar_editor( null, $event_id, $interval );

				do_action( 'bookacti_event_moved_or_resized', $event_id, $events );

				bookacti_send_json( array( 
					'status' => 'success', 
					'events' => $events[ 'events' ] ? $events[ 'events' ] : array(),
					'event_data' => $events[ 'data' ][ $event_id ] ? $events[ 'data' ][ $event_id ] : array() 
				), 'move_or_resize_event' ); 
			} else if ( $updated === 0 ) { 
				bookacti_send_json( array( 'status' => 'nochanges' ), 'move_or_resize_event' ); 
			} else { 
				bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_updated' ), 'move_or_resize_event' ); 
			}
		}
	}
	add_action( 'wp_ajax_bookactiResizeEvent', 'bookacti_controller_move_or_resize_event' );
	add_action( 'wp_ajax_bookactiMoveEvent', 'bookacti_controller_move_or_resize_event' );
	
	
	/**
	 * AJAX Controller - Update event
	 * @since 1.2.2 (was bookacti_controller_update_event_data)
	 * @version 1.7.10
	 */
	function bookacti_controller_update_event() {
		$event_id			= intval( $_POST['event-id'] );
		$template_id		= bookacti_get_event_template_id( $event_id );
		
		// Check nonce and capabilities
		$is_allowed			= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		$is_nonce_valid		= check_ajax_referer( 'bookacti_update_event_data', 'nonce_update_event_data', false );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'update_event' );
		}
			
		$event_availability	= intval( $_POST['event-availability'] );
		$sanitized_freq		= sanitize_title_with_dashes( $_POST['event-repeat-freq'] );
		$event_repeat_freq	= in_array( $sanitized_freq, array( 'none', 'daily', 'weekly', 'monthly' ), true ) ? $sanitized_freq : 'none';
		$event_repeat_from	= isset( $_POST['event-repeat-from'] ) ? bookacti_sanitize_date( $_POST['event-repeat-from'] ) : '';
		$event_repeat_to	= isset( $_POST['event-repeat-to'] ) ? bookacti_sanitize_date( $_POST['event-repeat-to'] ) : '';
		$dates_excep_array	= isset( $_POST['event-repeat-excep'] ) ? bookacti_sanitize_date_array( $_POST['event-repeat-excep'] ) : array();

		// Check if input data are complete and consistent 
		$event_validation	= bookacti_validate_event( $event_id, $event_availability, $event_repeat_freq, $event_repeat_from, $event_repeat_to );

		if( $event_validation['status'] !== 'valid' ) {
			bookacti_send_json( $event_validation, 'update_event' );
		}
				
		$event_title		= wp_kses_post( stripslashes( $_POST['event-title'] ) );
		$event_start		= bookacti_sanitize_datetime( $_POST['event-start'] );
		$event_end			= bookacti_sanitize_datetime( $_POST['event-end'] );
		$settings			= isset( $_POST['eventOptions'] ) && is_array( $_POST['eventOptions'] ) ? $_POST['eventOptions'] : array();
		$formatted_settings = bookacti_format_event_settings( $settings );

		// Update event data
		$updated_event		= bookacti_update_event( $event_id, $event_title, $event_availability, $event_start, $event_end, $event_repeat_freq, $event_repeat_from, $event_repeat_to );

		// Update event metadata
		$updated_event_meta = bookacti_update_metadata( 'event', $event_id, $formatted_settings );

		// Insert new exception
		$inserted_excep		= bookacti_insert_exceptions( $event_id, $dates_excep_array );

		// Remove exceptions that do not longer exist
		$deleted_excep		= bookacti_remove_exceptions( $event_id, $dates_excep_array );


		// if one of the elements has been updated, consider as success
		if(	( is_numeric( $updated_event )		&& $updated_event > 0 )
		||  ( is_numeric( $updated_event_meta )	&& $updated_event_meta > 0 )
		||  ( is_numeric( $inserted_excep )		&& $inserted_excep > 0 )
		||  ( is_numeric( $deleted_excep )		&& $deleted_excep > 0 ) ){

			// Retrieve new events
			$interval	= bookacti_sanitize_events_interval( $_POST[ 'interval' ] );
			$events		= bookacti_fetch_events_for_calendar_editor( null, $event_id, $interval );

			// Retrieve groups of events
			$groups_events = bookacti_get_groups_events( $template_id );

			do_action( 'bookacti_event_updated', $event_id, $events );

			bookacti_send_json( array( 
				'status'		=> 'success', 
				'events'		=> $events[ 'events' ] ? $events[ 'events' ] : array(),
				'events_data'	=> $events[ 'data' ] ? $events[ 'data' ] : array(),
				'groups_events'	=> $groups_events,
				'results'		=> array( 
					'updated_event'		=> $updated_event, 
					'updated_event_meta'=> $updated_event_meta, 
					'inserted_excep'	=> $inserted_excep, 
					'deleted_excep'		=> $deleted_excep ) 
				), 'update_event' ); 

		} else if( $updated_event === 0 
				&& ! $updated_event_meta 
				&& $inserted_excep === 0 
				&& $deleted_excep === 0 ) { 

			bookacti_send_json( array( 'status' => 'nochanges' ), 'update_event' );

		} else if( $updated_event === false 
				|| $updated_event_meta === false 
				|| $inserted_excep === false 
				|| $deleted_excep === false ) { 

			bookacti_send_json( array( 
				'status' => 'failed', 
				'updated_event'		=> $updated_event, 
				'updated_event_meta'=> $updated_event_meta, 
				'inserted_excep'	=> $inserted_excep, 
				'deleted_excep'		=> $deleted_excep ), 'update_event' ); 
		} else { 

			bookacti_send_json( array( 
				'status' => 'failed', 
				'status' => 'unknown_error', 
				'updated_event'		=> $updated_event, 
				'updated_event_meta'=> $updated_event_meta, 
				'inserted_excep'	=> $inserted_excep, 
				'deleted_excep'		=> $deleted_excep ), 'update_event' ); 
		}
	}
	add_action( 'wp_ajax_bookactiUpdateEvent', 'bookacti_controller_update_event' );
	
	
	/**
	 * AJAX Controller - Get all exceptions for a given template and / or event
	 * @version 1.7.10
	 */
	function bookacti_controller_get_exceptions() {
		$template_id	= intval( $_POST['template_id'] );
		$event_id		= intval( $_POST['event_id'] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_get_exceptions', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_read_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'get_event_repetition_exceptions' );
		}
		
		$exceptions = bookacti_get_exceptions( $template_id, $event_id );

		if( count( $exceptions ) > 0 ) {
			bookacti_send_json( array( 'status' => 'success', 'exceptions' => $exceptions ), 'get_event_repetition_exceptions' );
		} else if( count( $exceptions ) === 0 ) {
			bookacti_send_json( array( 'status' => 'no_exception' ), 'get_event_repetition_exceptions' );
		} else {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'unknown' ), 'get_event_repetition_exceptions' );
		}
	}
	add_action( 'wp_ajax_bookactiGetExceptions', 'bookacti_controller_get_exceptions' );
	
	
	/**
	 * AJAX Controller - Delete an event if it doesn't have bookings
	 * @version 1.7.10
	 */
	function bookacti_controller_delete_event() {
		$event_id		= intval( $_POST['event_id'] );
		$template_id	= bookacti_get_event_template_id( $event_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_delete_event', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_event' );
		}
			
		$filters = bookacti_format_booking_filters( array( 'event_id' => $event_id, 'active' => 1 ) );
		$has_bookings = bookacti_get_number_of_bookings( $filters );

		if( is_numeric( $has_bookings ) && $has_bookings > 0 ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'has_bookings' ), 'deactivate_event' );
		}
			
		// Deactivate the event
		$deactivated = bookacti_deactivate_event( $event_id );

		// Delete the event from all groups
		bookacti_delete_event_from_groups( $event_id );

		if( $deactivated ) {
			do_action( 'bookacti_event_deactivated', $event_id );
			
			bookacti_send_json( array( 'status' => 'success' ), 'deactivate_event' );
		} else {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_deleted' ), 'deactivate_event' );
		}
	}
	add_action( 'wp_ajax_bookactiDeleteEvent', 'bookacti_controller_delete_event' );
	
	
	/**
	 * AJAX Controller - Delete an event without booking check
	 * @since 1.1.4
	 * @version 1.7.10
	 */
	function bookacti_controller_delete_event_forced() {
		$event_id		= intval( $_POST['event_id'] );
		$template_id	= bookacti_get_event_template_id( $event_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_delete_event_forced', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_event' );
		}

		// Deactivate the event
		$deactivated = bookacti_deactivate_event( $event_id );

		// Delete the event from all groups
		bookacti_delete_event_from_groups( $event_id );

		if( ! $deactivated ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_deleted' ), 'deactivate_event' );
		}
		
		do_action( 'bookacti_event_deactivated', $event_id );
			
		bookacti_send_json( array( 'status' => 'success' ), 'deactivate_event' );
	}
	add_action( 'wp_ajax_bookactiDeleteEventForced', 'bookacti_controller_delete_event_forced' );
	
	
	/**
	 * AJAX Controller - Unbind occurences of an event
	 * @version 1.7.10
	 */
	function bookacti_controller_unbind_occurrences() {
		$event_id		= intval( $_POST[ 'event_id' ] );
		$template_id	= bookacti_get_event_template_id( $event_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_unbind_occurences', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'unbind_occurences' );
		}
			
		$interval			= bookacti_sanitize_events_interval( $_POST[ 'interval' ] );
		$sanitized_unbind	= sanitize_title_with_dashes( $_POST[ 'unbind' ] );
		$unbind				= in_array( $sanitized_unbind, array( 'selected', 'booked' ), true ) ? $sanitized_unbind : 'selected';

		if( $unbind === 'selected' ) {
			$event_start	= bookacti_sanitize_datetime( $_POST[ 'event_start' ] );
			$event_end		= bookacti_sanitize_datetime( $_POST[ 'event_end' ] );
			$new_event_id	= bookacti_unbind_selected_occurrence( $event_id, $event_start, $event_end );
			$events			= bookacti_fetch_events_for_calendar_editor( null, $new_event_id, $interval );

		} else if( $unbind === 'booked' ) {
			$new_event_id	= bookacti_unbind_booked_occurrences( $event_id );
			$events			= bookacti_fetch_events_for_calendar_editor( null, array( $event_id, $new_event_id ), $interval );
		}

		// Retrieve affected data
		$exceptions		= bookacti_get_exceptions( $template_id );
		$groups_events	= bookacti_get_groups_events( $template_id );
		
		do_action( 'bookacti_event_occurences_unbound', $event_id, $new_event_id, $events );
		
		bookacti_send_json( array( 
			'status'		=> 'success', 
			'new_event_id'	=> $new_event_id,
			'events'		=> $events[ 'events' ] ? $events[ 'events' ] : array(),
			'events_data'	=> $events[ 'data' ] ? $events[ 'data' ] : array(),
			'groups_events' => $groups_events, 
			'exceptions'	=> $exceptions 
		), 'unbind_occurences' );
	}
	add_action( 'wp_ajax_bookactiUnbindOccurences', 'bookacti_controller_unbind_occurrences' );
	
	
	
// GROUPS OF EVENTS
	
	/**
	 * Create a group of events with AJAX
	 * @since 1.1.0
	 * @version 1.7.10
	 */
	function bookacti_controller_insert_group_of_events() {
		$template_id	= intval( $_POST[ 'template_id' ] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_group_of_events', 'nonce_insert_or_update_group_of_events', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'insert_group_of_events' );
		}
		
		$category_id	= intval( $_POST[ 'group-of-events-category' ] );
		$category_title	= sanitize_text_field( stripslashes( $_POST[ 'group-of-events-category-title' ] ) );
		$group_title	= sanitize_text_field( stripslashes( $_POST[ 'group-of-events-title' ] ) );
		$events			= json_decode( stripslashes( $_POST['events'] ) );
		
		// Validate input data
		$is_group_of_events_valid = bookacti_validate_group_of_events_data( $group_title, $category_id, $category_title, $events );
		if( $is_group_of_events_valid[ 'status' ] !== 'valid' ) {
			bookacti_send_json( array( 'status' => 'not_valid', 'errors' => $is_group_of_events_valid[ 'errors' ] ), 'insert_group_of_events' );
		}
		
		// Create category if it doesn't exists
		$is_category = bookacti_group_category_exists( $category_id, $template_id );
		
		if( ! $is_category ) {
			$cat_options_array	= isset( $_POST['groupCategoryOptions'] ) && is_array( $_POST['groupCategoryOptions'] ) ? $_POST['groupCategoryOptions'] : array();
			$category_settings	= bookacti_format_group_category_settings( $cat_options_array );
			$category_id		= bookacti_insert_group_category( $category_title, $template_id, $category_settings );
		}
		
		if( empty( $category_id ) ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'invalid_category', 'category_id' => $category_id ), 'insert_group_of_events' );
		}
		
		// Insert the new group of event
		$group_options_array	= isset( $_POST['groupOfEventsOptions'] ) && is_array( $_POST['groupOfEventsOptions'] ) ? $_POST['groupOfEventsOptions'] : array();
		$group_settings			= bookacti_format_group_of_events_settings( $group_options_array );
		$group_id				= bookacti_create_group_of_events( $events, $category_id, $group_title, $group_settings );
		
		if( empty( $group_id ) ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'invalid_events', 'events' => $events, 'group_id' => $group_id ), 'insert_group_of_events' );
		}
		
		$category_data	= bookacti_get_group_category( $category_id );
		$group_data		= bookacti_get_group_of_events( $group_id );
		$group_events	= bookacti_get_group_events( $group_id );
		$group_title_raw= strip_tags( $group_data->title );
		
		do_action( 'bookacti_group_of_events_inserted', $group_id, $group_events, $group_data, $category_data );
		
		bookacti_send_json( array( 
			'status' => 'success', 
			'group_id' => $group_id, 
			'group' => $group_data, 
			'group_title_raw' => $group_title_raw, 
			'group_events' => $group_events, 
			'category_id' => $category_id, 
			'category' => $category_data ), 'insert_group_of_events' );
	}
	add_action( 'wp_ajax_bookactiInsertGroupOfEvents', 'bookacti_controller_insert_group_of_events' );


	/**
	 * Update group of events data with AJAX
	 * @since 1.1.0
	 * @version 1.7.10
	 */
	function bookacti_controller_update_group_of_events() {
		$group_id		= intval( $_POST[ 'group_id' ] );
		$template_id	= bookacti_get_group_of_events_template_id( $group_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_group_of_events', 'nonce_insert_or_update_group_of_events', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'update_group_of_events' );
		}
		
		$category_id	= intval( $_POST[ 'group-of-events-category' ] );
		$category_title	= sanitize_text_field( stripslashes( $_POST[ 'group-of-events-category-title' ] ) );
		$group_title	= wp_kses_post( stripslashes( $_POST[ 'group-of-events-title' ] ) );
		$events			= json_decode( stripslashes( $_POST['events'] ) );
		
		// Validate input data
		$is_group_of_events_valid = bookacti_validate_group_of_events_data( $group_title, $category_id, $category_title, $events );
		if( $is_group_of_events_valid[ 'status' ] !== 'valid' ) {
			bookacti_send_json( array( 'status' => 'not_valid', 'errors' => $is_group_of_events_valid[ 'errors' ] ), 'update_group_of_events' );
		}
		
		// Create category if it doesn't exists
		$is_category = bookacti_group_category_exists( $category_id, $template_id );
		
		if( ! $is_category ) {
			$cat_options_array	= isset( $_POST['groupCategoryOptions'] ) && is_array( $_POST['groupCategoryOptions'] ) ? $_POST['groupCategoryOptions'] : array();
			$category_settings	= bookacti_format_group_category_settings( $cat_options_array );
			$category_id		= bookacti_insert_group_category( $category_title, $template_id, $category_settings );
		}
		
		if( empty( $category_id ) ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'invalid_category', 'category_id' => $category_id ), 'update_group_of_events' );
		}
		
		// Insert the new group of event
		$group_options_array	= isset( $_POST['groupOfEventsOptions'] ) && is_array( $_POST['groupOfEventsOptions'] ) ? $_POST['groupOfEventsOptions'] : array();
		$group_settings			= bookacti_format_group_of_events_settings( $group_options_array );
		$updated				= bookacti_edit_group_of_events( $group_id, $category_id, $group_title, $events, $group_settings );
		
		if( $updated === false ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'invalid_events', 'events' => $events, 'group_id' => $group_id ), 'update_group_of_events' );
		} else if( $updated === 0 ) {
			bookacti_send_json( array( 'status' => 'nochanges' ), 'update_group_of_events' );
		} else if( is_string( $updated ) ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => $updated ), 'update_group_of_events' );
		}
		
		$category_data	= bookacti_get_group_category( $category_id );
		$group_data		= bookacti_get_group_of_events( $group_id );
		$group_events	= bookacti_get_group_events( $group_id );
		$group_title_raw= strip_tags( $group_data->title );
		
		do_action( 'bookacti_group_of_events_updated', $group_id, $group_events, $group_data, $category_data );
		
		bookacti_send_json( array(
			'status' => 'success', 
			'group' => $group_data, 
			'group_title_raw' => $group_title_raw, 
			'group_events' => $group_events, 
			'category_id' => $category_id, 
			'category' => $category_data ), 'update_group_of_events' );
	}
	add_action( 'wp_ajax_bookactiUpdateGroupOfEvents', 'bookacti_controller_update_group_of_events' );
	
	
	/**
	 * Delete a group of events with AJAX
	 * @since 1.1.0
	 * @version 1.7.10
	 */
	function bookacti_controller_delete_group_of_events() {
		$group_id		= intval( $_POST['group_id'] );
		$template_id	= bookacti_get_group_of_events_template_id( $group_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_delete_group_of_events', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_group_of_events' );
		}
		
		// Check if the group has been booked
		$filters		= bookacti_format_booking_filters( array( 'event_group_id' => $group_id ) );
		$booking_groups = bookacti_get_booking_groups( $filters );

		// Delete groups with no bookings
		if( empty( $booking_groups ) ) {
			$deleted = bookacti_delete_group_of_events( $group_id );

		// Deactivate groups with bookings
		} else {
			$deleted = bookacti_deactivate_group_of_events( $group_id );
		}

		if( ! $deleted ) {
			bookacti_send_json( array( 'status' => 'failed' ), 'deactivate_group_of_events' );
		}
		
		do_action( 'bookacti_group_of_events_deactivated', $group_id );

		bookacti_send_json( array( 'status' => 'success' ), 'deactivate_group_of_events' );
	}
	add_action( 'wp_ajax_bookactiDeleteGroupOfEvents', 'bookacti_controller_delete_group_of_events' );
	

	
// GROUP CATEGORIES
	
	/**
	 * Update group category data with AJAX
	 * @since 1.1.0
	 * @version 1.7.10
	 */
	function bookacti_controller_update_group_category() {
		$category_id = intval( $_POST[ 'category_id' ] );
		$template_id = bookacti_get_group_category_template_id( $category_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_group_category', 'nonce_insert_or_update_group_category', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'update_group_category' );
		}
			
		$category_title = sanitize_text_field( stripslashes( $_POST[ 'group-category-title' ] ) );
		$is_group_category_valid = bookacti_validate_group_category_data( $category_title );

		// Update template only if its data are consistent
		if( $is_group_category_valid[ 'status' ] !== 'valid' ) {
			bookacti_send_json( array( 'status' => 'not_valid', 'errors' => $is_group_category_valid[ 'errors' ] ), 'update_group_category' );
		}
			
		$cat_options_array	= isset( $_POST['groupCategoryOptions'] ) && is_array( $_POST['groupCategoryOptions'] ) ? $_POST['groupCategoryOptions'] : array();
		$category_settings	= bookacti_format_group_category_settings( $cat_options_array );

		$updated = bookacti_update_group_category( $category_id, $category_title, $category_settings );

		$category = bookacti_get_group_category( $category_id );

		if( $updated ) {
			do_action( 'bookacti_group_category_updated', $category_id, $category );
			
			bookacti_send_json( array( 'status' => 'success', 'category' => $category ), 'update_group_category' );
		} else if ( $updated === 0 ) { 
			bookacti_send_json( array( 'status' => 'nochanges', 'category' => $category ), 'update_group_category' );
		} else if ( $updated === false ) { 
			bookacti_send_json( array( 'status' => 'failed' ), 'update_group_category' );
		}
	}
	add_action( 'wp_ajax_bookactiUpdateGroupCategory', 'bookacti_controller_update_group_category' );


	/**
	 * Delete a group category with AJAX
	 * @since 1.1.0
	 * @version 1.7.10
	 */
	function bookacti_controller_delete_group_category() {
		$category_id	= intval( $_POST[ 'category_id' ] );
		$template_id	= bookacti_get_group_category_template_id( $category_id );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_delete_group_category', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_group_category' );
		}
			
		$groups_ids = bookacti_get_groups_of_events_ids_by_category( $category_id, true );

		// Check if one of the groups of this category has been booked
		$delete_category = true;
		foreach( $groups_ids as $group_id ) {
			$filters		= bookacti_format_booking_filters( array( 'event_group_id' => $group_id ) );
			$booking_groups = bookacti_get_booking_groups( $filters );

			// Delete groups with no bookings
			if( empty( $booking_groups ) ) {
				bookacti_delete_group_of_events( $group_id );

			// Deactivate groups with bookings
			} else {
				bookacti_deactivate_group_of_events( $group_id );
				$delete_category = false;
			}
		}

		// If one of its groups is booked, do not delete the category, simply deactivate it
		if( $delete_category ) {
			$deleted = bookacti_delete_group_category( $category_id );
		} else {
			$deleted = bookacti_deactivate_group_category( $category_id );
		}

		if( ! $deleted ) {
			bookacti_send_json( array( 'status' => 'failed' ), 'deactivate_group_category' );
		}
		
		do_action( 'bookacti_group_category_deactivated', $category_id );
		
		bookacti_send_json( array( 'status' => 'success' ), 'deactivate_group_category' );
	}
	add_action( 'wp_ajax_bookactiDeleteGroupCategory', 'bookacti_controller_delete_group_category' );
	
	
	
// TEMPLATES

	/**
	 * AJAX Controller - Create a new template
	 * @version	1.7.10
	 */
	function bookacti_controller_insert_template() {
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_template', 'nonce_insert_or_update_template', false );
		$is_allowed		= current_user_can( 'bookacti_create_templates' );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'insert_template' );
		}
			
		$template_title	= sanitize_text_field( stripslashes( $_POST['template-title'] ) );
		$template_start	= bookacti_sanitize_date( $_POST['template-opening'] );
		$template_end	= bookacti_sanitize_date( $_POST['template-closing'] );

		$is_template_valid = bookacti_validate_template_data( $template_title, $template_start, $template_end );

		// Create template only if its data are consistent
		if( $is_template_valid['status'] !== 'valid' ) {
			bookacti_send_json( array( 'status' => 'failed' ), 'insert_template' );
		}

		$duplicated_template_id	= intval( $_POST['duplicated-template-id'] );
		$managers_array			= isset( $_POST['template-managers'] ) ? bookacti_ids_to_array( $_POST['template-managers'] ) : array();
		$options_array			= isset( $_POST['templateOptions'] ) && is_array( $_POST['templateOptions'] ) ? $_POST['templateOptions'] : array();

		$template_managers		= bookacti_format_template_managers( $managers_array );
		$template_settings		= bookacti_format_template_settings( $options_array );

		$lastid = bookacti_insert_template( $template_title, $template_start, $template_end, $template_managers, $template_settings, $duplicated_template_id );

		if( ! $lastid ) {
			bookacti_send_json( array( 'status' => 'failed' ), 'insert_template' );
		}

		do_action( 'bookacti_template_inserted', $lastid );

		bookacti_send_json( array( 'status' => 'success', 'template_id' => $lastid ), 'insert_template' );
	}
	add_action( 'wp_ajax_bookactiInsertTemplate', 'bookacti_controller_insert_template' );
	
	
	/**
	 * AJAX Controller - Update template
	 * @version	1.7.10
	 */
	function bookacti_controller_update_template() {
		$template_id	= intval( $_POST['template-id'] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_template', 'nonce_insert_or_update_template', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'update_template' );
		}
			
		$template_title	= sanitize_text_field( stripslashes( $_POST['template-title'] ) );
		$template_start	= bookacti_sanitize_date( $_POST['template-opening'] );
		$template_end	= bookacti_sanitize_date( $_POST['template-closing'] );

		$is_template_valid = bookacti_validate_template_data( $template_title, $template_start, $template_end );

		// Update template only if its data are consistent
		if( $is_template_valid['status'] !== 'valid' ) {
			bookacti_send_json( array( 'status' => 'not_valid', 'errors' => $is_template_valid[ 'errors' ] ), 'update_template' );
		}

		$updated_template	= bookacti_update_template( $template_id, $template_title, $template_start, $template_end );

		$updated_metadata = 0;
		if( isset( $_POST['templateOptions'] ) ) {
			$template_settings	= bookacti_format_template_settings( $_POST['templateOptions'] );
			$updated_metadata	= bookacti_update_metadata( 'template', $template_id, $template_settings );
		}

		$updated_managers = 0;
		if( isset( $_POST['template-managers'] ) ) {
			$managers_array		= bookacti_ids_to_array( $_POST['template-managers'] );
			$template_managers	= bookacti_format_template_managers( $managers_array );
			$updated_managers	= bookacti_update_managers( 'template', $template_id, $template_managers );
		}

		if( $updated_template > 0 || intval( $updated_managers ) > 0 || intval( $updated_metadata ) > 0 ) {
			$templates_data = bookacti_get_templates_data( $template_id, true );

			do_action( 'bookacti_template_updated', $template_id, $templates_data[ $template_id ] );

			bookacti_send_json( array( 'status' => 'success', 'template_data' => $templates_data[ $template_id ] ), 'update_template' );

		} else if( $updated_template === 0 && ! $updated_managers && ! $updated_metadata ) { 
			bookacti_send_json( array( 'status' => 'nochanges' ), 'update_template' );

		} else if( $updated_template === false ) { 
			bookacti_send_json( array( 'status' => 'failed' ), 'update_template' );
		}
	}
	add_action( 'wp_ajax_bookactiUpdateTemplate', 'bookacti_controller_update_template' );
	
	
	/**
	 * AJAX Controller - Deactivate a template
	 * @version 1.7.10
	 */
	function bookacti_controller_deactivate_template() {
		$template_id = intval( $_POST['template_id'] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_deactivate_template', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_delete_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_template' );
		}

		$deactivated = bookacti_deactivate_template( $template_id );

		if( ! $deactivated ) {
			bookacti_send_json( array( 'status' => 'failed' ), 'deactivate_template' );
		} 
		
		do_action( 'bookacti_template_deactivated', $template_id );
		
		bookacti_send_json( array( 'status' => 'success' ), 'deactivate_template' );
	}
	add_action( 'wp_ajax_bookactiDeactivateTemplate', 'bookacti_controller_deactivate_template' );
	
	
	/**
	 * AJAX Controller - Change default template
	 * @version	1.7.4
	 */
	function bookacti_controller_switch_template() {
		$template_id = intval( $_POST[ 'template_id' ] );
		
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_switch_template', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );
		
		if( ! $is_nonce_valid || ! $is_allowed ) { bookacti_send_json_not_allowed( 'switch_template' ); }
		
		$updated			= update_user_meta( get_current_user_id(), 'bookacti_default_template', $template_id );
		$activities_list	= bookacti_get_template_activities_list( $template_id );
		$groups_list		= bookacti_get_template_groups_of_events_list( $template_id );
		
		$atts = json_decode( stripslashes( $_POST[ 'attributes' ] ), true );
		$booking_system_data = bookacti_get_editor_booking_system_data( $atts, $template_id );
		
		bookacti_send_json( array(
			'status'				=> 'success',
			'activities_list'		=> $activities_list, 
			'groups_list'			=> $groups_list,
			'booking_system_data'	=> $booking_system_data
		), 'switch_template' );
	}
	add_action( 'wp_ajax_bookactiSwitchTemplate', 'bookacti_controller_switch_template' );


	
// ACTIVITIES

	/**
	 * AJAX Controller - Create a new activity
	 * @version 1.7.10
	 */
	function bookacti_controller_insert_activity() {
		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_activity', 'nonce_insert_or_update_activity', false );
		$is_allowed		= current_user_can( 'bookacti_create_activities' );
		$template_id	= intval( $_POST['template-id'] );

		if( ! $is_nonce_valid || ! $is_allowed || ! $template_id ) {
			bookacti_send_json_not_allowed( 'insert_activity' );
		}

		$activity_title			= sanitize_text_field( stripslashes( $_POST['activity-title'] ) );
		$activity_color			= function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $_POST['activity-color'] ) : stripslashes( $_POST['activity-color'] );
		$activity_availability	= intval( $_POST['activity-availability'] );
		$activity_duration		= bookacti_sanitize_duration( $_POST['activity-duration'] );
		$activity_resizable		= intval( $_POST['activity-resizable'] );
		$managers_array			= isset( $_POST['activity-managers'] ) ? bookacti_ids_to_array( $_POST['activity-managers'] ) : array();
		$options_array			= isset( $_POST['activityOptions'] ) && is_array( $_POST['activityOptions'] ) ? $_POST['activityOptions'] : array();

		// Format arrays and check templates permissions
		$activity_managers	= bookacti_format_activity_managers( $managers_array );
		$activity_settings	= bookacti_format_activity_settings( $options_array );

		// Insert the activity and its metadata
		$activity_id = bookacti_insert_activity( $activity_title, $activity_color, $activity_availability, $activity_duration, $activity_resizable );
		
		if( ! $activity_id ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_inserted' ), 'insert_activity' );	
		}
		
		bookacti_insert_managers( 'activity', $activity_id, $activity_managers );
		bookacti_insert_metadata( 'activity', $activity_id, $activity_settings );

		// Bind the activity to the current template
		$bound = bookacti_bind_activities_to_template( $activity_id, $template_id );

		if( ! $bound ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'cannot_bind_to_template' ), 'insert_activity' );	
		}
		
		$activity_data	= bookacti_get_activity( $activity_id );
		$activity_list	= bookacti_get_template_activities_list( $template_id );
		
		do_action( 'bookacti_activity_inserted', $activity_id, $activity_data );
		
		bookacti_send_json( array( 'status' => 'success', 'activity_id' => $activity_id, 'activity_data' => $activity_data, 'activity_list' => $activity_list ), 'insert_activity' );
	}
	add_action( 'wp_ajax_bookactiInsertActivity', 'bookacti_controller_insert_activity' );
    
	
    /**
	 * AJAX Controller - Update an activity
	 * @version 1.7.10
	 */
	function bookacti_controller_update_activity() {
		$activity_id	= intval( $_POST['activity-id'] );
		$template_id	= intval( $_POST['template-id'] );

		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_insert_or_update_activity', 'nonce_insert_or_update_activity', false );
		$is_allowed		= current_user_can( 'bookacti_edit_activities' ) && bookacti_user_can_manage_activity( $activity_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'update_activity' );
		}

		$activity_title			= sanitize_text_field( stripslashes( $_POST['activity-title'] ) );
		$activity_old_title		= sanitize_text_field( stripslashes( $_POST['activity-old-title'] ) );
		$activity_color			= function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $_POST['activity-color'] ) : stripslashes( $_POST['activity-color'] );
		$activity_availability	= intval( $_POST['activity-availability'] );
		$activity_duration		= bookacti_sanitize_duration( $_POST['activity-duration'] );
		$activity_resizable		= intval( $_POST['activity-resizable'] );
		$managers_array			= isset( $_POST['activity-managers'] ) ? bookacti_ids_to_array( $_POST['activity-managers'] ) : array();
		$options_array			= isset( $_POST['activityOptions'] ) && is_array( $_POST['activityOptions'] ) ? $_POST['activityOptions'] : array();

		// Format arrays and check templates permissions
		$activity_managers	= bookacti_format_activity_managers( $managers_array );
		$activity_settings	= bookacti_format_activity_settings( $options_array );

		// Update the activity and its metadata
		$updated_activity	= bookacti_update_activity( $activity_id, $activity_title, $activity_color, $activity_availability, $activity_duration, $activity_resizable );
		$updated_managers	= bookacti_update_managers( 'activity', $activity_id, $activity_managers );
		$updated_metadata	= bookacti_update_metadata( 'activity', $activity_id, $activity_settings );

		// Update the event title bound to this activity
		$updated_events		= bookacti_update_events_title( $activity_id, $activity_old_title, $activity_title );

		if( $updated_activity > 0 || $updated_events > 0 || intval( $updated_managers ) > 0 || intval( $updated_metadata ) > 0 ){
			$activity_data	= bookacti_get_activity( $activity_id );
			$activity_list	= bookacti_get_template_activities_list( $template_id );
			
			do_action( 'bookacti_activity_updated', $activity_id, $activity_data );
			
			bookacti_send_json( array( 'status' => 'success', 'activity_data' => $activity_data, 'activity_list' => $activity_list ), 'update_activity' );
		} else if ( $updated_activity === false && $updated_events >= 0 ){ 
			bookacti_send_json( array( 'status' => 'failed_update_activity' ), 'update_activity' ); 
		} else if ( $updated_events === false && $updated_activity >= 0 ){ 
			bookacti_send_json( array( 'status' => 'failed_update_bound_events' ), 'update_activity' );
		} else if ( $updated_activity === 0 && $updated_events === 0 && ! $updated_managers && ! $updated_metadata ){ 
			bookacti_send_json( array( 'status' => 'no_changes' ), 'update_activity' );
		}
	}
	add_action( 'wp_ajax_bookactiUpdateActivity', 'bookacti_controller_update_activity' );
    
	
	/**
	 * AJAX Controller - Create an association between existing activities (on various templates) and current template
	 * @version 1.7.10
	 */
	function bookacti_controller_import_activities() {
		$template_id = intval( $_POST[ 'template_id' ] );

		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_import_activity', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_activities' ) && current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'import_activities' );
		}

		$activity_ids	= bookacti_ids_to_array( $_POST[ 'activity_ids' ] );
		$was_empty		= empty( $activity_ids );
		$inserted		= false;

		// Check activity permissions, and remove not allowed activity ids
		if( ! $was_empty ) {
			foreach( $activity_ids as $i => $activity_id ) {
				$can_manage_activity = bookacti_user_can_manage_activity( $activity_id );
				if( ! $can_manage_activity ) {
					unset( $activity_ids[ $i ] );
				}
			}
			if( $activity_ids ) {
				$inserted = bookacti_bind_activities_to_template( $activity_ids, $template_id );
			}
		}

		if( $inserted ) {
			$activities_data	= bookacti_get_activities_by_template( $template_id, false );
			$activity_list		= bookacti_get_template_activities_list( $template_id );
			
			do_action( 'bookacti_activities_imported', $template_id, $activity_ids, $activities_data );
			
			bookacti_send_json( array( 'status' => 'success', 'activities_data' => $activities_data, 'activity_list' => $activity_list ), 'import_activities' );
		} else if( $was_empty ) {
			bookacti_send_json( array( 'status' => 'no_activity' ), 'import_activities' );
		} else if( ! $was_empty && empty( $activity_ids ) ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'not_allowed' ), 'import_activities' );
		} else {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'unknown', 'activity_ids' => $activity_ids, 'template_id' => $template_id, 'inserted' => $inserted ), 'import_activities' );
		}
	}
	add_action( 'wp_ajax_bookactiImportActivities', 'bookacti_controller_import_activities' );
	
	
	/**
	 * AJAX Controller - Deactivate an activity
	 * @version 1.7.10
	 */
	function bookacti_controller_deactivate_activity() {
		$activity_id = intval( $_POST[ 'activity_id' ] );
		$template_id = intval( $_POST[ 'template_id' ] );

		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_deactivate_activity', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_delete_activities' ) && bookacti_user_can_manage_activity( $activity_id ) && current_user_can( 'bookacti_edit_templates' ) && bookacti_user_can_manage_template( $template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) {
			bookacti_send_json_not_allowed( 'deactivate_activity' );
		}

		$deleted = bookacti_delete_templates_x_activities( array( $template_id ), array( $activity_id ) );
		if( ! $deleted ) {
			bookacti_send_json( array( 'status' => 'failed', 'error' => 'cannot_delete_template_association' ), 'deactivate_activity' );	
		}
		
		do_action( 'bookacti_activity_template_association_removed', $activity_id, $template_id );
		
		// If the activity isn't bound to any template, deactivate it
		$templates = bookacti_get_templates_by_activity( $activity_id );
		if( empty( $templates ) ) {
			$deactivated = bookacti_deactivate_activity( $activity_id );
			if( $deactivated ) {
				do_action( 'bookacti_activity_deactivated', $activity_id );
			}
		}

		$delete_events	= intval( $_POST[ 'delete_events' ] );
		if( $delete_events ) {
			// Delete the events
			$deactivated1 = bookacti_deactivate_activity_events( $activity_id, $template_id );
			// Delete the events from all groups
			$deactivated2 = bookacti_deactivate_activity_events_from_groups( $activity_id, $template_id );

			if( ! is_numeric( $deactivated1 ) || ! is_numeric( $deactivated2 ) ) {
				bookacti_send_json( array( 'status' => 'failed', 'error' => 'cannot_delete_events_or_groups' ), 'deactivate_activity' );
			}
			
			do_action( 'bookacti_activity_events_deactivated', $template_id, $activity_id );
		}

		bookacti_send_json( array( 'status' => 'success' ), 'deactivate_activity' );
	}
	add_action( 'wp_ajax_bookactiDeactivateActivity', 'bookacti_controller_deactivate_activity' );


	/**
	 * AJAX Controller - Get activities by template
	 * @version 1.7.4
	 */
	function bookacti_controller_get_activities_by_template() {

		$selected_template_id	= intval( $_POST[ 'selected_template_id' ] );
		$current_template_id	= intval( $_POST[ 'current_template_id' ] );

		// Check nonce and capabilities
		$is_nonce_valid	= check_ajax_referer( 'bookacti_get_activities_by_template', 'nonce', false );
		$is_allowed		= current_user_can( 'bookacti_edit_activities' ) && current_user_can( 'bookacti_read_templates' ) && bookacti_user_can_manage_template( $selected_template_id );

		if( ! $is_nonce_valid || ! $is_allowed ) { bookacti_send_json_not_allowed( 'get_activities_by_template' ); }

		if( $selected_template_id === $current_template_id ) { bookacti_send_json( array( 'status' => 'failed', 'error' => 'no_change' ), 'get_activities_by_template' ); }

		$new_activities		= bookacti_get_activities_by_template( $selected_template_id, false );
		$current_activities	= bookacti_get_activity_ids_by_template( $current_template_id, false );

		// Check activity permissions, and remove not allowed activity ids
		foreach( $new_activities as $new_activity_id => $new_activity ) {
			if( ! in_array( $new_activity_id, $current_activities ) ) {
				$is_allowed = bookacti_user_can_manage_activity( $new_activity_id );
				if( ! $is_allowed || ! $new_activity[ 'active' ] ) {
					unset( $new_activities[ $new_activity_id ] );
				}
			} else {
				unset( $new_activities[ $new_activity_id ] );
			}
		}

		if( is_array( $new_activities ) && ! empty( $new_activities ) ) {
			bookacti_send_json( array( 'status' => 'success', 'activities' => $new_activities ), 'get_activities_by_template' );
		} else if( is_array( $new_activities ) && empty( $new_activities )  ) {
			bookacti_send_json( array( 'status' => 'no_activity' ), 'get_activities_by_template' );
		}
		
		bookacti_send_json( array( 'status' => 'failed', 'activities' => $new_activities ), 'get_activities_by_template' );
	}
	add_action( 'wp_ajax_bookactiGetActivitiesByTemplate', 'bookacti_controller_get_activities_by_template' );