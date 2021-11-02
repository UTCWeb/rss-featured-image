<?php
/*
 * Plugin Name:   RSS Feed Featured Image
 * Plugin URI:    http://www.utc.edu/
 * Description:   A plugin to customize the default RSS Feed to add media enclosures for featured image
 * Version:	  1.3.2
 * Author:        Chris Gilligan
 * Author URI:    https://chrisgilligan.com/
 */
?>
<?php
// add namespaces
add_filter( 'rss2_ns', 'rssimproved_namespace', 1000, 1 );

function rssimproved_namespace() {
    echo 'xmlns:media="http://search.yahoo.com/mrss/"';
}
// strip down the content of the feed
add_filter( 'the_content_feed', 'rssimproved_content', 1000, 1 );

function rssimproved_content() {
    return $excerpt;
}
// include media content and featured image in RSS
add_filter( 'rss2_item', 'rssimproved_attached_images' );

function rssimproved_attached_images() {
    global $post;

    // if no in-post attachments, but post has a featured image
    if ( get_the_post_thumbnail() ): ?>

            <media:content url="<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large'); echo $image[0]; ?>" />

    <?php endif;

    // define attachments
    $attachments = get_posts( array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'post_parent' => $post->ID
    ) );

    // if post has attachments, construct and output RSS media nodes;
    if ( $attachments ) {
        foreach ( $attachments as $att ) {
            $img_attr = wp_get_attachment_image_src( $att->ID, 'full' );
            ?>

            <media:content url="<?php echo $img_attr[0]; ?>" type="<?php echo $att->post_mime_type; ?>" medium="image" width="<?php echo $img_attr[1]; ?>" height="<?php echo $img_attr[2]; ?>">
                <media:description type="plain"><![CDATA[<?php echo $att->post_title; ?>]]></media:description>
            </media:content>

            <?php
        }
    }

    // if post has no attachments and no featured image: set a static placeholder images
    if ( !get_the_post_thumbnail() ): ?>

        <media:content url="https://www.utc.edu/sites/default/files/static-assets/logos/utc-power-c-for-dark-bg-with-padding-20210325a.svg">
            <media:description type="plain"><![CDATA[<?php echo $att->post_title; ?>]]></media:description>
        </media:content>

    <?php endif;
}
