<?php

$mp_artwork_work_bg = get_post_meta(get_the_ID(), '_work_bg', true);
if (empty($mp_artwork_work_bg)):
    $mp_artwork_work_bg = "work-wrapper-light";
endif;

$mp_artwork_work_bg_color = get_post_meta(get_the_ID(), '_work_bg_color', true);
$mp_artwork_work_bg_colorstyle = empty($mp_artwork_work_bg_color) ? '' : ' style=background-color:' . $mp_artwork_work_bg_color . ';';

$media = mp_artwork_plugin_get_first_embed_media($post->ID);
if ($media === false) :
    $mp_artwork_feat_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),
		apply_filters('mp_artwork_frontpage_image_size', mp_artwork_get_prefix() . 'thumb-large'));
    $mp_artwork_width_img = $mp_artwork_feat_image_url[1];
    $mp_artwork_class_page = '';
    if ( $mp_artwork_width_img >= apply_filters('mp_artwork_frontpage_image_max', 1200) ) {
        $mp_artwork_class_page = 'work-wrapper-cover';
    }
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class("page-wrapper"); ?><?php echo $mp_artwork_work_bg_colorstyle; ?>>
        <div class="work-wrapper <?php echo $mp_artwork_work_bg; ?> <?php echo $mp_artwork_class_page; ?> <?php
        if (!$mp_artwork_feat_image_url): echo 'work-wrapper-default';
        else: echo 'work-wrapper-bg';
        endif;
        ?> " <?php if ($mp_artwork_feat_image_url): ?> style="background-image: url(<?php echo $mp_artwork_feat_image_url[0]; ?>)" <?php endif; ?>>
         <?php else: ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class("page-wrapper"); ?><?php echo $mp_artwork_work_bg_colorstyle; ?>>
                <div class="work-wrapper <?php echo $mp_artwork_work_bg; ?>  work-wrapper-default">     
                <?php endif; ?>
                <div class="page-content">
                    <div class="entry-header">
                        <h2 class="entry-title h4">
                            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                        </h2>
                    </div> 
                    <div class="entry entry-content">
                        <p><?php mp_artwork_plugin_get_content_theme(163, false); ?></p>  
                        <a href="<?php the_permalink(); ?>" class="button" title="<? echo esc_html(get_the_title()); ?>"><?php echo apply_filters('mp_artwork_frontpage_button_label', __('View', 'mp-artwork')); ?></a>
                    </div>      
                </div>     

                <?php if ($media != false) : echo $media;
                endif; ?>
            </div>  
        </article>
<?php
