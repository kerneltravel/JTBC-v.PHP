<?php
$nroute = 'child';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
jtbc_cms_admin_init();
$ndatabase = $variable['common.adminlog.ndatabase'];
$nidfield = $variable['common.adminlog.nidfield'];
$nfpre = $variable['common.adminlog.nfpre'];
$npagesize = $variable[ii_cvgenre($ngenre) . '.npagesize'];
?>
