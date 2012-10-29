<?php
require('../../common/incfiles/common.inc.php');
require('common/incfiles/config.inc.php');
require('common/incfiles/module_config.inc.php');
jtbc_cms_module_action();
$myhtml = jtbc_cms_module();
echo $myhtml;
?>
