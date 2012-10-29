<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_list()
{
  global $conn, $nlng, $ngenre;
  $tclassid = ii_get_num($_GET['classid']);
  $toffset = ii_get_num($_GET['offset']);
  global $nclstype, $nlisttopx, $npagesize;
  global $ndatabase, $nidfield, $nfpre;
  $tclassids = mm_get_sortids($ngenre, $nlng);
  $tmpstr = ii_itake('module.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('hidden') . "=0";
  if ($tclassid != 0)
  {
    if (ii_cinstr($tclassids, $tclassid, ','))
    {
      mm_cntitle(mm_get_sorttext($ngenre, $nlng, $tclassid));
      if ($nclstype == 0) $tsqlstr .= " and " . ii_cfname('class') . "=$tclassid";
      else $tsqlstr .= " and " . ii_cfname('cls') . " like '%|" . $tclassid . "|%'";
    }
  }
  else
  {
    if (!ii_isnull($tclassids)) $tsqlstr .= " and " . ii_cfname('class') . " in ($tclassids)";
  }
  $tsqlstr .= " order by " . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> listkey = $tclassid;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tmptstr = $tmpastr;
      foreach ($trs as $key => $val)
      {
        $tkey = ii_get_lrstr($key, '_', 'rightr');
        $GLOBALS['RS_' . $tkey] = $val;
        $tmptstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmptstr);
      }
      $tmptstr = str_replace('{$id}', $trs[$nidfield], $tmptstr);
      $tmptstr = ii_creplace($tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = str_replace('{$genre}', $ngenre, $tmpstr);
  $tmpstr = str_replace('{$classid}', $tclassid, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

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
    $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
    $tmprstr = '';
    $turls = $trs[ii_cfname('urls')];
    if (!ii_isnull($turls))
    {
      $turlsary = explode('{|||}', $turls);
      $turlscount = count($turlsary);
      for ($i = 1; $i <= $turlscount; $i ++)
      {
        $turlstr = $turlsary[$i - 1];
        if (!ii_isnull($turlstr))
        {
          $turlstrary = explode('{:::}', $turlstr);
          if (count($turlstrary) == 2)
          {
            $tmptstr = str_replace('{$downurl}', $turlstrary[0], $tmpastr);
            $tmptstr = str_replace('{$downhref}', $turlstrary[1], $tmptstr);
            $tmprstr .= $tmptstr;
          }
        }
      }
    }
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
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
  global $ngenre;
  $tmpstr = ii_ireplace('module.index', 'tpl');
  if (!ii_isnull($tmpstr)) return $tmpstr;
  else return jtbc_cms_module_list();
}

function jtbc_cms_module()
{
  switch($_GET['type'])
  {
    case 'list':
      return jtbc_cms_module_list();
      break;
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
