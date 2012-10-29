<?php
$nroute = 'child';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
jtbc_cms_admin_init();
$ndatabase = $sort_database;
$nidfield = $sort_idfield;
$nfpre = $sort_fpre;
$npagesize = $variable[ii_cvgenre($ngenre) . '.npagesize'];
?>
