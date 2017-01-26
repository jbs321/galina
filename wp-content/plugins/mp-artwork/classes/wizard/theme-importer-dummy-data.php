<?php

/*
 * Class 
 */

class MP_Artwork_Plugin_Importer_Dummy {

    /**
     * Add actions
     */
    public function __construct() {
        add_action('wp_ajax_start_import', array($this, 'import_dummy'));
        add_action('wp_ajax_nopriv_start_import', array($this, 'import_dummy'));
    }

    function import_dummy() {
        if (class_exists('WP_Import')) {
            $import = $GLOBALS['wp_import'];
            $import->fetch_attachments = true;
            $upload_dir = wp_upload_dir();

            if (is_writable($upload_dir['path'])) {                
                ob_start();
                $import->import($_POST['filename']);
                $text = ob_get_contents();
                ob_end_clean();
                
                $response = array(
                    'status' => 'success',
                    'text' => $text
                );
                die(json_encode($response));
            } else {
                $response = array(
                    'status' => 'error',
                    'text' => __('Wordpress upload dir permission denied. Please temporally set permissions "777" for "wp-content/upload/".', 'mp-artwork')
                );
                die(json_encode($response));
            }
        }
    }

}

new MP_Artwork_Plugin_Importer_Dummy();
