<?php
global $mp_artwork_page_template;
$mp_artwork_work_bg = get_post_meta(get_the_ID(), '_work_bg', true);
if (empty($mp_artwork_work_bg)):
    $mp_artwork_work_bg = "work-wrapper-light";
endif;

$mp_artwork_work_bg_color = get_post_meta(get_the_ID(), '_work_bg_color', true);
$mp_artwork_work_bg_colorstyle = empty($mp_artwork_work_bg_color) ? '' : ' style=background-color:' . $mp_artwork_work_bg_color . ';';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class("page-wrapper"); ?><?php echo $mp_artwork_work_bg_colorstyle; ?>>
    <div class="work-wrapper <?php echo $mp_artwork_work_bg; ?> work-wrapper-default">
        <div class="gallery-wrapper">
            <div class="gallery-content">
                <div class="container">
                    <?php mp_artwork_plugin_get_post_gallery($post, $mp_artwork_page_template); ?>
                </div>
            </div>
        </div>
        <div class="page-content">
            <div class="entry-header">
                <h2 class="entry-title h4">
                    <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                </h2>
            </div> 
            <div class="entry entry-content">
                <p><?php mp_artwork_plugin_get_content_theme(163); ?></p>  
                <a href="<?php the_permalink(); ?>" class="button" title="<? echo esc_html(get_the_title()); ?>"><?php echo apply_filters('mp_artwork_frontpage_button_label', __('View', 'mp-artwork')); ?></a>
            </div>      
        </div> 
    </div>  
</article>
<?php
