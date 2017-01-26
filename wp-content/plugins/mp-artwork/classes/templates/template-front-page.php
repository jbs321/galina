<?php

/**
 * Template Name: Front Page
 * The template file for front page.
 * @package Artwork
 * @since Artwork 
 */
get_header();

if (have_posts()) :
    /* The loop */
    while (have_posts()) : the_post();
        mp_artwork_plugin_get_template_part('custom-page');     
    endwhile;
endif;

get_footer();
