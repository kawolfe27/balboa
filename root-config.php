<?php
// we simply define a constant to represent the full server path for where our home directory is located.
// we do this so we have an absolute value for where our files reside.  Makes including stuff more reliable and
// predictable. Most importantly - it makes it so our CRON jobs can find the files relative to the server and
// not just our web document root.
define( "APP_ROOT", __DIR__ .'/' );
