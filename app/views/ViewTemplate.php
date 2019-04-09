<?php

/* 
 * A template that just renders a body within a header/footer.
 */

class ViewTemplate extends AbstractView
{
    private $m_body;
    private $m_title;
    
    
    public function __construct(string $title, string $body)
    {
        $this->m_title = $title;
        $this->m_body = $body;
    }
    
    protected function renderContent() 
    {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link href="/libs/bootstrap-4.0.0-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/custom.css" rel="stylesheet">
        <script src="/js/jquery-3.3.1.min.js"></script>
        <script src="/libs/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

        <title><?= $this->m_title; ?></title>
    </head>
    <body>

        <div id="container" class="container">
            <div class="page-header">
                <h1><?= $this->m_title; ?></h1>
            </div>

            <div id="body">
                <?= $this->m_body; ?>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ajaxError(function () {
                window.location.reload();
            });
        </script>
    </body>
</html>
<?php
    }

}
