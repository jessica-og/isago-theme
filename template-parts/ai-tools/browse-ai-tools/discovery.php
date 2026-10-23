<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$selected_category =
    $args['selected_category']
    ?? '';

$search =
    $args['search']
    ?? '';

$platform_categories =
    $args['platform_categories']
    ?? [];


/*=========================================================
ALL URL
=========================================================*/

$all_url =
    get_permalink();


if (!empty($search)) {

    $all_url =
        add_query_arg(
            'platform_search',
            $search,
            $all_url
        );

}

?>


<!-- =================================================
DISCOVERY BAR
================================================= -->

<section
    class="ai-platform-discovery"
>

    <div class="container">


        <form
            method="get"
            class="ai-platform-discovery-form"
            data-ai-platform-search-form
        >


            <div
                class="ai-platform-search-box"
            >


                <i
                    class="fa-solid fa-magnifying-glass"
                    aria-hidden="true"
                ></i>


                <label
                    for="platform-search"
                    class="screen-reader-text"
                >

                    Search AI platforms

                </label>


                <input
                    type="search"
                    id="platform-search"
                    name="platform_search"
                    value="<?php echo esc_attr(
                        $search
                    ); ?>"
                    placeholder="Search AI platforms and models..."
                >


                <!-- =================================================
                CLEAR SEARCH
                ================================================= -->

                <a
                    href="<?php echo esc_url(
                        get_permalink()
                    ); ?>"
                    class="
                        ai-platform-search-clear
                        <?php echo empty($search)
                            ? 'is-hidden'
                            : ''; ?>
                    "
                    aria-label="Clear search"
                    data-ai-platform-clear
                    <?php echo empty($search)
                        ? 'aria-hidden="true"'
                        : ''; ?>
                >

                    <i
                        class="fa-solid fa-xmark"
                        aria-hidden="true"
                    ></i>

                </a>


                <button
                    type="submit"
                    class="ai-platform-search-button"
                >

                    Search

                </button>


            </div>


        </form>


        <!-- =================================================
        CATEGORY FILTERS
        ================================================= -->

        <div
            class="ai-platform-category-navigation"
        >


          

            <div
                class="ai-platform-category-pills"
            >


                <!-- =================================================
                ALL
                ================================================= -->

                <a
                    href="<?php echo esc_url(
                        $all_url
                    ); ?>"
                    class="
                        ai-platform-category-pill
                        <?php echo empty(
                            $selected_category
                        )
                            ? 'is-active'
                            : ''; ?>
                    "
                    data-ai-platform-category=""
                >

                    All

                </a>


                <?php foreach (
                    $platform_categories
                    as $slug => $label
                ) : ?>


                    <?php

                    $filter_args = [

                        'platform_category' =>
                            $slug

                    ];


                    if (!empty($search)) {

                        $filter_args[
                            'platform_search'
                        ] =
                            $search;

                    }


                    $filter_url =
                        add_query_arg(
                            $filter_args,
                            get_permalink()
                        );

                    ?>


                    <a
                        href="<?php echo esc_url(
                            $filter_url
                        ); ?>"
                        class="
                            ai-platform-category-pill
                            <?php echo (
                                $selected_category ===
                                $slug
                            )
                                ? 'is-active'
                                : ''; ?>
                        "
                        data-ai-platform-category="<?php echo esc_attr(
                            $slug
                        ); ?>"
                    >

                        <?php echo esc_html(
                            $label
                        ); ?>

                    </a>


                <?php endforeach; ?>


            </div>


        </div>


    </div>

</section>