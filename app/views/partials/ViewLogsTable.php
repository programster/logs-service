<?php

class ViewLogsTable extends AbstractView
{
    private $m_logs;
    
    
    public function __construct(LogCollection $logs)
    {
        $this->m_logs = $logs;
    }
    
    
    protected function renderContent() 
    {
?>

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
                        foreach ($this->m_logs as $log)
                        {
                            $logDateTime = new DateTime();
                            $logDateTime->setTimestamp($log->getWhen());
                            $timeDiff = Programster\CoreLibs\TimeLib::get_human_readble_time_difference($logDateTime);
                            
                            // whole row is clickable link to the log id.
                            // http://stackoverflow.com/questions/17147821/how-to-make-a-whole-row-in-a-table-clickable-as-a-link

                            /* @var $log Log */
                            print
                                "<tr class='clickableRow' onclick='window.location=\"/logs/{$log->get_id()}\"'>" .
                                    "<td>" . $log->get_id() . "</td>" .
                                    "<td>" . $log->getMessage() . "</td>" .
                                    "<td>" . $log->getPriority() . "</td>" .
                                    "<td style='white-space: nowrap'>" . $log->getHumanReadableTimestamp() . "</td>" .
                                    "<td style='white-space: nowrap'>" . $timeDiff . "</td>" .
                                "</tr>";
                        }
                        ?>

                        <?php if (!$this->m_logs) : ?>
                            <tr>
                                <td colspan="5">Sorry, no data to be listed</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>


<?php
    }
}