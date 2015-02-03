<?php
/** @type bool $success */
/** @type string $reset_db_page_url */
/** @type string $reattempt_action_url */
/** @type string $reset_db_action_url */
/** @type EE_Data_Migration_Script_Base $most_recent_migration */
?>
<?php if ( $success ) { ?>
	<h1><?php _e( "Data Migration Report Sent", "event_espresso" ); ?></h1>
	<p><?php _e( "A migration crash report email was sent to Event Espresso. You should hear back from us soon.", "event_espresso" ); ?></p>
<?php } else {//didn't send email properly ?>
	<h1><?php _e( "Migration Report not sent", "event_espresso" ); ?></h1>
	<p><?php _e( "An error occurred and we were not able to automatically send a report to Event Espresso support.", "event_espresso" ); ?></p>
	<p><?php printf( __( "Please copy-and-paste the system information below to %s", "event_espresso" ), '<a href="mailto:' . EE_SUPPORT_EMAIL . '">' .EE_SUPPORT_EMAIL . "</a>" ); ?></p>
	<label>
		<?php _e( "system status info", "event_espresso" ); ?>
		<textarea name="system_status_info" class="system_status_info"><?php print_r( EEM_System_Status::instance()->get_system_stati() ); ?></textarea>
	</label>
<?php } ?>


<h1><?php _e( "What's next?", 'event_espresso' ); ?></h1>
<p><?php _e( "Well, it depends on your situation:", 'event_espresso' ); ?></p>
<div class='ee-table-wrap'>
	<table>
		<thead>
			<tr>
				<th><?php _e( "Your Situation", 'event_espresso' ); ?></th>
				<th><?php _e( "Suggested Action", 'event_espresso' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><p class='big-text'><?php _e( "I want to retry migrating my data", 'event_espresso' ); ?></p></td>
				<td>
					<p>
						<?php
						printf(
							__( 'First, %1$s check the forums %2$s to see if there is a solution before re-attempting to migrate.', 'event_espresso' ),
							"<a href='" . EE_SUPPORT_EMAIL . "' target='_blank'>",
							'</a>'
						);
						?>
					</p>

					<p>
						<?php
						printf(
							__( 'To retry migrating your data: restore to the backup you made before the migration and reactivate EE (and any addons you are using) and re-run the migration scripts. If you did not make a database migration and are migrating from EE3: delete your EE4 data (use the %1$s Reset/Delete Data tab above%2$s), and then reactivate EE4, and then re-run the migration scripts. If you did not make a database backup, are not migrating from EE3, have found a solution to the fatal error you received, and are willing to possibly have some data lost, %3$sattempt to continue migrating%2$s.', 'event_espresso' ),
							"<a href='$reset_db_page_url'>",
							"</a>",
							"<a class='button-primary' href='$reattempt_action_url'>"
						);
						?>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p class='big-text'><?php _e( "I want to hear from Support before proceeding", 'event_espresso' ); ?></p>
				</td>
				<td>
					<?php
					printf(
						__( 'Just make sure you\'ve %1$s checked for a solution in the forums,%2$s and properly contacted Support. We will get back to you as soon as possible', 'event_espresso' ),
						"<a href='" . EE_SUPPORT_EMAIL . "'>",
						"</a>"
					);
					?>
				</td>
			</tr>
			<tr>
				<td>
					<p class='big-text'><?php printf( __( 'I don\'t need my old EE %s data', 'event_espresso' ), $most_recent_migration->slug() ); ?></p>
				</td>
				<td>
					<?php
					printf(
						__( 'If you are ok with losing all the EE %1$s data, you can skip the migrations and %2$s use EE4 with only default Data%3$s', 'event_espresso' ),
						$most_recent_migration->slug(),
						"<a id='do-not-migrate' class='do-not-migrate button-primary' href='$reset_db_action_url'>",
						"</a>"
					);
					?>
				</td>
			</tr>
			<tr>
				<td>
					<p class='big-text'><?php printf( __( 'I want to go back to my old version of EE %1$s', 'event_espresso' ), $most_recent_migration->slug() ); ?>
				</td>
				<td>
					<p>
					<?php
					printf(
						__( 'Then we suggest you re-activate the old version of EE %3$s and restore your database to the backup you made just before migrating . If you didn\'t backup your database and are migrating from EE3, you can also delete your EE4 data (use on the %1$s"Reset/Delete Data" tab above%2$s), and then reactivate EE3 from the plugins page. Note: some of your EE3 shortcodes may have been changed to their EE4 equivalents, so you will need to change them back.', 'event_espresso' ),
						"<a href='$reset_db_page_url'>",
						"</a>",
						$most_recent_migration->slug()
					);
					?>
					</p>
					<p><?php _e( "If you ever decide to re-attempt using EE4, you will again be given the option to migrate your EE3 data or not.", 'event_espresso' ); ?></p>
				</td>
			</tr>

		</tbody>
	</table>
</div>