<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');
?>

    <div>
        <header class="entry-header has-text-align-center header-footer-group">
            <div class="entry-header-inner section-inner medium">
                <?php

                /**
                 * Allow child themes and plugins to filter the display of the categories in the article header.
                 *
                 * @param bool Whether to show the categories in article header, Default true.
                 *
                 * @since Twenty Twenty 1.0
                 *
                 */
                $show_categories = apply_filters('twentytwenty_show_categories_in_entry_header', true);

                if (true === $show_categories && has_category()) {
                    ?>

                    <div class="entry-categories">
                        <span class="screen-reader-text"><?php _e('Categories', 'twentytwenty'); ?></span>
                        <div class="entry-categories-inner">
                            <?php the_category(' '); ?>
                        </div>
                    </div>

                    <?php
                }

                the_title('<h1 class="entry-title logo_page"><img class="logo_image_title" src="../wp-content/uploads/logo.png" alt="">', '</h1>');
                ?>

            </div>
        </header>
    </div>

<?php
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');

while (have_posts()) {
    the_post();

    wc_get_template_part('content', 'single-product');
}

/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * woocommerce_sidebar hook.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');

get_custom_footer('with-buttons');
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
