<?php
/**
 * Ajax
 *
 * @package     EDD\Manual_Emails\Ajax
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_edd_manual_emails_send', 'edd_manual_emails_send_ajax' );
function edd_manual_emails_send_ajax() {
    $response = array();

    $from_name = edd_get_option( 'from_name', get_bloginfo( 'name' ) );
    $from_email = edd_get_option( 'from_email', get_option( 'admin_email' ) );
    $subject = $_REQUEST['subject'];
    $to = $_REQUEST['to'];
    $content = $_REQUEST['content'];

    // This fix a replacement of " to \", needs a better fix
    $content = str_replace('\"', '"', $content);

    foreach($to as $to_email) {
        $emails = new EDD_Emails;

        $emails->from_name    = $from_name;
        $emails->from_address = $from_email;
        $emails->heading      = $subject;

        $response[$to_email] = $emails->send( $to_email, $subject, $content );
    }

    wp_send_json( $response );
}