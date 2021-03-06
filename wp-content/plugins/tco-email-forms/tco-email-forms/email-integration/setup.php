<?php

// =============================================================================
// EMAIL-INTEGRATION/SETUP.PHP
// -----------------------------------------------------------------------------
// Email integration framework.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   02. Setup Menu
//   03. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

define( 'TCO_EMAIL_INTEGRATION_IS_LOADED', true );
define( 'TCO_EMAIL_INTEGRATION_URL', plugins_url( '', __FILE__ ) );
define( 'TCO_EMAIL_INTEGRATION_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

GLOBAL $email_forms;



// Setup Menu
// =============================================================================

function email_forms_admin_menu() {
  GLOBAL $email_forms;
  add_options_page( __( 'Email Forms', '__tco__' ), __( 'Email Forms', '__tco__' ), 'manage_options', 'tco-extensions-email-forms', array( $email_forms, 'admin_controller' ) );
}

add_action( 'admin_menu', 'email_forms_admin_menu', 100 );



// Initialize
// =============================================================================

require( TCO_EMAIL_INTEGRATION_PATH . '/functions/framework/init.php' );
require( TCO_EMAIL_INTEGRATION_PATH . '/functions/plugin.php' );

$email_forms = new Tco_Email_Integration( __FILE__, 'email_forms' );
