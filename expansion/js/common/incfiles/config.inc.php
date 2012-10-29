<?php
$nroute = 'child';
$ngenre = ii_get_actual_genre(__FILE__, $nroute);
jtbc_cms_init($nroute);
$npagesize = ii_get_num($variable[ii_cvgenre($ngenre) . '.npagesize']);
$ndatabase = $variable[ii_cvgenre($ngenre) . '.ndatabase'];
$nidfield = $variable[ii_cvgenre($ngenre) . '.nidfield'];
$nfpre = $variable[ii_cvgenre($ngenre) . '.nfpre'];
$njspath = 'common/js/';

function pp_encode2js($strers)
{
  if (!ii_isnull($strers))
  {
    $tmpstr = '';
    $tstrers = ii_encode_newline($strers);
    $tary = explode(CRLF, $tstrers);
    foreach ($tary as $key => $val)
    {
      $tmpstr .= 'document.write("' . ii_encode_scripts($val) . '");' . CRLF;
    }
    return $tmpstr;
  }
}

function pp_get_retimetype($strers)
{
  $tstrers = ii_get_num($strers, 0);
  switch($tstrers)
  {
    case 0:
      return 'n';
      break;
    case 1:
      return 'h';
      break;
    case 2:
      return 'd';
      break;
    default:
      return 'd';
      break;
  }
}
?>
