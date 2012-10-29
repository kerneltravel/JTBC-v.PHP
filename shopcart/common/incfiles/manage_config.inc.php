<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();
$nurltype = 0;
$nsearch = 'name,orderid,id';
$ncontrol = 'select,delete';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function pp_manage_navigation_status()
{
  return ii_ireplace('manage.navigation_status', 'tpl');
}

function jtbc_cms_admin_manage_editdisp()
{
  global $conn, $nshop, $ngenre;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tprolist = $trs[ii_cfname('fid')];
    $tstates = ii_get_num($trs[ii_cfname('state')], 0);
    $tstate = ii_get_num($_POST['state'], 0);
    $ttraffic = ii_get_num($_POST['traffic'], 0);
    $ttrafficprice = ii_itake('sel_traffic_fare.' . $ttraffic, 'sel');
    $tallprice = $tmerchandiseprice + $ttrafficprice;
    $tsqlstr = "update $ndatabase set
    " . ii_cfname('payment') . "=" . ii_get_num($_POST['payment']) . ",
    " . ii_cfname('traffic') . "=$ttraffic,
    " . ii_cfname('trafficprice') . "=$ttrafficprice,
    " . ii_cfname('allprice') . "=$tallprice,
    " . ii_cfname('state') . "=$tstate,
    " . ii_cfname('prepaid') . "=" . ii_get_num($_POST['prepaid']) . ",
    " . ii_cfname('dtime') . "='" . ii_now() . "'
    where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      if ($tstates != -1 && $tstate == -1)
      {
        $tdatabase = mm_cndatabase($nshop);
        $tidfield = mm_cnidfield($nshop);
        $tfpre = mm_cnfpre($nshop);
        if (!ii_isnull($tprolist))
        {
          $tary = explode(',', $tprolist);
          if (is_array($tary))
          {
            foreach ($tary as $key => $val)
            {
              $tid = ii_get_num(ii_get_lrstr($val, ':', 'left'), 0);
              if ($tid != 0)
              {
                $tsqlstr2 = "update $tdatabase set " . ii_cfnames($tfpre, 'limitnum') . "=" . ii_cfnames($tfpre, 'limitnum') . "+" . ii_get_num(ii_get_lrstr($val, ':', 'right'), 0) . " where $tidfield=$tid";
                @ii_conn_query($tsqlstr2, $conn);
              }
            }
          }
        }
      }
      jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  switch($_GET['action'])
  {
    case 'edit':
      jtbc_cms_admin_manage_editdisp();
      break;
    case 'delete':
      jtbc_cms_admin_deletedisp($ndatabase, $nidfield);
      break;
    case 'control':
      jtbc_cms_admin_controldisp($ndatabase, $nidfield, $nfpre, $ncontrol);
      break;
  }
}

function jtbc_cms_admin_manage_edit()
{
  global $conn, $nshop;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('manage.edit', 'tpl');
    $tprolist = $trs[ii_cfname('fid')];
    $tdatabase = mm_cndatabase($nshop);
    $tidfield = mm_cnidfield($nshop);
    $tfpre = mm_cnfpre($nshop);
    $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
    $tmprstr = '';
    $tmerchandiseprice = 0;
    if (!ii_isnull($tprolist))
    {
      $tary = explode(',', $tprolist);
      if (is_array($tary))
      {
        foreach ($tary as $key => $val)
        {
          $tid = ii_get_num(ii_get_lrstr($val, ':', 'left'), 0);
          if ($tid != 0)
          {
            $tsqlstr2 = "select * from $tdatabase where " . ii_cfnames($tfpre, 'hidden') . "=0 and $tidfield=$tid";
            $trs2 = ii_conn_query($tsqlstr2, $conn);
            $trs2 = ii_conn_fetch_array($trs2);
            if ($trs2)
            {
              $tnum = ii_get_num(ii_get_lrstr($val, ':', 'right'), 0);
              $tprice = ii_get_num($trs2[ii_cfnames($tfpre, 'price')], 0);
              $twprice = ii_get_num($trs2[ii_cfnames($tfpre, 'wprice')], 0);
              $tmerchandiseprice = $tmerchandiseprice + ($twprice * $tnum);
              $tmptstr = $tmpastr;
              $tmptstr = str_replace('{$id}', $trs2[$tidfield], $tmptstr);
              $tmptstr = str_replace('{$num}', $tnum, $tmptstr);
              $tmptstr = str_replace('{$price}', $tprice, $tmptstr);
              $tmptstr = str_replace('{$wprice}', $twprice, $tmptstr);
              $tmptstr = str_replace('{$topic}', ii_htmlencode($trs2[ii_cfnames($tfpre, 'topic')]), $tmptstr);
              $tmptstr = str_replace('{$limitnum}', ii_get_num($trs2[ii_cfnames($tfpre, 'limitnum')], 0), $tmptstr);
              $tmprstr .= $tmptstr;
            }
          }
        }
      }
    }
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
    $tmpstr = str_replace('{$tallprice}', $tmerchandiseprice, $tmpstr);
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else
  {
    mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
  }
}

function jtbc_cms_admin_manage_list()
{
  global $conn;
  global $ngenre, $npagesize, $nlisttopx;
  global $ndatabase, $nidfield, $nfpre;
  $toffset = ii_get_num($_GET['offset']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tary = ii_itake('sel_state.all', 'sel', 1);
  if (is_array($tary))
  {
    foreach ($tary as $key => $val)
    {
      $tstr0 = $key;
      $tstr1 = $val;
      if (!ii_isnull($tstr0))
      {
        $thspan = 'state' . $tstr0;
        $tmptstr = $tmpastr;
        $tmptstr = str_replace('{$topic}', $tstr1, $tmptstr);
        $tmptstr = str_replace('{$ahref}', '?keyword=' . $tstr0 . '&field=state&hspan=' . $thspan, $tmptstr);
        $tmptstr = str_replace('{$hspan}', $thspan, $tmptstr);
      }
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_idb}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where $nidfield>0";
  if ($search_field == 'name') $tsqlstr .= " and " . ii_cfname('name') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'orderid') $tsqlstr .= " and " . ii_cfname('orderid') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'state') $tsqlstr .= " and " . ii_cfname('state') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'prepaid') $tsqlstr .= " and " . ii_cfname('prepaid') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by $ndatabase." . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (!(ii_isnull($search_keyword)) && $search_field == 'name') $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tname = ii_htmlencode($trs[ii_cfname('name')]);
      if (isset($font_red))
      {
        $font_red = str_replace('{$explain}', $search_keyword, $font_red);
        $tname = str_replace($search_keyword, $font_red, $tname);
      }
      $tmptstr = str_replace('{$name}', $tname, $tmpastr);
      $tmptstr = str_replace('{$namestr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('name')])), $tmptstr);
      $tmptstr = str_replace('{$orderid}', ii_htmlencode($trs[ii_cfname('orderid')]), $tmptstr);
      $tmptstr = str_replace('{$paystate}', ii_itake('sel_paystate.' . ii_get_num($trs[ii_cfname('prepaid')]), 'lng'), $tmptstr);
      $tmptstr = str_replace('{$state}', ii_itake('sel_state.' . ii_get_num($trs[ii_cfname('state')]), 'lng'), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$nidfield]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage_status()
{
  global $conn, $ngenre;
  global $ndatabase, $nidfield, $nfpre;
  $search_state = ii_get_safecode($_GET['state']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tstatus = ii_get_safecode($_GET['status']);
  $tmpstr = ii_itake('manage.status', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tary = ii_itake('sel_state.all', 'sel', 1);
  if (is_array($tary))
  {
    foreach ($tary as $key => $val)
    {
      $tstr0 = $key;
      $tstr1 = $val;
      if (!ii_isnull($tstr0))
      {
        $thspan = 'state' . $tstr0;
        $tmptstr = $tmpastr;
        $tmptstr = str_replace('{$topic}', $tstr1, $tmptstr);
        $tmptstr = str_replace('{$ahref}', '?type=status&status=' . $tstatus . '&keyword=' . urlencode($search_keyword) . '&state=' . $tstr0 . '&hspan=' . $thspan, $tmptstr);
        $tmptstr = str_replace('{$hspan}', $thspan, $tmptstr);
      }
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_idb}');
  $tmprstr = '';
  unset($tary);
  for ($i = 1; $i <= 12; $i ++)
  {
    switch($tstatus)
    {
      case 'money':
        $tunit = ii_itake('global.lng_unit.money', 'lng');
        $tsqlstr = "select sum(" . ii_cfname('allprice') . ") from $ndatabase where month(" . ii_cfname('time') . ")=$i";
        break;
      default:
        $tunit = ii_itake('global.lng_unit.ge', 'lng');
        $tsqlstr = "select count($nidfield) from $ndatabase where month(" . ii_cfname('time') . ")=$i";
        break;
    }
    if (!ii_isnull($search_keyword)) $tsqlstr .= " and year(" . ii_cfname('time') . ")=" . ii_get_num($search_keyword);
    if (!ii_isnull($search_state)) $tsqlstr .= " and " . ii_cfname('state') . "=" . ii_get_num($search_state);
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs) $tary[$i] = ii_get_num($trs[0], 0);
  }
  $ttotalize = 0;
  if (is_array($tary))
  {
    $tmax = ii_get_arymax($tary);
    foreach ($tary as $key => $val)
    {
      $ttotalize += $val;
      $tcolor = '#00FF00';
      if ($tmax == $val) $tcolor = '#FF0000';
      $tmptstr = str_replace('{$month}', $key, $tmpastr);
      $tmptstr = str_replace('{$sum}', $val, $tmptstr);
      $tmptstr = str_replace('{$color}', $tcolor, $tmptstr);
      $tmptstr = str_replace('{$width}', ii_cper($val, $tmax), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tyear = '';
  if (!ii_isnull($search_keyword)) $tyear = ii_get_num($search_keyword);
  $tmpstr = str_replace('{$year}', $tyear, $tmpstr);
  $tmpstr = str_replace('{$totalize}', $ttotalize, $tmpstr);
  $tmpstr = str_replace('{$status}', $tstatus, $tmpstr);
  $tmpstr = str_replace('{$unit}', $tunit, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'edit':
      return jtbc_cms_admin_manage_edit();
      break;
    case 'list':
      return jtbc_cms_admin_manage_list();
      break;
    case 'status':
      return jtbc_cms_admin_manage_status();
      break;
    default:
      return jtbc_cms_admin_manage_list();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
