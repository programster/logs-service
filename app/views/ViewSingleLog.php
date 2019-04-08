<?php
/* @var $log Log_model */
?>

<style type="text/css">
    dd {
        position: relative;
    }
    .log-container-resize {
        display: none;
        position: absolute;
        right: 0;
        top: 0;
        visibility: hidden;
    }
    dd:hover .log-container-resize {
        display: block;
    }
</style>

<dl class="dl-horizontal">

    <dt>ID</dt>
    <dd><?php echo $log->get_id() ?></dd>

    <dt>Message</dt>
    <dd>
        <button class="btn btn-default btn-xs log-container-resize">
            <span class="glyphicon glyphicon-resize-horizontal"></span>
        </button>
        <pre><?php echo $log->get_message() ?></pre>
    </dd>

    <dt>Priority</dt>
    <dd><?php echo $log->get_priority() ?></dd>

    <dt>When</dt>
    <dd><?php echo $log->getHumanReadableTimestamp() ?></dd>

    <dt>Context</dt>
    <dd>
        <button class="btn btn-default btn-xs log-container-resize">
            <span class="glyphicon glyphicon-resize-horizontal"></span>
        </button>
        <pre><?php echo htmlentities(print_r($log->get_context_object(), true)); ?></pre>
    </dd>

</dl>
<script type="text/javascript">
    $(document).ready(function () {
        var pre;
        setTimeout(function () {
            $('.log-container-resize').each(function () {
                pre = $(this).siblings('pre');
                pre.scrollLeft(9999);
                if(pre.scrollLeft() !== 0)
                {
                    $(this).css('visibility', 'visible');
                    pre.scrollLeft(0);
                }
            });
        }, 500);
        
        $('.log-container-resize').click(function () {
            pre = $(this).siblings('pre');
            $(this).blur();
            
            var css = pre.css('white-space');
            if(css === 'pre')
            {
                pre.css('white-space', 'pre-wrap');
            }
            if(css === 'pre-wrap')
            {
                pre.css('white-space', 'pre');
            }
        });
        
    });
</script>