<?php
$nroute = 'node';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
$nhead = $variable[$ngenre . '.nhead'];
$nfoot = $variable[$ngenre . '.nfoot'];
if (ii_isnull($nhead)) $nhead = $default_head;
if (ii_isnull($nfoot)) $nfoot = $default_foot;
$npagesize = $variable[$ngenre . '.npagesize'];
$nlisttopx = $variable[$ngenre . '.nlisttopx'];
$nsearch_genre = $variable[$ngenre . '.nsearch_genre'];
$nsearch_field = $variable[$ngenre . '.nsearch_field'];
$ntitle = ii_itake('module.channel_title', 'lng');
?>
