<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_detail()
{
  global $conn, $ngenre;
  $tid = ii_get_num($_GET['id']);
  $tpage = ii_get_num($_GET['page']);
  global $ndatabase, $nidfield, $nfpre;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('hidden') . "=0 and $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.detail', 'tpl');
    mm_cntitle(ii_htmlencode($trs[ii_cfname('topic')]));
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = str_replace('{$genre}', $ngenre, $tmpstr);
    $tmpstr = str_replace('{$page}', $tpage, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
}

function jtbc_cms_module_index()
{
  $tmpstr = ii_ireplace('module.index', 'tpl');
  return $tmpstr;
}

function jtbc_cms_module()
{
  switch($_GET['type'])
  {
    case 'detail':
      return jtbc_cms_module_detail();
      break;
    case 'index':
      return jtbc_cms_module_index();
      break;
    default:
      return jtbc_cms_module_index();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
