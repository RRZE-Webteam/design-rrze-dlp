<?php

/* ------------------ */
/* theme options page */
/* ------------------ */

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'rrze_dlp_options', 'rrze_dlp_theme_options', 'rrze_dlp_validate_options' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page('Optionen', 'Optionen', 'edit_theme_options', 'theme-optionen', 'rrze_dlp_theme_options_page' );	// Seitentitel, Titel in der Navi, Berechtigung zum Editieren (http://codex.wordpress.org/Roles_and_Capabilities) , Slug, Funktion
}

/**
 * Create the options page
 */
function rrze_dlp_theme_options_page() {
global $select_options, $radio_options;
if ( ! isset( $_REQUEST['settings-updated'] ) )
	$_REQUEST['settings-updated'] = false; ?>

<div class="wrap">
	<h2>Einstellungen für <?php bloginfo('name'); ?></h2>

<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
<div class="updated fade">
	<p><strong><?php _e( 'Settings have been updated.', 'rrze-dlp' ); ?></strong></p>
</div>
<?php endif; ?>

	<form method="post" action="options.php">
	<?php settings_fields( 'rrze_dlp_options' ); ?>
    <?php $options = get_option( 'rrze_dlp_theme_options' ); ?>

		<div id="message" class="update-nag">
			<h3>Absprache vom 2.10.2013:</h3>
			<p>Folgende Abschnitte werden NICHT allgemein angezeigt:</p>
			<ul>
				<li>Basisdienstleistungen</li>
				<li>Preis Basisdienstleistungen</li>
				<li>Leistungserweiterungen</li>
				<li>Preis Leistungserweiterungen</li>
				<li>Abhängigkeiten</li>
			</ul>
		</div>

	<table class="form-table">
      <tr>
        <th scope="col">Feld</th>
        <th scope="col">Label</th>
        <th scope="col">Öffentlich sichtbar</th>
	  </tr>
	  <tr>
        <th scope="row">service</th>
        <td><input id="label_service" class="regular-text" type="text" name="label_service" value="<?php esc_attr( $options['service'] ); ?>" /></td>
        <td><input id="display_service" name="display_service"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">beschreibung</th>
        <td><input id="label_beschreibung" class="regular-text" type="text" name="label_beschreibung" value="<?php esc_attr( $options['beschreibung'] ); ?>" /></td>
        <td><input id="display_beschreibung" name="display_beschreibung"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">umfang</th>
        <td><input id="label_umfang" class="regular-text" type="text" name="label_umfang" value="<?php esc_attr( $options['umfang'] ); ?>" /></td>
        <td><input id="display_umfang" name="display_umfang"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">links_zu_dokumentation</th>
        <td><input id="label_links_zu_dokumentation" class="regular-text" type="text" name="label_links_zu_dokumentation" value="<?php esc_attr( $options['links_zu_dokumentation'] ); ?>" /></td>
        <td><input id="display_links_zu_dokumentation" name="display_links_zu_dokumentation"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">basisdienstleistungen</th>
        <td><input id="label_basisdienstleistungen" class="regular-text" type="text" name="label_basisdienstleistungen" value="<?php esc_attr( $options['basisdienstleistungen'] ); ?>" /></td>
        <td><input id="display_basisdienstleistungen" name="display_basisdienstleistungen"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">preis_basisdienstleistungen</th>
        <td><input id="label_preis_basisdienstleistungen" class="regular-text" type="text" name="label_preis_basisdienstleistungen" value="<?php esc_attr( $options['preis_basisdienstleistungen'] ); ?>" /></td>
        <td><input id="display_preis_basisdienstleistungen" name="display_preis_basisdienstleistungen"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">leistungserweiterungen</th>
        <td><input id="label_leistungserweiterungen" class="regular-text" type="text" name="label_leistungserweiterungen" value="<?php esc_attr( $options['leistungserweiterungen'] ); ?>" /></td>
        <td><input id="display_leistungserweiterungen" name="display_leistungserweiterungen"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">preis_leistungserweiterungen</th>
        <td><input id="label_preis_leistungserweiterungen" class="regular-text" type="text" name="label_preis_leistungserweiterungen" value="<?php esc_attr( $options['preis_leistungserweiterungen'] ); ?>" /></td>
        <td><input id="display_preis_leistungserweiterungen" name="display_preis_leistungserweiterungen"
				   type="checkbox" value="1"></td>
	  </tr>
	  <tr>
        <th scope="row">kontakt</th>
        <td><input id="label_kontakt" class="regular-text" type="text" name="label_kontakt" value="<?php esc_attr( $options['kontakt'] ); ?>" /></td>
        <td><input id="display_kontakt" name="display_kontakt"
				   type="checkbox" value="1"></td>
	  </tr>
	</table>

    <!-- submit -->
    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Update settings', 'rrze-dlp') ?>" /></p>
  </form>
</div>
<?php }

// Strip HTML-Code:
// Hier kann definiert werden, ob HTML-Code in einem Eingabefeld
// automatisch entfernt werden soll. Soll beispielsweise im
// Copyright-Feld KEIN HTML-Code erlaubt werden, kommentiert die Zeile
// unten wieder ein. http://codex.wordpress.org/Function_Reference/wp_filter_nohtml_kses
function rrze_dlp_validate_options( $input ) {
	//$input['copyright'] = wp_filter_nohtml_kses( $input['copyright'] );
	return $input;
}
