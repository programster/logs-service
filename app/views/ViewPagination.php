
<?php

function render_list_items($max_results, $url_generator, $current_page)
{
    $max_page_count   = ceil($max_results / RESULTS_PER_PAGE);
    $show_left_arrow  = true;
    $show_right_arrow = true;

    // if the user has not specified the current page, then assume 1
    if (!isset($current_page)) {
        $current_page = 1;
    }

    // max number of pages to show
    $MAX_NUM_OPTIONS = 20;

    $min_page_index = $current_page - round($MAX_NUM_OPTIONS / 2);
    $max_page_index = $current_page + round($MAX_NUM_OPTIONS / 2);

    if ($min_page_index <= 1) {
        $min_page_index = 1;
    }

    if ($current_page == 1) {
        $show_left_arrow = false;
    }

    if ($current_page == $max_page_count) {
        $show_right_arrow = false;
    }

    if ($max_page_index < 10) {
        $max_page_index = 10;
    }

    if ($max_page_index > $max_page_count) {
        $max_page_index = $max_page_count;
    }
    
    if($current_page != 1) {
        print '<li><a href="' . $url_generator(1) . '" title="First">&laquo;</a></li>';
    }

    if ($show_left_arrow) {
        $link = $url_generator($current_page - 1);
        print '<li><a href="' . $link . '" title="Previous">&lsaquo;</a></li>';
    }


    for ($counter = $min_page_index; $counter <= $max_page_index; $counter++)
    {
        $class = "";

        if ($counter == $current_page) {
            $class = " class='active' ";
        }

        print '<li ' . $class . '><a href="' . $url_generator($counter) . '" title="Page ' . $counter . '">' . $counter . '</a></li>' . PHP_EOL;
    }

    if ($show_right_arrow) {
        $link = $url_generator($current_page + 1);
        print '<li><a href="' . $link . '" title="Next">&rsaquo;</a></li>' . PHP_EOL;
    }
    
    if($current_page != $max_page_count) {
        print '<li><a href="' . $url_generator($max_page_count) . '" title="Last">&raquo;</a></li>';
    }
}
?>

<!-- pagination selection here !-->
<div class="row">
    <div class="col-xs-12">
        <nav>
            <ul class="pagination">

                <?php
                if (!isset($maximum) || !isset($current_page)) {
                    throw new Exception("Pagination view requires the maximum page count and current_page to be set");
                }

                if (!isset($url_generator)) {
                    throw new Exception("The link generator must be set.");
                }

                if (!is_callable($url_generator)) {
                    throw new Exception("The link generator must be an inline function.");
                }

                render_list_items($maximum, $url_generator, $current_page);
                ?>

            </ul>
        </nav>
    </div>
</div>

