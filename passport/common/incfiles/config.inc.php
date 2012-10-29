<?php
$nroute = 'node';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
$nhead = $variable[$ngenre . '.nhead'];
$nfoot = $variable[$ngenre . '.nfoot'];
if (ii_isnull($nhead)) $nhead = $default_head;
if (ii_isnull($nfoot)) $nfoot = $default_foot;
$ndatabase = $variable[$ngenre . '.ndatabase'];
$nidfield = $variable[$ngenre . '.nidfield'];
$nfpre = $variable[$ngenre . '.nfpre'];
$npagesize = $variable[$ngenre . '.npagesize'];
$nlisttopx = $variable[$ngenre . '.nlisttopx'];
$face_width_max = $variable[$ngenre . '.face_width_max'];
$face_height_max = $variable[$ngenre . '.face_height_max'];
$ntitle = ii_itake('module.channel_title', 'lng');
?>
