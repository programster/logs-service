
<?php

class ViewPaginationLinks extends AbstractView
{
    private $m_currentPage;
    private $m_numResults;
    private $m_urlGenerator;
    private $m_maxNumberOfLinks;
    private $m_resultsPerPage;
    
    
    public function __construct( 
        int $currentPage, 
        int $numResults, 
        Closure $urlGenerator, 
        int $maxNumberOfLinks = 5,
        int $resultsPerPage = 20
    )
    {
        $this->m_currentPage = $currentPage;
        $this->m_numResults = $numResults;
        $this->m_urlGenerator = $urlGenerator;
        $this->m_maxNumberOfLinks = $maxNumberOfLinks;
        $this->m_resultsPerPage = $resultsPerPage;
    }
    
    
    function renderListItems($maxResults, $urlGenerator, $currentPage)
    {
        $maxPageCount = ceil($maxResults / RESULTS_PER_PAGE);
        $showLeftArrow = true;
        $showRightArrow = true;

        // if the user has not specified the current page, then assume 1
        if (!isset($currentPage)) 
        {
            $currentPage = 1;
        }

        // max number of pages to show
        $MAX_NUM_OPTIONS = 20;

        $min_page_index = $currentPage - round($MAX_NUM_OPTIONS / 2);
        $max_page_index = $currentPage + round($MAX_NUM_OPTIONS / 2);

        if ($min_page_index <= 1) 
        {
            $min_page_index = 1;
        }

        if ($currentPage == 1) 
        {
            $showLeftArrow = false;
        }

        if ($currentPage == $maxPageCount) 
        {
            $showRightArrow = false;
        }

        if ($max_page_index < 10) 
        {
            $max_page_index = 10;
        }

        if ($max_page_index > $maxPageCount) 
        {
            $max_page_index = $maxPageCount;
        }

        if ($currentPage != 1) 
        {
            print '<li><a href="' . $urlGenerator(1) . '" title="First">&laquo;</a></li>';
        }

        if ($showLeftArrow) 
        {
            $link = $urlGenerator($currentPage - 1);
            print '<li><a href="' . $link . '" title="Previous">&lsaquo;</a></li>';
        }


        for ($counter = $min_page_index; $counter <= $max_page_index; $counter++)
        {
            $class = "";

            if ($counter == $currentPage) 
            {
                $class = " class='active' ";
            }

            print '<li ' . $class . '><a href="' . $urlGenerator($counter) . '" title="Page ' . $counter . '">' . $counter . '</a></li>' . PHP_EOL;
        }

        if ($showRightArrow) 
        {
            $link = $urlGenerator($currentPage + 1);
            print '<li><a href="' . $link . '" title="Next">&rsaquo;</a></li>' . PHP_EOL;
        }

        if ($currentPage != $maxPageCount) 
        {
            print '<li><a href="' . $urlGenerator($maxPageCount) . '" title="Last">&raquo;</a></li>';
        }
    }
    
    
    protected function renderContent() 
    {
?>







<nav aria-label="...">
    <ul class="pagination">
        <?= renderListItems($this->m_numberOfResults, $this->m_urlGenerator, $this->m_currentPage); ?>
        <!-- shows previous or left arrow -->
        <a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a>
        
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active"><span class="page-link">2<span class="sr-only">(current)</span></span></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        
        <!-- shows next or right arrow -->
        <li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>
    </ul>
</nav>

<?php
    }
}