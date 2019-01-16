<?php


# Just call CI's index.php which kicks everything off.
# The only reason we need this is to shift the publicly accessible files down to one subfolder as suggested by
# http://stackoverflow.com/questions/6630770/where-do-i-put-image-files-css-js-etc-in-codeigniter
require (__DIR__ . '/../index.php');
