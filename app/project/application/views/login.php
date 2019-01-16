
<p>To be able to view the logs, you need to login.</p>

<style type="text/css">
    input {
        position: relative;
        top: -1px;
    }
    input:first-child {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        top: 1px;
    }
    input:last-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    input:focus {
        z-index: 5;
    }
</style>
<div class="row">
    <div class="col-lg-4 col-md-5 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Login Form</h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="/login">
                    <div class="form-group">
                        <input name="email" type="email" placeholder="Email" class="form-control" value="<?= $_POST['email'] ?? '' ?>" />
                        <input name="password" type="password" placeholder="Password" class="form-control" />
                    </div>
                    <button type="submit" class="btn btn-default">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('input:first-child').focus();
        $('form').submit(function () {
            $.post(
                    '<?= iRAP\CoreLibs\Core::getCurrentUrl() ?>',
                    $(this).serialize(),
                    function (data) {
                        if (data.result === 'success')
                            window.location.reload();
                        else if ($('#error').length) {
                            $('#error').text(data.message);
                        } else
                        {
                            $('.panel-body').prepend('<p class="text-danger" id="error"></p>');
                            $('#error').text(data.message);
                        }
                    }, 'json');
            return false;
        });
    });
</script>