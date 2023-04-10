<?php
$gatm_google_analytics_code = get_option( 'gatm_google_analytics_code' );
$gatm_google_tag_manager_code = get_option( 'gatm_google_tag_manager_code' );

$existing_tracking_codes = $this->detect_existing_tracking_codes();

if ( $existing_tracking_codes ) {
    echo '<div class="notice notice-warning is-dismissible"><p>' . __( 'Warning: Another Google Analytics or Google Tag Manager tracking code has been detected. Using multiple tracking codes may cause incorrect data in your reports.', 'google-analytics-tag-manager' ) . '</p></div>';
}

?>

<div class="wrap">
	<h1><?php echo esc_html( 'Adjective Digital - Google Analtyics and Google Tag Manager' ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'google-analytics-tag-manager' );
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="gatm_google_analytics_code"><?php _e( 'Google Analytics Code', 'google-analytics-tag-manager' ); ?></label></th>
				<td>
					<textarea name="gatm_google_analytics_code" id="gatm_google_analytics_code" class="large-text code" rows="10"><?php echo esc_textarea( $gatm_google_analytics_code ); ?></textarea>
					<p class="description"><?php _e( 'Paste your Google Analytics tracking code here.', 'google-analytics-tag-manager' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="gatm_google_tag_manager_code"><?php _e( 'Google Tag Manager Code', 'google-analytics-tag-manager' ); ?></label></th>
				<td>
					<textarea name="gatm_google_tag_manager_code" id="gatm_google_tag_manager_code" class="large-text code" rows="10"><?php echo esc_textarea( $gatm_google_tag_manager_code ); ?></textarea>
					<p class="description"><?php echo esc_html( 'There will be two code snippets for Google Tag Manager. One for <head> and one for <body>, paste both tracking code snippets here.', 'google-analytics-tag-manager' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>

	<p><?php printf( __( 'Need help getting your tracking code? Check the <a href="%1$s" target="_blank">Google Analytics documentation</a> or <a href="%2$s" target="_blank">Google Tag Manager documentation</a>.', 'google-analytics-tag-manager' ), 'https://support.google.com/analytics/answer/1008080', 'https://developers.google.com/tag-manager/quickstart' ); ?></p>
	<p><?php printf( __( 'Need analytics or marketing support? Get in touch with us - <a href="%1$s" target="_blank">Adjective Digital</a>.', 'google-analytics-tag-manager' ), 'https://adjectivedigital.com.au/contact/'); ?></p>
</div>
