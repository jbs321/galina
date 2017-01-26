<?php

class MP_Artwork_Plugin_Widget_Registrator {

    protected $widgets = array(
        'widget/Items/About.php',
        'widget/Items/Contact.php',
        'widget/Items/Socials.php',
    );

    public function __construct() {

        // Allow child themes/plugins to add widgets to be loaded.
        $widgets = apply_filters('sp_widgets', $this->widgets);
        foreach ($widgets as $w) {
            include_once MP_ARTWORK_PLUGIN_CLASS_PATH  . $w;
        }
    }

}