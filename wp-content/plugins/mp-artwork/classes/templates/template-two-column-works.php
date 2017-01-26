<?php
/**
 * Template Name: Works Archive
 * The template file for works archive.
 * @package Artwork
 * @since Artwork 1.0
 */
get_header();
if (have_posts()) :
    /* The loop */ 
    while (have_posts()) : the_post();
        $mp_artwork_content = get_the_content();
        if (strlen($mp_artwork_content)): ?>
            <div class="container main-container works-archive">
                <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php if (has_post_thumbnail() && !post_password_required()) : ?>
                        <div class="entry-thumbnail">
                        <?php the_post_thumbnail(); ?>
                        </div>
                        <?php endif; ?>
                    <div class="entry-content">
                        <?php the_content(); ?>                    
                    </div> 
                </article> 
            </div>
            <?php
        endif;
    endwhile;
endif;

global $mp_artwork;
$mp_artwork_paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$mp_artwork_args = array(
    'post_type' => mp_artwork_plugin_get_post_type_slug(),
    'paged' => $mp_artwork_paged
);

$mp_artwork_works = new WP_Query($mp_artwork_args);

if ($mp_artwork_works->have_posts()) {
    ?>
    <div class="two-col-works">
        <?php
        while ($mp_artwork_works->have_posts()) {
            $mp_artwork_works->the_post();
            $mp_artwork_feat_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), mp_artwork_get_prefix() . 'thumb-large');
            $mp_artwork_width_img = $mp_artwork_feat_image_url[1];
            $mp_artwork_class_page = '';
            if ( $mp_artwork_width_img >= apply_filters('mp_artwork_worksarchive_image_max', 1200) ) {
                $mp_artwork_class_page = 'work-wrapper-cover';
            }
            $mp_artwork_work_bg = get_post_meta(get_the_ID(), '_work_bg', true);
            if (empty($mp_artwork_work_bg)):
                $mp_artwork_work_bg = "work-wrapper-light";
            endif;
			
			$mp_artwork_work_bg_color = get_post_meta(get_the_ID(), '_work_bg_color', true);
			$mp_artwork_work_bg_colorstyle = empty($mp_artwork_work_bg_color) ? '' : ' style=background-color:' . $mp_artwork_work_bg_color . ';';
			
			$mp_artwork_post_link = get_the_permalink();
			$mp_artwork_post_link_target = '';
			
			if ( get_post_format() == 'link' ) {
				$mp_artwork_link = mp_artwork_plugin_get_first_link();
				if ( !empty($mp_artwork_link) ) {
					$mp_artwork_post_link = $mp_artwork_link;
					$mp_artwork_link_target = mp_artwork_plugin_get_first_link_target();
					if ( !empty($mp_artwork_link_target) )
						$mp_artwork_post_link_target = ( $mp_artwork_link_target == '_blank') ? '_blank' : '_self';
				}
			}
			
            if ($mp_artwork_feat_image_url):
                ?>
                <a href="<?php echo $mp_artwork_post_link;  ?>" class="work-element" title="<? echo esc_html(get_the_title()); ?>" <?php echo !empty($mp_artwork_post_link_target) ? 'target="'.$mp_artwork_post_link_target.'"' : ''; ?><?php echo $mp_artwork_work_bg_colorstyle; ?>>
                    <div class="work-wrapper work-wrapper-bg <?php echo $mp_artwork_work_bg; ?> <?php echo $mp_artwork_class_page; ?>" style="background-image: url(<?php echo $mp_artwork_feat_image_url[0]; ?>)">
                    </div>
                <?php the_title('<div class="work-content"><div class="work-header"><h5>', '</h5></div></div>'); ?>  
                </a>
        <?php else: ?>
                <a href="<?php echo $mp_artwork_post_link; ?>" class="work-element default-elemet" title="<? echo esc_html(get_the_title()); ?>" <?php echo !empty($mp_artwork_post_link_target) ? 'target="'.$mp_artwork_post_link_target.'"' : ''; ?>>
                    <div class="work-wrapper <?php echo $mp_artwork_work_bg; ?> <?php echo $mp_artwork_class_page; ?>" >
                    </div>
                <?php the_title('<div class="work-content"><div class="work-header"><h5>', '</h5></div></div>'); ?>  
                </a>
            <?php
            endif;
        }
        ?>
    </div>
    <div class="clearfix"></div>			           
    <div class="hidden">
        <div class="older-works">
        <?php next_posts_link('&laquo; Older Entries', $mp_artwork_works->max_num_pages) ?>
        </div>
    <?php previous_posts_link('Newer Entries &raquo;') ?>
    </div>
    <?php
}
get_footer();
