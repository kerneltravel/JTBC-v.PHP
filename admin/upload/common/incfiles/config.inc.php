<?php
$nroute = 'child';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
jtbc_cms_admin_init();
$ndatabase = $variable['common.upload.ndatabase'];
$nidfield = $variable['common.upload.nidfield'];
$nfpre = $variable['common.upload.nfpre'];
$npagesize = $variable[ii_cvgenre($ngenre) . '.npagesize'];
?>
