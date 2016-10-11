<?php
/**
 * Plugin Name:     EDD Manual Emails
 * Plugin URI:      https://wordpress.org/plugins/edd-manual-emails/
 * Description:     Send emails to anyone using your EDD email template
 * Version:         1.0.0
 * Author:          rubengc
 * Author URI:      http://rubengc.com
 * Text Domain:     edd-manual-emails
 *
 * @package         EDD\Manual_Emails
 * @author          rubengc
 * @copyright       Copyright (c) rubengc
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Manual_Emails' ) ) {

    /**
     * Main EDD_Manual_Emails class
     *
     * @since       1.0.0
     */
    class EDD_Manual_Emails {

        /**
         * @var         EDD_Manual_Emails $instance The one true EDD_Manual_Emails
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Manual_Emails
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Manual_Emails();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_MANUAL_EMAILS_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_MANUAL_EMAILS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_MANUAL_EMAILS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            require_once EDD_MANUAL_EMAILS_DIR . 'includes/ajax.php';
            require_once EDD_MANUAL_EMAILS_DIR . 'includes/scripts.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Register settings
            add_filter( 'edd_settings_sections_emails', array( $this, 'settings_section' ) );
            add_filter( 'edd_settings_emails', array( $this, 'settings' ), 1 );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_MANUAL_EMAILS_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_manual_emails_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-manual-emails' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-manual-emails', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-manual-emails/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-manual-emails/ folder
                load_textdomain( 'edd-manual-emails', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-manual-emails/languages/ folder
                load_textdomain( 'edd-manual-emails', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-manual-emails', false, $lang_dir );
            }
        }

        /**
         * Add settings section
         *
         * @access      public
         * @since       1.0.0
         * @param       array $sections The existing EDD settings sections array
         * @return      array The modified EDD settings sections array
         */
        public function settings_section( $sections ) {
            $sections['edd-manual-emails'] = __( 'EDD Manual Emails', 'edd-manual-emails' );

            return $sections;
        }

        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $edd_manual_emails_settings = array(
                array(
                    'id'    => 'edd_manual_emails_header',
                    'name'  => '<strong>' . __( 'EDD Manual Emails', 'edd-manual-emails' ) . '</strong>',
                    'desc'  => __( 'Send an email', 'edd-manual-emails' ),
                    'type'  => 'header',
                ),
                array(
                    'id'    => 'edd_manual_emails_subject',
                    'name'  => __( 'Subject', 'edd-manual-emails' ),
                    'desc'  => __( '', 'edd-manual-emails' ),
                    'type'  => 'text',
                ),
                array(
                    'id'    => 'edd_manual_emails_content',
                    'name'  => __( 'Content', 'edd-manual-emails' ),
                    'desc'  => __( '', 'edd-manual-emails' ),
                    'type'  => 'rich_editor',
                ),
                array(
                    'id'    => 'edd_manual_emails_to',
                    'name'  => __( 'To', 'edd-manual-emails' ),
                    'desc'  => __( 'Enter the email address(es) that should receive the email, one per line', 'edd-manual-emails' ),
                    'type'  => 'textarea',
                ),
            );

            if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
                $edd_manual_emails_settings = array( 'edd-manual-emails' => $edd_manual_emails_settings );
            }

            return array_merge( $settings, $edd_manual_emails_settings );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_Manual_Emails
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Manual_Emails The one true EDD_Manual_Emails
 */
function edd_manual_emails() {
    return EDD_Manual_Emails::instance();
}
add_action( 'plugins_loaded', 'edd_manual_emails' );
