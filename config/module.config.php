<?php

$setting = array_merge_recursive(
include "controller.config.php",
include "plugins.config.php",
include "route.config.php",
include "services.config.php",
include "view-helper.config.php"
);

return $setting;
