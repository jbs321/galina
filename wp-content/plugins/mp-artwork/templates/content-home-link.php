<?php
$mp_artwork_feat_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),
	apply_filters('mp_artwork_frontpage_image_size', mp_artwork_get_prefix() . 'thumb-large'));
$mp_artwork_width_img = $mp_artwork_feat_image_url[1];
$mp_artwork_class_page = '';
if ( $mp_artwork_width_img >= apply_filters('mp_artwork_frontpage_image_max', 1200) ) {
    $mp_artwork_class_page = 'work-wrapper-cover';
}
$mp_artwork_work_bg = get_post_meta(get_the_ID(), '_work_bg', true);
if (empty($mp_artwork_work_bg)):
    $mp_artwork_work_bg = "work-wrapper-light";
endif;

$mp_artwork_work_bg_color = get_post_meta(get_the_ID(), '_work_bg_color', true);
$mp_artwork_work_bg_colorstyle = empty($mp_artwork_work_bg_color) ? '' : ' style=background-color:' . $mp_artwork_work_bg_color . ';';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class("page-wrapper"); ?><?php echo $mp_artwork_work_bg_colorstyle; ?>>
    <div class="work-wrapper <?php echo $mp_artwork_work_bg; ?> <?php echo $mp_artwork_class_page; ?> <?php
    if (!$mp_artwork_feat_image_url): echo 'work-wrapper-default';
    else: echo 'work-wrapper-bg';
    endif;
    ?> " <?php if ($mp_artwork_feat_image_url): ?> style="background-image: url(<?php echo $mp_artwork_feat_image_url[0]; ?>)" <?php endif; ?>>
        <div class="page-content">
            <div class="entry-header">
                <h2 class="entry-title h4">
                    <?php the_title(); ?>
                </h2>
            </div> 
            <div class="entry entry-content">
                <?php
                $mp_artwork_link = mp_artwork_plugin_get_first_link();
                if ($mp_artwork_link) {
					$mp_artwork_link_target = mp_artwork_plugin_get_first_link_target();
					if ( !empty($mp_artwork_link_target) )
						$mp_artwork_post_link_target = ( $mp_artwork_link_target == '_blank') ? '_blank' : '_self';
                    ?>
                    <a href="<?php echo $mp_artwork_link; ?>" class="button" title="<? echo esc_html(get_the_title()); ?>" <?php echo !empty($mp_artwork_post_link_target) ? 'target="'.$mp_artwork_post_link_target.'"' : ''; ?>><?php echo apply_filters('mp_artwork_frontpage_button_label', __('View', 'mp-artwork')); ?></a>
                    <?php
                } else {
                    ?>
                    <a href="<?php the_permalink(); ?>" class="button"><?php echo apply_filters('mp_artwork_frontpage_button_label', __('View', 'mp-artwork')); ?></a>
                    <?php
                }
                ?>
            </div>      
        </div> 
    </div>  
</article>
<?php
