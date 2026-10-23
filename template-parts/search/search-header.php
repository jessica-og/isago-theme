<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO SEARCH HEADER
=========================================================*/

/*  
 * IMPORTANT:
 *
 * The search engine clears WordPress' native "s" value
 * so WordPress does not search post content.
 *
 * Therefore we read the original search keyword directly
 * from the URL instead of using get_search_query().
 */

$search_keyword = '';

if (isset($_GET['s'])) {

    $search_keyword = sanitize_text_field(
        wp_unslash($_GET['s'])
    );

}


/*
 * Search result count.
 */

global $wp_query;

$total = 0;

if (
    $wp_query
    &&
    isset($wp_query->found_posts)
) {

    $total = (int) $wp_query->found_posts;

}

?>

<header class="search-header">


    <div class="search-header-content">


        <h1>

            Search Results

        </h1>


        <p>

            <?php

            printf(

                'Showing %d result%s for "%s"',

                $total,

                $total === 1
                    ? ''
                    : 's',

                esc_html(
                    $search_keyword
                )

            );

            ?>

        </p>


    </div>


   <form
    class="isago-search-form search-page-form"

        method="get"

        action="<?php echo esc_url(
            home_url('/')
        ); ?>"
    >


       <input
    class="isago-search-input"
    type="search"
    name="s"
    value="<?php echo esc_attr($search_keyword); ?>"
    placeholder="Search articles, prompts, AI tools..."
    required
    autocomplete="off"
>

<div class="isago-search-results"></div>


        <button type="submit">

            Search

        </button>


    </form>

 <div class="isago-search-results"></div>
</header>