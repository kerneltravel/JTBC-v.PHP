<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_admin_manage_delete()
{
  global $nuri;
  $tfile = $_GET['file'];
  $tcache_dir = ii_get_actual_route('./') . CACHE_DIR;
  $tfilename = $tcache_dir . '/' . $tfile;
  $tfilename = iconv (CHARSET, 'cp936', $tfilename);
  if (unlink($tfilename))
  {
    header('location: ' . $nuri);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('manage.delete_error', 'lng'), $nuri, 1);
  }
}

function jtbc_cms_admin_manage_removeall()
{
  global $nuri;
  @ii_cache_remove();
  header('location: ' . $nuri);
}

function jtbc_cms_admin_manage_action()
{
  switch($_GET['action'])
  {
    case 'delete':
      jtbc_cms_admin_manage_delete();
      break;
    case 'removeall':
      jtbc_cms_admin_manage_removeall();
      break;
  }
}

function jtbc_cms_admin_manage_list()
{
  $tmpstr = ii_ireplace('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{#recurrence_ida}');
  $tcache_dir = ii_get_actual_route('./') . CACHE_DIR;
  $tcdirs = dir($tcache_dir);
  while($tentry = $tcdirs -> read())
  {
    if (is_numeric(strpos($tentry, '.')))
    {
      $ttentry = iconv('cp936', CHARSET, $tentry);
      $tcachename = ii_get_lrstr($ttentry, '.', 'left');
      if (!(ii_isnull($tcachename)))
      {
        $tmptstr = str_replace('{$cache_name}', $tcachename, $tmpastr);
        $tmptstr = str_replace('{$file_name}', urlencode($ttentry), $tmptstr);
        $tmprstr = $tmprstr . $tmptstr;
      }
    }
  }
  $tcdirs -> close();
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'list':
      return jtbc_cms_admin_manage_list();
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
