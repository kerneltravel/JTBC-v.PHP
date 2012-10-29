<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function mm_get_admin_sellng()
{
  global $slng;
  $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  $tmpstr = ii_ireplace('global.tpl_admin.slng', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, "{@recurrence_ida}");
  $tarys = ii_itake('global.sel_lng.all', 'sel', 1);
  if (is_array($tarys))
  {
    foreach ($tarys as $key => $val)
    {
      if ($key == $slng) $val = str_replace('{$explain}', $val, $font_red);
      $tmptstr = str_replace('{$topic}', $val, $tmpastr);
      $tmptstr = str_replace('{$href}', '?' . ii_replace_querystring('slng', $key), $tmptstr);
      $tmprstr = $tmprstr . $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  return $tmpstr;
}

function mm_get_admin_search()
{
  global $nsearch;
  if (!ii_isnull($nsearch))
  {
    $tfield = ii_get_safecode($_GET['field']);
    return  ii_show_xmlinfo_select('global.sel_search.all|' . $nsearch, $tfield, 'select');
  }
}

function mm_get_admin_keyword()
{
  global $nsearch;
  if (!ii_isnull($nsearch))
  {
    $tfield = ii_get_safecode($_GET['field']);
    $tkeyword = $_GET['keyword'];
    if (ii_cinstr($nsearch, $tfield, ',')) return ii_htmlencode($tkeyword);
  }
}

function mm_get_genre_description($genre)
{
  if (!ii_isnull($genre))
  {
    $tmpstr = @ii_itake('global.' . $genre . ':manage.mgtitle', 'lng');
    if (ii_isnull($tmpstr)) $tmpstr = @ii_itake('global.' . $genre . ':module.channel_title', 'lng');
    if (ii_isnull($tmpstr)) $tmpstr = '?';
    return $tmpstr;
  }
}

function mm_get_html_content()
{
  $tmpstr = ii_ireplace('global.tpl_config.a_href_sort', 'tpl');
  $tmpstr0 = str_replace('{$explain}', ii_itake('global.lng_admin.content_htmledit', 'lng'), $tmpstr);
  $tmpstr0 = str_replace('{$value}', '?' . ii_replace_querystring('htype', '0'), $tmpstr0);
  $tmpstr1 = str_replace('{$explain}', ii_itake('global.lng_admin.content_ubbcode', 'lng'), $tmpstr);
  $tmpstr1 = str_replace('{$value}', '?' . ii_replace_querystring('htype', '1'), $tmpstr1);
  $tmpstr2 = str_replace('{$explain}', ii_itake('global.lng_admin.content_text', 'lng'), $tmpstr);
  $tmpstr2 = str_replace('{$value}', '?' . ii_replace_querystring('htype', '2'), $tmpstr2);
  $tmpstr3 = str_replace('{$explain}', ii_itake('global.lng_admin.content_html', 'lng'), $tmpstr);
  $tmpstr3 = str_replace('{$value}', '?' . ii_replace_querystring('htype', '3'), $tmpstr3);
  return $tmpstr3 . ' ' . $tmpstr0 . ' ' . $tmpstr1 . ' ' . $tmpstr2;
}

function mm_html_content($name, $value, $htype)
{
  global $ncttype;
  $thtype = $_GET['htype'];
  if (ii_isnull($thtype))
  {
    $thtype = $htype;
    if (ii_isnull($thtype)) $thtype = $ncttype;
  }
  $thtype = ii_get_num($thtype);
  switch($thtype)
  {
    case 0:
      $tmpstr = ii_itake('global.tpl_admin.content_htmledit', 'tpl');
      break;
    case 1:
      $tmpstr = ii_itake('global.tpl_admin.content_ubbcode', 'tpl');
      break;
    case 2:
      $tmpstr = ii_itake('global.tpl_admin.content_text', 'tpl');
      break;
    case 3:
      $tmpstr = ii_itake('global.tpl_admin.content_html', 'tpl');
      break;
    default:
      $tmpstr = ii_itake('global.tpl_admin.content_htmledit', 'tpl');
      break;
  }
  $tmpstr = str_replace('{$name}', $name, $tmpstr);
  $tmpstr = str_replace('{$value}', $value, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function mm_nav_sort($sgenre, $baseurl, $id)
{
  global $conn;
  global $slng;
  global $sort_database, $sort_idfield, $sort_fpre;
  $tid = ii_get_num($id);
  $tpl_href = ii_itake('global.tpl_config.a_href_sort', 'tpl');
  $tsqlstr = "select * from $sort_database where $sort_idfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tfid = $trs[ii_cfnames($sort_fpre, 'fid')];
    $tfid = mm_get_sortfid($tfid, $tid);
    if (ii_cidary($tfid))
    {
      $tmpstr = '';
      $font_disabled = ii_itake('global.tpl_config.font_disabled', 'tpl');
      $tsqlstr = "select * from $sort_database where $sort_idfield in (" . $tfid . ") and " . ii_cfnames($sort_fpre, 'genre') . "='$sgenre' and " . ii_cfnames($sort_fpre, 'lng') . "='$slng' order by $sort_idfield asc";
      $trs = ii_conn_query($tsqlstr, $conn);
      while ($trow = ii_conn_fetch_array($trs))
      {
        $tsort = $trow[ii_cfnames($sort_fpre, 'sort')];
        if ($trow[ii_cfnames($sort_fpre, 'hidden')] == 1) $tsort = str_replace('{$explain}', $tsort, $font_disabled);
        $tstr = $tpl_href;
        $tstr = str_replace('{$explain}', $tsort, $tstr);
        $tstr = str_replace('{$value}', $baseurl . $trow[$sort_idfield], $tstr);
        $tmpstr .= $tstr;
      }
      return $tmpstr;
    }
  }
}

function mm_nav_sort_child($sgenre, $baseurl, $fid, $rnum)
{
  global $conn;
  global $slng;
  global $sort_database, $sort_idfield, $sort_fpre;
  $tfid = ii_get_num($fid);
  $trnum = ii_get_num($rnum);
  if ($tfid < 0 || $trnum <= 0) return;
  $tpl_href = ii_ireplace('global.tpl_config.a_href_sort', 'tpl');
  $tpl_html = ii_ireplace('global.tpl_config.table_html', 'tpl');
  $tstra = ii_ctemplate($tpl_html, '{@}');
  $tstrb = ii_ctemplate($tstra, '{@@}');
  $tsqlstr = "select * from $sort_database where " . ii_cfnames($sort_fpre, 'fsid') . "=$tfid and " . ii_cfnames($sort_fpre, 'hidden') . "=0 and " . ii_cfnames($sort_fpre, 'genre') . "='$sgenre' and " . ii_cfnames($sort_fpre, 'lng') . "='$slng' order by " . ii_cfnames($sort_fpre, 'order') . " asc";
  $trs = ii_conn_query($tsqlstr, $conn);
  $ti = 0;
  $tstrc = ''; $tstrd =''; $tstre = '';
  while ($trow = ii_conn_fetch_array($trs))
  {
    if (!($ti == 0) && ($ti % $trnum == 0))
    {
      $tstrc .= str_replace(JTBC_CINFO, $tstre, $tstra);
      $tstrd =''; $tstre = '';
    }
    $tsort = $trow[ii_cfnames($sort_fpre, 'sort')];
    $tstrd = $tpl_href;
    $tstrd = str_replace('{$explain}', $tsort, $tstrd);
    $tstrd = str_replace('{$value}', $baseurl . $trow[$sort_idfield], $tstrd);
    $tstre .= str_replace('{$value}', $tstrd, $tstrb);
    $ti += 1;
  }
  if (!(ii_isnull($tstre))) $tstrc .= str_replace(JTBC_CINFO, $tstre, $tstra);
  $tstrc = str_replace(JTBC_CINFO, $tstrc, $tpl_html);
  return $tstrc;
}

function jtbc_cms_cklogin($username, $password)
{
  global $conn;
  global $admc_username, $admc_popedom;
  if (ii_isnull($admc_popedom) || ii_isnull($admc_username))
  {
    global $variable;
    $tdatabase =  $variable['common.admin.ndatabase'];
    $tidfield =  $variable['common.admin.nidfield'];
    $tfpre =  $variable['common.admin.nfpre'];
    $tusername = ii_get_safecode($username);
    $tpassword = ii_get_safecode($password);
    $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'name') . "='$tusername' and " . ii_cfnames($tfpre, 'pword') . "='$tpassword' and " . ii_cfnames($tfpre, 'lock') . "=0";
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs)
    {
      setcookie(APP_NAME . 'admin[username]', $trs[ii_cfnames($tfpre, 'name')], 0, COOKIES_PATH);
      setcookie(APP_NAME . 'admin[password]', $trs[ii_cfnames($tfpre, 'pword')], 0, COOKIES_PATH);
      session_register(APP_NAME . 'admin_popedom');
      session_register(APP_NAME . 'admin_username');
      $_SESSION[APP_NAME . 'admin_popedom'] = $trs[ii_cfnames($tfpre, 'popedom')];
      $_SESSION[APP_NAME . 'admin_username'] = $trs[ii_cfnames($tfpre, 'name')];
      $admc_popedom = $_SESSION[APP_NAME . 'admin_popedom'];
      $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'lasttime') . "='" . ii_now() . "', " . ii_cfnames($tfpre, 'lastip') . "='" . ii_get_client_ip() . "' where " . ii_cfnames($tfpre, 'name') . "='$tusername' and " . ii_cfnames($tfpre, 'pword') . "='$tpassword' and " . ii_cfnames($tfpre, 'lock') . "=0";
      ii_conn_query($tsqlstr, $conn);
      return true;
    }
    else
    {
      return false;
    }
  }
  else
  {
    return true;
  }
}

function jtbc_cms_admin_init()
{
  global $admin_head, $admin_foot;
  global $admc_name, $admc_pword, $admc_popedom, $admc_username;
  $admin_head = 'admin_head';
  $admin_foot = 'admin_foot';
  $admc_name = ii_get_safecode($_COOKIE[APP_NAME . 'admin']['username']);
  $admc_pword = ii_get_safecode($_COOKIE[APP_NAME . 'admin']['password']);
  $admc_popedom = $_SESSION[APP_NAME . 'admin_popedom'];
  $admc_username = $_SESSION[APP_NAME . 'admin_username'];
  global $slng, $nlng;
  $slng = ii_get_safecode($_GET['slng']);
  if (!(ii_isnull($slng)))
  {
    setcookie(APP_NAME . 'admin[slng]', $slng, time() + 31536000);
  }
  else
  {
    $slng = ii_get_safecode($_COOKIE[APP_NAME . 'admin']['slng']);
    if (ii_isnull($slng)) $slng = $nlng;
  }
}

function jtbc_cms_admin_showmsg($msg, $backurl)
{
  $tmpstr = ii_ireplace('global.tpl_admin.msg', 'tpl');
  $tmpstr = str_replace('{$backurl}', $backurl, $tmpstr);
  $tmpstr = str_replace('{$msginfo}', $msg, $tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_showmsgs($msg)
{
  $tmpstr = ii_ireplace('global.tpl_admin.msgs', 'tpl');
  $tmpstr = str_replace('{$msginfo}', $msg, $tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_msg($msg, $backurl, $type)
{
  global $admin_head, $admin_foot;
  if ($type == 0)
  {
    echo jtbc_cms_admin_showmsg($msg, $backurl);
  }
  else
  {
    ob_end_clean();
    $tmybody = jtbc_cms_admin_showmsg($msg, $backurl);
    $tmyhead = jtbc_cms_web_head($admin_head);
    $tmyfoot = jtbc_cms_web_foot($admin_foot);
    $tmyhtml = $tmyhead . $tmybody . $tmyfoot;
    echo $tmyhtml;
    exit();
  }
}

function jtbc_cms_admin_msgs($msg, $type)
{
  global $admin_head, $admin_foot;
  if ($type == 0)
  {
    echo jtbc_cms_admin_showmsgs($msg);
  }
  else
  {
    ob_end_clean();
    $tmybody = jtbc_cms_admin_showmsgs($msg);
    $tmyhead = jtbc_cms_web_head($admin_head);
    $tmyfoot = jtbc_cms_web_foot($admin_foot);
    $tmyhtml = $tmyhead . $tmybody . $tmyfoot;
    echo $tmyhtml;
    exit();
  }
}

function jtbc_cms_islogin()
{
  global $npopedom, $ngenre;
  global $admc_name, $admc_pword, $admc_popedom, $admc_pstate;
  if (ii_isnull($npopedom)) $npopedom = $ngenre;
  if (!(jtbc_cms_cklogin($admc_name, $admc_pword))) header('location: ' . ii_get_actual_route(ADMIN_FOLDER));
  if (!(ii_cinstr($admc_popedom, $npopedom, ',') || $admc_pstate == 'public' || $admc_popedom == '-1')) jtbc_cms_admin_msgs(ii_itake('global.lng_admin.popedom_error', 'lng'), 1);
}

function jtbc_cms_admin_batch_shiftdisp($database, $idfield, $fpre)
{
  global $conn;
  $tbackurl = $_GET['backurl'];
  $tsort1 = ii_get_num($_POST['sort1']);
  $tsort2 = ii_get_num($_POST['sort2']);
  $tchild = ii_get_num($_POST['child']);
  $tsqlstr = "update $database set " . ii_cfnames($fpre, 'class') . "=$tsort2," . ii_cfnames($fpre, 'cls') . "='" . mm_get_sort_cls($tsort2) . "'";
  if ($tchild == 0) $tsqlstr .= " where " . ii_cfnames($fpre, 'class') . "=$tsort1";
  else $tsqlstr .= " where " . ii_cfnames($fpre, 'cls') . " like '%|" . $tsort1 . "|%'";
  $trs = ii_conn_query($tsqlstr, $conn);
  if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_batch_deletedisp($database, $idfield, $fpre)
{
  global $conn;
  global $ngenre, $nuppath, $nupcfgs;
  $tbackurl = $_GET['backurl'];
  $tsort1 = ii_get_num($_POST['sort1']);
  $tchild = ii_get_num($_POST['child']);
  $tsqlstr = "select * from $database";
  if (!($tsort1 == -1))
  {
    if ($tchild == 0) $tsqlstr .= " where " . ii_cfnames($fpre, 'class') . "=$tsort1";
    else $tsqlstr .= " where " . ii_cfnames($fpre, 'cls') . " like '%|" . $tsort1 . "|%'";
  }
  $trs = ii_conn_query($tsqlstr, $conn);
  while ($trow = ii_conn_fetch_array($trs))
  {
    if (!(ii_isnull($nuppath)) && $nupcfgs != -1) uu_upload_delete_database_note($ngenre, $trow[$idfield]);
    mm_dbase_delete($database, $idfield, $trow[$idfield]);
  }
  jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_controldisp($database, $idfield, $fpre, $control)
{
  global $ngenre, $nuppath, $nupcfgs;
  $tbackurl = $_GET['backurl'];
  $tsid = $_POST['sel_ids'];
  $tcontrol = $_POST['control'];
  switch($tcontrol)
  {
    case 'hidden':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'hidden', $idfield, $tsid);
      break;
    case 'htop':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'htop', $idfield, $tsid);
      break;
    case 'top':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'top', $idfield, $tsid);
      break;
    case 'lock':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'lock', $idfield, $tsid);
      break;
    case 'good':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'good', $idfield, $tsid);
      break;
    case 'spprice':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_switch($database, $fpre . 'spprice', $idfield, $tsid);
      break;
    case 'delete':
      if (ii_cinstr($control, $tcontrol, ',')) $texec = mm_dbase_delete($database, $idfield, $tsid);
      break;
  }
  if ($texec)
  {
    if ($tcontrol == 'delete')
    {
      if (!(ii_isnull($nuppath)) && $nupcfgs != -1) uu_upload_delete_database_note($ngenre, $tsid);
    }
    jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_deletedisp($database, $idfield)
{
  global $ngenre, $nuppath, $nupcfgs;
  $tbackurl = $_GET['backurl'];
  $tid = ii_get_num($_GET['id']);
  $texec = mm_dbase_delete($database, $idfield, $tid);
  if ($texec)
  {
    if (!(ii_isnull($nuppath)) && $nupcfgs != -1) uu_upload_delete_database_note($ngenre, $tid);
    jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_orderdisp($genre, $strers, $osql)
{
  global $conn;
  $tat = $_GET['at'];
  $tbackurl = $_GET['backurl'];
  $tid = ii_get_num($_GET['id']);
  $tdatabase = mm_cndatabase($genre, $strers);
  $tidfield = mm_cnidfield($genre, $strers);
  $tfpre = mm_cnfpre($genre, $strers);
  if (!(ii_isnull($tdatabase)))
  {
    $tsqlstr = "select * from $tdatabase where $tidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs)
    {
      $tsqlstr2 = "select count($tidfield) from $tdatabase where " . ii_cfnames($tfpre, 'fid') . "='" . $trs[ii_cfnames($tfpre, 'fid')] . "'" . $osql;
      $trs2 = ii_conn_query($tsqlstr2, $conn);
      $trs2 = ii_conn_fetch_array($trs2);
      $tfid_count = $trs2[0];
      if ($tat == 'down')
      {
        $tnum = $trs[ii_cfnames($tfpre, 'order')] + 1;
        if ($tnum <= ($tfid_count - 1))
        {
          $tsqlstr3 = "update $tdatabase set " . ii_cfnames($tfpre, 'order') . "=" . ii_cfnames($tfpre, 'order') . "-1 where " . ii_cfnames($tfpre, 'fsid') . "=" . $trs[ii_cfnames($tfpre, 'fsid')] . " and " . ii_cfnames($tfpre, 'order') . "=" . $tnum . $osql;
          $tsqlstr4 = "update $tdatabase set " . ii_cfnames($tfpre, 'order') . "=$tnum where $tidfield=$tid";
          $trs3 = ii_conn_query($tsqlstr3, $conn);
          if ($trs3) @ii_conn_query($tsqlstr4, $conn);
        }
      }
      else
      {
        $tnum = $trs[ii_cfnames($tfpre, 'order')] - 1;
        if ($tnum >= 0)
        {
          $tsqlstr3 = "update $tdatabase set " . ii_cfnames($tfpre, 'order') . "=" . ii_cfnames($tfpre, 'order') . "+1 where " . ii_cfnames($tfpre, 'fsid') . "=" . $trs[ii_cfnames($tfpre, 'fsid')] . " and " . ii_cfnames($tfpre, 'order') . "=" . $tnum . $osql;
          $tsqlstr4 = "update $tdatabase set " . ii_cfnames($tfpre, 'order') . "=$tnum where $tidfield=$tid";
          $trs3 = ii_conn_query($tsqlstr3, $conn);
          if ($trs3) @ii_conn_query($tsqlstr4, $conn);
        }
      }
    }
    mm_client_redirect($tbackurl);
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
