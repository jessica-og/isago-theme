<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<header class="search-header">

 

    <div class="search-header-content">

        <h1>

            Search Results

        </h1>

        <p>

     <?php
        global $wp_query;

        $total = $wp_query->found_posts;

        printf(
            'Showing %d result%s for "%s"',
            $total,
            $total == 1 ? '' : 's',
            esc_html(get_search_query())
        );
        ?>
        </p>

    </div>

    <form

        class="search-page-form"

        method="get"

        action="<?php echo esc_url(home_url('/')); ?>">

        <input

            type="search"

            name="s"

            value="<?php echo esc_attr(get_search_query()); ?>"

            placeholder="Search articles, prompts, AI tools..."

            required>

        <button>

            Search

        </button>

    </form>

</header>