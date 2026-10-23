<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="isago-search-overlay">

    <div class="isago-search-box">
    <div class="isago-search-inner">
        <button
            class="isago-search-close"
            aria-label="Close Search">

            <i class="fa-solid fa-xmark"></i>

        </button>  

        <h2>

            Search Isago

        </h2>
<form class="isago-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">

    <div class="isago-search-field">
 
        <input
            class="isago-search-input"
            type="search"
            placeholder="Search articles, prompts, tools..."
            value="<?php echo get_search_query(); ?>"
            name="s"
            autocomplete="off"
        >

        <div class="isago-search-results"></div>

    </div>

    <button class="isago-search-submit">
        Search
    </button>

</form>
           <div class="isago-search-results"></div>
</div>
    </div>

</div>