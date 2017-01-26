<?php

/*
 * Class MP_Profit_Plugin_Customizer
 *
 * add actions for default widgets if footer
 */

class MP_Artwork_Plugin_Customizer {

    private $prefix;

    public function __construct($prefix) {
        $this->prefix = $prefix;
        //Handles the theme's theme customizer functionality.
        add_action('customize_register', array($this, 'customize_register'));
    }

    /**
     * Get prefix.
     *
     * @access public
     * @return sting
     */
    private function get_prefix() {
        return $this->prefix . '_';
    }

    /**
     * Sets up the theme customizer sections, controls, and settings.
     *
     * @access public
     * @param  object  $wp_customize
     * @return void
     */
    public function customize_register($wp_customize) {
        $list_fonts = array(); // 1
        $list_font_weights = array(); // 2
        $webfonts_array = file(MP_ARTWORK_PLUGIN_PATH . '/assets/fonts.json');
        $webfonts = implode('', $webfonts_array);
        $list_fonts_decode = json_decode($webfonts, true);

        foreach ($list_fonts_decode['items'] as $key => $value) {
            $item_family = $list_fonts_decode['items'][$key]['family'];
            $list_fonts[$item_family] = $item_family;
            $list_font_weights[$item_family] = $list_fonts_decode['items'][$key]['variants'];
        }

        $list_all_font_weights = $this->get_list_all_font_weights();
        $list_all_font_size = $this->get_list_all_font_size();
        $wp_customize->add_setting($this->get_prefix() . 'display_logo', array(
            'default' => 1,
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
        ));

        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize, $this->get_prefix() . 'display_logo', array(
                'label' => esc_html__('Display logo on front page', 'mp-artwork'),
                'section' => 'title_tagline',
                'settings' => $this->get_prefix() . 'display_logo',
                'type' => 'checkbox',
                'priority' => 14,
            ))
        );
        $wp_customize->add_setting($this->get_prefix() . 'logo_border', array(
            'default' => 1,
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
        ));

        $wp_customize->add_control(
                new WP_Customize_Control(
                $wp_customize, $this->get_prefix() . 'logo_border', array(
            'label' => esc_html__('Display border around title on Front Page', 'mp-artwork'),
            'section' => 'title_tagline',
            'settings' => $this->get_prefix() . 'logo_border',
            'type' => 'checkbox',
            'priority' => 15,
                ))
        );

        $wp_customize->add_section(
                $this->get_prefix() . 'font_section', array(
            'title' => esc_html__('Fonts', 'mp-artwork'),
            'priority' => 40,
            'capability' => 'edit_theme_options'
                )
        );
        $wp_customize->add_setting($this->get_prefix() . 'title_font_family', array(
            'default' => 'Niconne',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));
        $wp_customize->add_control($this->get_prefix() . 'title_font_family', array(
            'type' => 'select',
            'label' => __('Site Title Font Family', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 11,
            'choices' => $list_fonts
        ));
        $wp_customize->add_setting($this->get_prefix() . 'title_font_weight', array(
            'default' => '700',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));

        $wp_customize->add_control($this->get_prefix() . 'title_font_weight', array(
            'type' => 'select',
            'label' => __('Site Title Font Weight', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 12,
            'choices' => $list_all_font_weights
        ));
        $wp_customize->add_setting($this->get_prefix() . 'title_font_size', array(
            'default' => '5.625em',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));

        $wp_customize->add_control($this->get_prefix() . 'title_font_size', array(
            'type' => 'select',
            'label' => __('Site Title Font Size', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 13,
            'choices' => $list_all_font_size
        ));
        $wp_customize->add_setting($this->get_prefix() . 'tagline_font', array(
            'default' => 0,
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
        ));

        $wp_customize->add_control(
                new WP_Customize_Control(
                $wp_customize, $this->get_prefix() . 'tagline_font', array(
            'label' => esc_html__('Make Tagline font family same as Title', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'settings' => $this->get_prefix() . 'tagline_font',
            'type' => 'checkbox',
            'priority' => 14,
                ))
        );
        /*
         *
         */
        $wp_customize->add_setting($this->get_prefix() . 'text_font_family', array(
            'default' => 'Josefin Sans',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));

        $wp_customize->add_control($this->get_prefix() . 'text_font_family', array(
            'type' => 'select',
            'label' => __('Site Font Family', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 14,
            'choices' => $list_fonts
        ));
        $wp_customize->add_setting($this->get_prefix() . 'text_font_weight', array(
            'default' => '400',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));

        $wp_customize->add_control($this->get_prefix() . 'text_font_weight', array(
            'type' => 'select',
            'label' => __('Site Font Weight', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 15,
            'choices' => $list_all_font_weights
        ));
        $wp_customize->add_setting($this->get_prefix() . 'text_font_size', array(
            'default' => '1.000em',
            'sanitize_callback' => array($this, 'sanitize_select'),
        ));

        $wp_customize->add_control($this->get_prefix() . 'text_font_size', array(
            'type' => 'select',
            'label' => __('Site Font Size', 'mp-artwork'),
            'section' => $this->get_prefix() . 'font_section',
            'priority' => 16,
            'choices' => $list_all_font_size
        ));
        /*
         * Add 'about' section
         */
        $wp_customize->add_section(
                $this->get_prefix() . 'about_section', array(
            'title' => esc_html__('About Page', 'mp-artwork'),
            'priority' => 70,
            'capability' => 'edit_theme_options'
                )
        );
        /*
         * Add the 'phone info' setting.
         */
        $wp_customize->add_setting($this->get_prefix() . 'about_content', array(
            'sanitize_callback' => array($this, 'sanitize_text_all'),
            'default' => '',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage'
        ));
        /*
         * Add the control for the 'phone info' setting.
         */
        $wp_customize->add_control( $this->get_prefix() . 'about_content', array(
            'label' => __('Description', 'mp-artwork'),
            'section' => $this->get_prefix() . 'about_section',
            'settings' => $this->get_prefix() . 'about_content',
            'type'=>'textarea'
        ));
        if (get_theme_mod($this->get_prefix() . 'about_content', false) === false) :
            set_theme_mod($this->get_prefix() . 'about_content', (__('<h1>Hello!</h1><p>Artistique is a fine art gallery located in 24 Hillside Gardens Northwood, Greater London UK. The gallery was established in 1993 and is dedicated to the exhibition, education and research of contemporary figurative art. Our aim is to contribute, initiate, develop and inspire viewers about the visual arts.</p><p>Artistique Artwork Gallery is always open for submissions from all creative artists. Get your opportunity to exhibit your works at a prestigious gallery and have them seen by thousands of visitors who appreciate the art.</p><h5>OPENING HOURS</h5> <p class="color-black">10.30am to 5.30pm, Tuesday to Sunday during exhibitions.<br/>Closed on Mondays (except bank holidays) and during exhibition changeovers.</p><h5>DIRECTIONS</h5><p>via subway take 282/H11 train towards Harrow Bus Station (Stop D), walk about 2 min, head southeast on Green Ln/A4125, at the roundabout, take the 2nd exit onto Northwood Way, turn right to stay on Northwood Way, turn left onto Hillside Rise, Continue onto Hillside Gardens. Artistique Artwork Gallery will be on the left.</p>', 'mp-artwork') . '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2477.886123831363!2d-0.40599407821061495!3d51.606975100903576!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48766b6644faeab9%3A0x43cc932445a52c2a!2s24+Hillside+Gardens%2C+Northwood%2C+Greater+London+HA6+1RL%2C+UK!5e0!3m2!1sen!2sua!4v1448962013715" width="600" height="217" frameborder="0" style="border:0" allowfullscreen></iframe>'));
        endif;
        /*
         * Add the 'logo footer' upload setting.
         */
        $wp_customize->add_setting(
                $this->get_prefix() . 'about_img', array(
            'default' => MP_ARTWORK_PLUGIN_PATH . '/images/about_img.png',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw',
                )
        );
        /*
         * Add the upload control for the$this->get_prefix().'logo' setting.
         */
        $wp_customize->add_control(
                new WP_Customize_Image_Control(
                $wp_customize, $this->get_prefix() . 'about_img', array(
            'label' => esc_html__('Background image', 'mp-artwork'),
            'section' => $this->get_prefix() . 'about_section',
            'settings' => $this->get_prefix() . 'about_img',
                )
                )
        );
    }

    /*
     * Sanitize function for select
     */

    function sanitize_select($input, $setting) {
        global $wp_customize;

        $control = $wp_customize->get_control($setting->id);

        if (array_key_exists($input, $control->choices)) {
            return $input;
        } else {
            return $setting->default;
        }
    }

    /*
     * Sanitize function for all content
     */

    function sanitize_text_all($txt) {
        return force_balance_tags($txt);
    }

    /*
     * Get array of all font weights
     */

    function get_list_all_font_weights() {
        return array(
            '100' => __('Ultra Light', 'mp-artwork'),
            '100italic' => __('Ultra Light Italic', 'mp-artwork'),
            '200' => __('Light', 'mp-artwork'),
            '200italic' => __('Light Italic', 'mp-artwork'),
            '300' => __('Book', 'mp-artwork'),
            '300italic' => __('Book Italic', 'mp-artwork'),
            '400' => __('Regular', 'mp-artwork'),
            '400italic' => __('Regular Italic', 'mp-artwork'),
            '500' => __('Medium', 'mp-artwork'),
            '500italic' => __('Medium Italic', 'mp-artwork'),
            '600' => __('Semi-Bold', 'mp-artwork'),
            '600italic' => __('Semi-Bold Italic', 'mp-artwork'),
            '700' => __('Bold', 'mp-artwork'),
            '700italic' => __('Bold Italic', 'mp-artwork'),
            '800' => __('Extra Bold', 'mp-artwork'),
            '800italic' => __('Extra Bold Italic', 'mp-artwork'),
            '900' => __('Ultra Bold', 'mp-artwork'),
            '900italic' => __('Ultra Bold Italic', 'mp-artwork')
        );
    }

    /*
     * Get array of all font size
     */

    function get_list_all_font_size() {
        return array(
            '0.875em' => '14px',
            '0.938em' => '15px',
            '1.000em' => '16px',
            '1.125em' => '18px',
            '1.250em' => '20px',
            '1.375em' => '22px',
            '1.500em' => '24px',
            '1.625em' => '26px',
            '1.750em' => '28px',
            '1.875em' => '30px',
            '2.000em' => '32px',
            '2.125em' => '34px',
            '2.250em' => '36px',
            '2.375em' => '38px',
            '2.500em' => '40px',
            '2.625em' => '42px',
            '2.750em' => '44px',
            '3.000em' => '48px',
            '3.125em' => '50px',
            '3.250em' => '52px',
            '3.375em' => '54px',
            '3.500em' => '56px',
            '3.625em' => '58px',
            '3.750em' => '60px',
            '3.875em' => '62px',
            '4.000em' => '64px',
            '4.125em' => '66px',
            '4.250em' => '68px',
            '4.375em' => '70px',
            '4.500em' => '72px',
            '4.625em' => '74px',
            '4.750em' => '76px',
            '4.875em' => '78px',
            '5.000em' => '80px',
            '5.125em' => '82px',
            '5.250em' => '84px',
            '5.375em' => '86px',
            '5.500em' => '88px',
            '5.625em' => '90px',
            '5.750em' => '92px',
            '5.875em' => '94px',
            '6.000em' => '96px',
            '6.125em' => '98px'
        );
    }

    /**
     * Sanitize checkbox
     *
     * @access public
     * @return sanitized output
     */
    function sanitize_checkbox($input) {
        if ($input == 1) {
            return 1;
        } else {
            return 0;
        }
    }

}
