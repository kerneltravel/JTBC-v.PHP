<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_adddisp()
{
  global $ngenre;
  $tbackurl = $_GET['backurl'];
  $tid = ii_get_num($_GET['id'], 0);
  $tbuynum = ii_get_num($_GET['buynum'], 1);
  if ($tbuynum < 1) $tbuynum = 1;
  if ($tid != 0) setcookie(APP_NAME . $ngenre . '[' . $tid . ']', $tbuynum, 0, COOKIES_PATH);
  mm_client_redirect('./?type=list&backurl' . urlencode($tbackurl));
}

function jtbc_cms_module_editdisp()
{
  global $ngenre;
  $tsid = $_POST['sel_ids'];
  $tcookiesAry = $_COOKIE[APP_NAME . $ngenre];
  if (is_array($tcookiesAry))
  {
    foreach ($tcookiesAry as $key => $val)
    {
      setcookie(APP_NAME . $ngenre . '[' . $key . ']', 0, time() - 3600, COOKIES_PATH);
    }
  }
  if (ii_cidary($tsid))
  {
    $tary = explode(',', $tsid);
    foreach ($tary as $key => $val)
    {
      $tnum = ii_get_num($_POST['num_' . $val], 0);
      if ($tnum != 0) setcookie(APP_NAME . $ngenre . '[' . $val . ']', $tnum, 0, COOKIES_PATH);
    }
  }
  mm_client_redirect('./?type=list');
}

function jtbc_cms_module_deletedisp()
{
  global $ngenre;
  $tcookiesAry = $_COOKIE[APP_NAME . $ngenre];
  if (is_array($tcookiesAry))
  {
    foreach ($tcookiesAry as $key => $val)
    {
      setcookie(APP_NAME . $ngenre . '[' . $key . ']', 0, time() - 3600, COOKIES_PATH);
    }
  }
  mm_client_redirect('./?type=list');
}

function jtbc_cms_module_buydisp()
{
  global $ctype, $Err;
  $ctype = 'list';
  global $nusername;
  global $conn, $nshop, $ngenre;
  global $ndatabase, $nidfield, $nfpre;
  $tdatabase = mm_cndatabase($nshop);
  $tidfield = mm_cnidfield($nshop);
  $tfpre = mm_cnfpre($nshop);
  $titems = '';
  $tcookiesAry = $_COOKIE[APP_NAME . $ngenre];
  if (is_array($tcookiesAry))
  {
    foreach ($tcookiesAry as $key => $val)
    {
      $tid = ii_get_num($key, 0);
      $tnum = ii_get_num($val, 0);
      if ($tid != 0 && $tnum != 0)
      {
        $titems .= $tid . ':' . $tnum . ',';
        $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'hidden') . "=0 and $tidfield=$tid";
        $trs = ii_conn_query($tsqlstr, $conn);
        $trs = ii_conn_fetch_array($trs);
        if ($trs)
        {
          if (ii_get_num($trs[ii_cfnames($tfpre, 'limitnum')], 0) < $tnum) $Err[count($Err)] = str_replace('[]', '[' . ii_htmlencode(ii_cfnames($tfpre, 'topic')) . ']', ii_itake('module.addbuyerror1', 'lng'));
          $tsqlary[$tid] = "update $tdatabase set " . ii_cfnames($tfpre, 'limitnum') . "=" . ii_cfnames($tfpre, 'limitnum') . "-$tnum where $tidfield=$tid";
        }
        else $Err[count($Err)] = ii_itake('module.addbuyerror2', 'lng');
      }
    } 
  }
  if (ii_isnull($titems)) $Err[count($Err)] = ii_itake('module.payerror', 'lng');
  if (!is_array($Err))
  {
    $titems = ii_get_lrstr($titems, ',', 'leftr');
    if (is_array($tsqlary))
    {
      foreach ($tsqlary as $key => $val)
      {
        if (!ii_isnull($val)) @ii_conn_query($val, $conn);
      }
    }
    $ttraffic = ii_get_num($_POST['traffic'], 0);
    $tmerchandiseprice = ii_get_num($_POST['merchandiseprice'], 0);
    $ttrafficprice = ii_itake('sel_traffic_fare.' . $ttraffic, 'sel');
    $tallprice = $tmerchandiseprice + $ttrafficprice;
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('fid') . ",
    " . ii_cfname('merchandiseprice') . ",
    " . ii_cfname('trafficprice') . ",
    " . ii_cfname('allprice') . ",
    " . ii_cfname('name') . ",
    " . ii_cfname('address') . ",
    " . ii_cfname('phone') . ",
    " . ii_cfname('code') . ",
    " . ii_cfname('email') . ",
    " . ii_cfname('remark') . ",
    " . ii_cfname('payment') . ",
    " . ii_cfname('traffic') . ",
    " . ii_cfname('username') . ",
    " . ii_cfname('time') . ",
    " . ii_cfname('dtime') . "
    ) values (
    '$titems',
    $tmerchandiseprice,
    $ttrafficprice,
    $tallprice,
    '" . ii_left(ii_cstr($_POST['name']), 50) . "',
    '" . ii_left(ii_cstr($_POST['address']), 255) . "',
    '" . ii_left(ii_cstr($_POST['phone']), 50) . "',
    '" . ii_left(ii_cstr($_POST['code']), 50) . "',
    '" . ii_left(ii_cstr($_POST['email']), 50) . "',
    '" . ii_left(ii_cstr($_POST['remark']), 1000) . "',
    " . ii_get_num($_POST['payment']) . ",
    " . ii_get_num($_POST['traffic']) . ",
    '$nusername',
    '" . ii_now() . "',
    '" . ii_now() . "'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $tupid = ii_conn_insert_id();
      $torderid = ii_format_date(ii_now(), 0) . ($tupid % 10);
      $tsqlstr = "update $ndatabase set " . ii_cfname('orderid') . "=$torderid where $nidfield=$tupid";
      @ii_conn_query($tsqlstr, $conn);
      mm_client_redirect('./?type=succeed&orderid=' . $torderid);
    }
    else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), -1);
  }
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'add':
      jtbc_cms_module_adddisp();
      break;
    case 'edit':
      jtbc_cms_module_editdisp();
      break;
    case 'delete':
      jtbc_cms_module_deletedisp();
      break;
    case 'buy':
      jtbc_cms_module_buydisp();
      break;
  }
}

function jtbc_cms_module_list()
{
  global $conn, $nshop, $ngenre;
  $tbackurl = $_GET['backurl'];
  $tcontinue = ii_get_actual_route($nshop);
  if (!ii_isnull($tbackurl)) $tcontinue = ii_htmlencode($tbackurl);
  $tdatabase = mm_cndatabase($nshop);
  $tidfield = mm_cnidfield($nshop);
  $tfpre = mm_cnfpre($nshop);
  $tmpstr = ii_itake('module.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tmerchandiseprice = 0;
  $tcookiesAry = $_COOKIE[APP_NAME . $ngenre];
  if (is_array($tcookiesAry))
  {
    foreach ($tcookiesAry as $key => $val)
    {
      $tid = ii_get_num($key, 0);
      if ($tid != 0)
      {
        $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'hidden') . "=0 and $tidfield=$tid";
        $trs = ii_conn_query($tsqlstr, $conn);
        $trs = ii_conn_fetch_array($trs);
        if ($trs)
        {
          $tnum = ii_get_num($val, 0);
          $tprice = ii_get_num($trs[ii_cfnames($tfpre, 'price')], 0);
          $twprice = ii_get_num($trs[ii_cfnames($tfpre, 'wprice')], 0);
          $tmerchandiseprice = $tmerchandiseprice + ($twprice * $tnum);
          $tmptstr = $tmpastr;
          $tmptstr = str_replace('{$id}', $trs[$tidfield], $tmptstr);
          $tmptstr = str_replace('{$num}', $tnum, $tmptstr);
          $tmptstr = str_replace('{$price}', $tprice, $tmptstr);
          $tmptstr = str_replace('{$wprice}', $twprice, $tmptstr);
          $tmptstr = str_replace('{$topic}', ii_htmlencode($trs[ii_cfnames($tfpre, 'topic')]), $tmptstr);
          $tmptstr = str_replace('{$limitnum}', ii_get_num($trs[ii_cfnames($tfpre, 'limitnum')], 0), $tmptstr);
          $tmprstr .= $tmptstr;
        }
      }
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$merchandiseprice}', $tmerchandiseprice, $tmpstr);
  $tmpstr = str_replace('{$continue}', $tcontinue, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_module_succeed()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $torderid = ii_cstr($_GET['orderid']);
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('orderid') . "='$torderid'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.succeed', 'tpl');
    $tmpstr = str_replace('{$orderid}', $torderid, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), './?type=list');
}

function jtbc_cms_module_index()
{
  $tmpstr = ii_ireplace('module.index', 'tpl');
  if (!ii_isnull($tmpstr)) return $tmpstr;
  else return jtbc_cms_module_list();
}

function jtbc_cms_module()
{
  switch(mm_ctype($_GET['type']))
  {
    case 'list':
      return jtbc_cms_module_list();
      break;
    case 'succeed':
      return jtbc_cms_module_succeed();
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
