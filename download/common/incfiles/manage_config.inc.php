<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();
$nurltype = 0;
$nsearch = 'topic,sort,id';
$ncontrol = 'select,hidden,good,delete';
$ncttype = ii_get_num($_GET['htype'], -1);
if ($ncttype == -1) $ncttype = 0;

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function pp_manage_batch_menu()
{
  return ii_ireplace('manage.batch_menu', 'tpl');
}

function pp_get_post_urls($count)
{
  $tmpstr = '';
  for ($i = 1; $i <= $count; $i ++)
  {
    $tmpstr .= ii_cstr($_POST['urls_topic' . $i]) . '{:::}' . ii_cstr($_POST['urls_link' . $i]) . '{|||}';
  }
  if (!ii_isnull($tmpstr)) $tmpstr = ii_left($tmpstr, mb_strlen($tmpstr, CHARSET) - 5);
  return $tmpstr;
}

function jtbc_cms_admin_manage_adddisp()
{
  global $ngenre;
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tclass = ii_get_num($_POST['sort'], 0);
  $tbackurl = ii_replace_querystring('classid', $tclass, $tbackurl);
  $timage = ii_left(ii_cstr($_POST['image']), 250);
  $tcontent_images_list = ii_left(ii_cstr($_POST['content_images_list']), 10000);
  if (!($tclass == 0))
  {
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('topic') . ",
    " . ii_cfname('cls') . ",
    " . ii_cfname('class') . ",
    " . ii_cfname('scont') . ",
    " . ii_cfname('image') . ",
    " . ii_cfname('content') . ",
    " . ii_cfname('cttype') . ",
    " . ii_cfname('content_images_list') . ",
    " . ii_cfname('size') . ",
    " . ii_cfname('runco') . ",
    " . ii_cfname('star') . ",
    " . ii_cfname('accredit') . ",
    " . ii_cfname('lng') . ",
    " . ii_cfname('link') . ",
    " . ii_cfname('author') . ",
    " . ii_cfname('urls') . ",
    " . ii_cfname('hidden') . ",
    " . ii_cfname('good') . ",
    " . ii_cfname('time') . "
    ) values (
    '" . ii_left(ii_cstr($_POST['topic']), 50) . "',
    '" . mm_get_sort_cls($tclass) . "',
    $tclass,
    '" . ii_left(ii_cstr($_POST['scont']), 1000) . "',
    '$timage',
    '" . ii_left(ii_cstr($_POST['content']), 100000) . "',
    " . ii_get_num($_POST['cttype']) . ",
    '$tcontent_images_list',
    " . ii_get_num($_POST['size']) . ",
    '" . ii_left(mm_get_postarystr($_POST['runco']), 255) . "',
    " . ii_get_num($_POST['star']) . ",
    " . ii_get_num($_POST['accredit']) . ",
    " . ii_get_num($_POST['lng']) . ",
    '" . ii_left(ii_cstr($_POST['link']), 255) . "',
    '" . ii_left(ii_cstr($_POST['author']), 255) . "',
    '" . ii_left(pp_get_post_urls(ii_get_num($_POST['urls_date_option'])), 10000) . "',
    " . ii_get_num($_POST['hidden']) . ",
    " . ii_get_num($_POST['good']) . ",
    '" . ii_now() . "'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $upfid = ii_conn_insert_id();
      uu_upload_update_database_note($ngenre, $timage, 'image', $upfid);
      uu_upload_update_database_note($ngenre, $tcontent_images_list, 'content_images', $upfid);
      jtbc_cms_admin_msg(ii_itake('global.lng_public.add_succeed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.add_failed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_editdisp()
{
  global $ngenre;
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tclass = ii_get_num($_POST['sort'], 0);
  $timage = ii_left(ii_cstr($_POST['image']), 250);
  $tcontent_images_list = ii_left(ii_cstr($_POST['content_images_list']), 10000);
  $tid = ii_get_num($_GET['id']);
  if (!($tclass == 0))
  {
    $tsqlstr = "update $ndatabase set
    " . ii_cfname('topic') . "='" . ii_left(ii_cstr($_POST['topic']), 50) . "',
    " . ii_cfname('cls') . "='" . mm_get_sort_cls($tclass) . "',
    " . ii_cfname('class') . "=$tclass,
    " . ii_cfname('scont') . "='" . ii_left(ii_cstr($_POST['scont']), 1000) . "',
    " . ii_cfname('image') . "='$timage',
    " . ii_cfname('content') . "='" . ii_left(ii_cstr($_POST['content']), 100000) . "',
    " . ii_cfname('cttype') . "=" . ii_get_num($_POST['cttype']) . ",
    " . ii_cfname('content_images_list') . "='$tcontent_images_list',
    " . ii_cfname('size') . "=" . ii_get_num($_POST['size']) . ",
    " . ii_cfname('runco') . "='" . ii_left(mm_get_postarystr($_POST['runco']), 255) . "',
    " . ii_cfname('star') . "=" . ii_get_num($_POST['star']) . ",
    " . ii_cfname('accredit') . "=" . ii_get_num($_POST['accredit']) . ",
    " . ii_cfname('lng') . "=" . ii_get_num($_POST['lng']) . ",
    " . ii_cfname('link') . "='" . ii_left(ii_cstr($_POST['link']), 255) . "',
    " . ii_cfname('author') . "='" . ii_left(ii_cstr($_POST['author']), 255) . "',
    " . ii_cfname('urls') . "='" . ii_left(pp_get_post_urls(ii_get_num($_POST['urls_date_option'])), 10000) . "',
    " . ii_cfname('hidden') . "=" . ii_get_num($_POST['hidden']) . ",
    " . ii_cfname('good') . "=" . ii_get_num($_POST['good']) . ",
    " . ii_cfname('time') . "='" . ii_get_date(ii_cstr($_POST['time'])) . "'
    where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $upfid = $tid;
      uu_upload_update_database_note($ngenre, $timage, 'image', $upfid);
      uu_upload_update_database_note($ngenre, $tcontent_images_list, 'content_images', $upfid);
      jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  switch($_GET['action'])
  {
    case 'add':
      jtbc_cms_admin_manage_adddisp();
      break;
    case 'edit':
      jtbc_cms_admin_manage_editdisp();
      break;
    case 'delete':
      jtbc_cms_admin_deletedisp($ndatabase, $nidfield);
      break;
    case 'control':
      jtbc_cms_admin_controldisp($ndatabase, $nidfield, $nfpre, $ncontrol);
      break;
    case 'batch_shift':
      jtbc_cms_admin_batch_shiftdisp($ndatabase, $nidfield, $nfpre);
      break;
    case 'batch_delete':
      jtbc_cms_admin_batch_deletedisp($ndatabase, $nidfield, $nfpre);
      break;
    case 'upload':
      uu_upload_files();
      break;
  }
}

function jtbc_cms_admin_manage_add()
{
  $tmpstr = ii_ireplace('manage.add', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_edit()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('manage.edit', 'tpl');
    $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
    $tmprstr = '';
    $turls = $trs[ii_cfname('urls')];
    if (!ii_isnull($turls))
    {
      $ticount = 1;
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
            $tmptstr = str_replace('{$urls_topic}', $turlstrary[0], $tmpastr);
            $tmptstr = str_replace('{$urls_link}', $turlstrary[1], $tmptstr);
            $tmptstr = str_replace('{$ulop_i}', $ticount, $tmptstr);
            $ticount += 1;
            $tmprstr .= $tmptstr;
          }
        }
      }
    }
    else $turlscount = 0;
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
    $tmpstr = str_replace('{$ulop_count}', $turlscount, $tmpstr);
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
  global $conn, $slng;
  global $ngenre, $nclstype, $npagesize, $nlisttopx;
  global $ndatabase, $nidfield, $nfpre;
  global $sort_database, $sort_idfield, $sort_fpre;
  $toffset = ii_get_num($_GET['offset']);
  $tclassid = ii_get_num($_GET['classid']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase,$sort_database where $ndatabase." . ii_cfname('class') . "=$sort_database.$sort_idfield and $sort_database." . ii_cfnames($sort_fpre, 'lng') . "='$slng' and $sort_database." . ii_cfnames($sort_fpre, 'genre') . "='$ngenre'";
  if ($tclassid != 0)
  {
    if ($nclstype == 0) $tsqlstr .= " and $ndatabase." . ii_cfname('class') . "=$tclassid";
    else $tsqlstr .= " and $ndatabase." . ii_cfname('cls') . " like '%|" . $tclassid . "|%'";
  }
  if ($search_field == 'topic') $tsqlstr .= " and $ndatabase." . ii_cfname('topic') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'sort') $tsqlstr .= " and $sort_database." . ii_cfnames($sort_fpre, 'sort') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'good') $tsqlstr .= " and $ndatabase." . ii_cfname('good') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'hidden') $tsqlstr .= " and $ndatabase." . ii_cfname('hidden') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'id') $tsqlstr .= " and $ndatabase.$nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by $ndatabase." . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  $font_disabled = ii_itake('global.tpl_config.font_disabled', 'tpl');
  $postfix_good = ii_ireplace('global.tpl_config.postfix_good', 'tpl');
  if (!(ii_isnull($search_keyword)) && $search_field == 'topic') $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $ttopic = ii_htmlencode($trs[ii_cfname('topic')]);
      if (isset($font_red))
      {
        $font_red = str_replace('{$explain}', $search_keyword, $font_red);
        $ttopic = str_replace($search_keyword, $font_red, $ttopic);
      }
      if ($trs[ii_cfname('hidden')] == 1) $ttopic = str_replace('{$explain}', $ttopic, $font_disabled);
      if ($trs[ii_cfname('good')] == 1) $ttopic .= $postfix_good;
      $tmptstr = str_replace('{$topic}', $ttopic, $tmpastr);
      $tmptstr = str_replace('{$topicstr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('topic')])), $tmptstr);
      $tmptstr = str_replace('{$sort}', ii_htmlencode($trs[ii_cfnames($sort_fpre, 'sort')]), $tmptstr);
      $tmptstr = str_replace('{$classid}', ii_get_num($trs[$sort_idfield]), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$nidfield]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$nav_sort}', mm_nav_sort($ngenre, '?classid=', $tclassid), $tmpstr);
  $tmpstr = str_replace('{$nav_sort_child}', mm_nav_sort_child($ngenre, '?classid=', $tclassid, 6), $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage_batch_shift()
{
  $tmpstr = ii_ireplace('manage.batch_shift', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_batch_delete()
{
  $tmpstr = ii_ireplace('manage.batch_delete', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_displace()
{
  switch($_GET['mtype'])
  {
    case 'batch_shift':
      return jtbc_cms_admin_manage_batch_shift();
      break;
    case 'batch_delete':
      return jtbc_cms_admin_manage_batch_delete();
      break;
    default:
      return jtbc_cms_admin_manage_batch_shift();
      break;
  }
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'add':
      return jtbc_cms_admin_manage_add();
      break;
    case 'edit':
      return jtbc_cms_admin_manage_edit();
      break;
    case 'list':
      return jtbc_cms_admin_manage_list();
      break;
    case 'displace':
      return jtbc_cms_admin_manage_displace();
      break;
    case 'upload':
      uu_upload_files_html('upload_html');
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
