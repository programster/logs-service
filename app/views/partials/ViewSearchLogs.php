<?php

class ViewSearchLogs extends AbstractView
{
    private $m_filter;
    
    
    public function __construct(LogFilter $filter)
    {
        $this->m_filter = $filter;
    }
    
    
    protected function renderContent() 
    {
?>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default collapsible">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-plus pull-right"></span>
                <h3 class="panel-title">
                    Filters
                </h3>
            </div>
            <div class="panel-body" style="display: none;">

                <form method="POST" action="/logs">

                    <div class="form-group">
                        <label>Search Message Text</label>
                        <input name="search_text" type="input" class="form-control" placeholder="Search Text..." />
                    </div>

                    <div class="form-group">
                        <label>Max Age</label>
                        <div class="row">
                            <div class="col-sm-8">
                                <input name="max_age_amount" type="input" placeholder="Max Age" class="form-control" />
                            </div>
                            <div class="col-sm-4">
                                <select name="max_age_units" class="form-control">
                                    <option>minutes</option>
                                    <option selected="selected">hours</option>
                                    <option>days</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Min Age</label>
                        <div class="row">
                            <div class="col-sm-8">
                                <input name="min_age_amount" type="input" placeholder="Min Age" class="form-control" />
                            </div>
                            <div class="col-sm-4">
                                <select name="min_age_units" class="form-control">
                                    <option>minutes</option>
                                    <option selected="selected">hours</option>
                                    <option>days</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alert Level</label>

                        <div class="btn-group btn-group-justified">


                            <?php
                            // render buttons for the user to click for changing which alert levels they want
                            // to view. Multiple buttons can be selected, rather than just one at a time.
                            $alert_levels = array(
                                "debug",
                                "info",
                                "notice",
                                "warning",
                                "error",
                                "critical",
                                "alert"
                            );

                            /* @var $this->m_filter LogFilter */
                            $enabledAlertLevels = $this->m_filter->getAlertLevels();

                            foreach ($alert_levels as $alert_level => $alert_level_name)
                            {
                                $active = false;
                                
                                if (in_array($alert_level, $enabledAlertLevels)) 
                                {
                                    $active = "active";
                                }

                                print
                                    '<div class="btn-group btn-group-sm">' .
                                    '<button ' .
                                    'id="btn_' . $alert_level_name . '" ' .
                                    'type="button" ' .
                                    'value="' . $alert_level_name . '" ' .
                                    'class="btn btn-default ' . $active . '"' .
                                    '>' .
                                    $alert_level_name .
                                    '</button>' .
                                    '</div>';
                            }
                            ?>
                        </div>
                    </div>



                    <?php
                    // Add a hidden input field for each alert level. This is what the form actually 
                    // submits
                    foreach ($alert_levels as $alert_level)
                    {
                        print
                            '<input ' .
                            'type="hidden" ' .
                            'id="level_' . $alert_level . '" ' .
                            'name="level_' . $alert_level . '" ' .
                            'value="false" ' .
                            '/>';
                    }
                    ?>

                    <!-- when one of the buttons is clicked, ensure that it stays as active, and update 
                         the corresponding hidden input field in the form for php to receive and handle -->
                    <script type="text/javascript">
                        // register a handler to trigger when a a level button is clicked
                        $('body').on('click', '.btn-group button', function (e) {
                            $(this).blur();
                            $(this).toggleClass('active');

                            //do any other button related things
                            var name = $(this).val();
                            var input_id = "level_" + name;

                            if ($(this).hasClass("active"))
                            {
                                $('#' + input_id).val('true');
                            } else
                            {
                                $('#' + input_id).val('false');
                            }
                        });

                        // Sync input values for the button levels when the document loads
                        var levels = [
                            "debug",
                            "info",
                            "notice",
                            "warning",
                            "error",
                            "critical",
                            "alert"
                        ];

                        for (level in levels)
                        {
                            var level_name = levels[level];
                            var btn_id = "btn_" + level_name;
                            var input_id = "level_" + level_name;

                            var button = $('#' + btn_id);
                            var input_element = $('#' + input_id);

                            if (button.hasClass("active"))
                            {
                                input_element.val('true');
                            } else
                            {
                                input_element.val('false');
                            }
                        }
                    </script>


                    <!-- consider using date range selection in future instead of age -->

                    <!-- this is just here so php know the filer form was submitted -->
                    <input type="hidden" name="filter_form" value="1">

                    <button type="submit"  class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $('.panel.collapsible .panel-heading').css('cursor', 'pointer');

        $('.panel.collapsible .panel-heading').click(function () {
            var panelHead = $(this);
            var panelBody = panelHead.siblings('.panel-body');
            var icon = panelHead.find('.glyphicon');

            panelBody.stop().slideToggle('fast');
            icon.toggleClass('glyphicon-plus glyphicon-minus');
        });

    });
</script>

<?php
    }
}