<style type="text/css">
    #logs-table {
        table-layout: fixed;
    }
    #logs-table td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        word-wrap: break-word;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Logs</h3>
            </div>
            <div class="panel-body">

                <table class="table" id="logs-table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">ID</th>
                            <th>Message</th>
                            <th style="width: 60px;">Priority</th>
                            <th style="width: 200px;">When (UTC)</th>
                            <th style="width: 150px;">Timediff</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach ($logs as $log)
                        {
                            $logDateTime = new DateTime($log->get_when());
                            $timeDiff    = \iRAP\CoreLibs\TimeLib::get_human_readble_time_difference($logDateTime);

                            // whole row is clickable link to the log id.
                            // http://stackoverflow.com/questions/17147821/how-to-make-a-whole-row-in-a-table-clickable-as-a-link

                            /* @var $log Log_model */
                            print
                                    "<tr class='clickableRow' href='/logs/id/" . $log->get_id() . "'>" .
                                    "<td>" . $log->get_id() . "</td>" .
                                    "<td>" . $log->get_message() . "</td>" .
                                    "<td>" . $log->get_priority() . "</td>" .
                                    "<td style='white-space: nowrap'>" . $log->getHumanReadableTimestamp() . "</td>" .
                                    "<td style='white-space: nowrap'>" . $timeDiff . "</td>" .
                                    "</tr>";
                        }
                        ?>

                        <?php if (!$logs) : ?>
                            <tr>
                                <td colspan="5">Sorry, no data to be listed</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <script type="text/javascript">
                        var loc = window.location;
                        if(loc.hash)
                        {
                            // if a hash is found, load that logs detail in modal
                            showModal(loc.protocol + '//' + loc.hostname + '/logs/id/' + loc.hash.substring(1));
                        }
                        /**
                         * Show modal with content fetched using ajax
                         * @param {string} url the url from where to fetch the data
                         * @returns {void} show modal
                         */
                        function showModal(url)
                        {
                            var current_url = loc.protocol + '//' + loc.hostname + loc.pathname;
                            window.location.hash = url.substring(url.lastIndexOf('/') + 1);
                            $.get(url, null, function (logDetails) {
                                var overlay = $('<div class="logs-overlay"></div>');
                                overlay
                                    .css('display', 'none')
                                    .click(function () {
                                        close();
                                    });

                                var panel = $('<div class="panel panel-default"></div>');
                                panel.click(function (e) {
                                    e.stopPropagation();
                                });

                                var content = $('<div class="panel-body"></div>');
                                content.append(logDetails);
                                content.appendTo(panel);

                                var footer = $('<div class="panel-footer text-right"></div>');

                                var closeButton = $('<button type="button" class="btn btn-default">Close</button>');
                                closeButton.click(function () {
                                    close();
                                });
                                closeButton.appendTo(footer);
                                footer.appendTo(panel);

                                panel.appendTo(overlay);

                                $('body').append(overlay);
                                $('body').css('overflow', 'hidden');
                                
                                overlay.fadeIn('fast', function () {
                                    panel.scrollTop(9999);
                                    if(panel.scrollTop() !== 0)
                                    {
                                        // there is a vertical scroll bar
                                        panel.scrollTop(0);
                                        var newHeight = panel.height() - footer.outerHeight();
                                        content
                                            .css({
                                                overflow: 'auto',
                                                height: newHeight + 'px'
                                            });
                                    }
                                });

                                var left = Math.round((($(window).width() - panel.outerWidth()) / (2 * $(window).width())) * 100);
                                var top = Math.round((($(window).height() - panel.outerHeight()) / (3 * $(window).height())) * 100);
                                panel.css({
                                    top: top + '%',
                                    left: left + '%'
                                });

                                var close = function () {
                                    window.history.replaceState(null, null, current_url);
                                    overlay.fadeOut('fast', function () {
                                        overlay.remove();
                                        $('body').css('overflow', 'auto');
                                        $(document).unbind('keydown');
                                    });
                                };
                                
                                $(document).keydown(function (e) {
                                    if(e.keyCode === 27) close();
                                });
                            });
                        }
                        jQuery(document).ready(function ($) {
                            $(".clickableRow").click(function () {
                                showModal($(this).attr("href"));
                            });
                        });
                    </script>
                </table>

            </div>
        </div>
    </div>

</div>