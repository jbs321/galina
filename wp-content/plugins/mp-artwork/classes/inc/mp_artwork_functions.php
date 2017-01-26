<?php

/**
 * Get template part (for templates like the shop-loop).
 *
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function mp_artwork_plugin_get_template_part($slug, $name = '') {
    $template = '';
    // Look in yourtheme/slug-name.php and yourtheme/mp-artwork/slug-name.php
    if ($name) {
        $template = locate_template(array("{$slug}-{$name}.php", MP_Artwork_Plugin_Instance()->template_path() . "{$slug}-{$name}.php"));
    }

    // Get default slug-name.php
    if (!$template && $name && file_exists(MP_Artwork_Plugin_Instance()->plugin_path() . "/templates/{$slug}-{$name}.php")) {
        $template = MP_Artwork_Plugin_Instance()->plugin_path() . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/mp-artwork/slug.php
    if (!$template) {
        $template = locate_template(array("{$slug}.php", MP_Artwork_Plugin_Instance()->template_path() . "{$slug}.php"));
    }
    // Get default slug.php
    if (!$template && file_exists(MP_Artwork_Plugin_Instance()->plugin_path() . "/templates/{$slug}.php")) {
        $template = MP_Artwork_Plugin_Instance()->plugin_path() . "/templates/{$slug}.php";
    }
    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters('mp_artwork_plugin_get_template_part', $template, $slug, $name);

    if ($template) {
        load_template($template, false);
    }
}

/*
 * Post first embed media
 */

function mp_artwork_plugin_get_first_embed_media($post_id) {
    $post = get_post($post_id);
    $content = do_shortcode(apply_filters('the_content', $post->post_content));
    $embeds = get_media_embedded_in_content($content);
    if (!empty($embeds)) {
        return '<div class="entry-media">' . $embeds[0] . '</div>';
    } else {
        return false;
    }
}

/*
 * Post content
 */

function mp_artwork_plugin_get_content_theme($contentLength) {
?>
    <?php

    $content = apply_filters('the_content', strip_shortcodes(get_the_content()));
    $content = wp_strip_all_tags($content);
    $content = wp_kses($content, array());
    $content = preg_replace('/<(script|style)(.*?)>(.*?)<\/(script|style)>/is', '', $content);
    if (strlen($content) > $contentLength) {
        $content = extension_loaded('mbstring') ? mb_substr($content, 0, $contentLength) . '...' : substr($content, 0, $contentLength) . '...';
    }
    echo $content;
    ?>
    <?php

}

/*
 * Get post first link
 * 
 * @return string 
 */

function mp_artwork_plugin_get_first_link() {
    if (!preg_match('/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches)) {
        return false;
    }
    return esc_url_raw($matches[1]);
}

function mp_artwork_plugin_get_first_link_target() {
    if (!preg_match('/<a\s[^>]*?target=[\'"](.+?)[\'"]/is', get_the_content(), $matches)) {
        return '';
    }
    return $matches[1];
}

/**
 * Artwork post gallery.
 *
 * Show  post gallery.
 *
 * @since Artwork 1.0
 */
function mp_artwork_plugin_get_post_gallery($post, $mp_artwork_page_template) {
    MP_Artwork_Plugin_Instance()->galleryPost->get_post_gallery($post, $mp_artwork_page_template);
}
/*
     * Get post type slug
     * 
     * @return string 
     */

function mp_artwork_plugin_get_post_type_slug(){
    $post_type_slug = mp_artwork_plugin_posttype_name_sanitize_text(get_option(MP_Artwork_Plugin_Instance()->get_prefix() . 'post_type_slug' , 'works'));
	if ($post_type_slug) {
		return $post_type_slug;
	} else {
		if (has_post_format('work')) {
			return '';
		}
	}
}
 function mp_artwork_plugin_posttype_name_sanitize_text($txt) {
        $txt = strip_tags($txt, '');
        $txt = preg_replace("/[^a-zA-Z0-9-]+/", "", $txt);
        $txt = substr(strtolower($txt), 0, 19);
        return wp_kses_post(force_balance_tags($txt));
    }