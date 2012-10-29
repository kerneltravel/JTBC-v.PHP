<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function pp_get_uninstall_module_select()
{
  global $variable;
  $tary = ii_get_valid_module();
  if (is_array($tary))
  {
    $tmpstr = '';
    $option_unselected = ii_itake('global.tpl_config.option_unselect', 'tpl');
    foreach ($tary as $key => $val)
    {
      $tnuninstall = $variable[ii_cvgenre ($val) . '.nuninstall'];
      if (!ii_isnull($tnuninstall))
      {
        $tmprstr = $option_unselected;
        $tmprstr = str_replace('{$explain}', '(' . mm_get_genre_description($val) . ')' . $val, $tmprstr);
        $tmprstr = str_replace('{$value}', $val, $tmprstr);
        $tmpstr .= $tmprstr;
      }
    }
    return $tmpstr;
  }
}

function pp_get_uninstall_module($module)
{
  global $variable;
  $tarys = Array();
  $tmodule = $module;
  $tary = ii_get_valid_module();
  if (is_array($tary))
  {
    foreach ($tary as $key => $val)
    {
      if (strlen($val) >= strlen($tmodule))
      {
        if ($val == $tmodule || ii_get_num(strpos($val, $tmodule . '/'), -1) == 0)
        {
          $tnuninstall = $variable[ii_cvgenre ($val) . '.nuninstall'];
          if (!ii_isnull($tnuninstall)) $tarys[$val] = $tnuninstall;
        }
      }
    }
  }
  return $tarys;
}

function pp_uninstall_module($genre, $set)
{
  global $conn, $variable;
  if (!ii_isnull($set))
  {
    $tary = explode('|', $set);
    if (count($tary) >= 3)
    {
      if ($tary[0] == '1')
      {
        $tdropstate1 = 1;
        $tdropstate2 = 1;
        $tdatabasestr = ii_cvgenre($genre) . '.ndatabase';
        if (is_array($variable))
        {
          foreach ($variable as $key => $val)
          {
            if (ii_get_num(strpos($key, $tdatabasestr), -1) == 0)
            {
              $tdropstate2 = 0;
              $tsqlstr = "DROP TABLE `" . $val . "`";
              if (ii_conn_query($tsqlstr, $conn)) $tdropstate1 = 0;
            }
          }
          if ($tdropstate2 == 0 && $tdropstate1 == 1) jtbc_cms_admin_msg(ii_itake('manage.uninstall_error_0', 'lng'), '?type=uninst', 1);
        }
      }
      if ($tary[1] == '1')
      {
        global $sort_database, $sort_fpre;
        @mm_exec_delete($sort_database, " where " . ii_cfnames($sort_fpre, 'genre') . "='" . $genre . "'");
      }
      if ($tary[2] == '1')
      {
        $tdatabase = $variable['common.upload.ndatabase'];
        $tfpre = $variable['common.upload.nfpre'];
        @mm_exec_delete($tdatabase, " where " . ii_cfnames($tfpre, 'genre') . "='" . $genre . "'");
      }
      if (!ii_deldir(ii_get_actual_route($genre))) jtbc_cms_admin_msg(ii_itake('manage.uninstall_error_1', 'lng'), '?type=uninst', 1);
    }
  }
}

function jtbc_cms_admin_manage_installdisp()
{
  global $conn;
  $tpath = ii_get_actual_route('./');
  $tfilesize = ii_get_num($_FILES['file1']['size']);
  if ($tfilesize <= 0) $terrnum = 0;
  else
  {
    $tmp_filename = $_FILES['file1']['tmp_name'];
    $tdoc = new DOMDocument();
    $tdoc -> load($tmp_filename);
    $txpath = new DOMXPath($tdoc);
    $tquery = '//xml/configure/genre';
    $tgenre = $txpath -> query($tquery) -> item(0) -> nodeValue;
    $tgenrepath = ii_get_actual_route($tgenre);
    if (is_dir($tgenrepath)) $terrnum = 1;
    else
    {
      $tquery = '//xml/item_list/item';
      $trests = $txpath -> query($tquery);
      foreach ($trests as $trest)
      {
        $texfilename = ii_get_actual_route($trest -> childNodes -> item(1) -> nodeValue);
        ii_mkdir(ii_get_lrstr($texfilename, '/', 'leftr'));
        file_put_contents($texfilename, base64_decode($trest -> childNodes -> item(3) -> nodeValue));
      }
      $tsqltext = @file_get_contents($tgenrepath . '/_install/mysql.sql');
      if (!ii_isnull($tsqltext))
      {
        $tsqlary = explode(';', $tsqltext);
        foreach ($tsqlary as $key => $val)
        {
          if (!ii_isnull($val))
          {
            if (!ii_conn_query($val, $conn)) $terrnum = 2;
          }
        }
        @ii_deldir($tgenrepath . '/_install');
      }
      if (!isset($terrnum)) $terrnum = -1;
    }
  }
  switch ($terrnum)
  {
    case 0:
      jtbc_cms_admin_msg(ii_itake('manage.install_error_0', 'lng'), '?type=install', 1);
      break;
    case 1:
      jtbc_cms_admin_msg(ii_itake('manage.install_error_1', 'lng'), '?type=install', 1);
      break;
    case 2:
      jtbc_cms_admin_msg(ii_itake('manage.install_error_2', 'lng'), '?type=install', 1);
      break;
    case -1:
      jtbc_cms_admin_msg(ii_itake('manage.install_succeed', 'lng'), '?type=install', 1);
      break;
    default:
      jtbc_cms_admin_msg(ii_itake('manage.install_succeed', 'lng'), '?type=install', 1);
      break;
  }
}

function jtbc_cms_admin_manage_uninstalldisp()
{
  global $variable;
  $tarys = Array();
  $tmodule = $_POST['module'];
  $tary = pp_get_uninstall_module($tmodule);
  if (is_array($tary))
  {
    while (count($tary) > 0)
    {
      $tlens = 0;
      $tkeys = '';
      foreach ($tary as $key => $val)
      {
        if (strlen($key) > $tlens)
        {
          $tlens = strlen($key);
          $tkeys = $key;
        }
      }
      $tarys[$tkeys] = $tary[$tkeys];
      unset($tary[$tkeys]);
    }
  }
  foreach ($tarys as $key => $val)
  {
    pp_uninstall_module($key, $val);
  }
  jtbc_cms_admin_msg(ii_itake('manage.uninstall_succeed', 'lng'), '?type=uninst', 1);
}

function jtbc_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  $taction = $_GET['action'];
  if (!ii_isnull($taction)) @ii_cache_remove();
  switch($taction)
  {
    case 'install':
      jtbc_cms_admin_manage_installdisp();
      break;
    case 'uninstall':
      jtbc_cms_admin_manage_uninstalldisp();
      break;
  }
}

function jtbc_cms_admin_manage_install()
{
  $tmpstr = ii_ireplace('manage.install', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_uninst()
{
  $tmpstr = ii_ireplace('manage.uninst', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_uninstall()
{
  global $variable;
  $tmodule = $_GET['module'];
  $tmpstr = ii_itake('manage.uninstall', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tary = pp_get_uninstall_module($tmodule);
  if (is_array($tary))
  {
    foreach ($tary as $key => $val)
    {
      $tmptstr = str_replace('{$title}', '(' . mm_get_genre_description($key) . ')' . $key, $tmpastr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$module}', ii_htmlencode($tmodule), $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'install':
      return jtbc_cms_admin_manage_install();
      break;
    case 'uninst':
      return jtbc_cms_admin_manage_uninst();
      break;
    case 'uninstall':
      return jtbc_cms_admin_manage_uninstall();
      break;
    default:
      return jtbc_cms_admin_manage_install();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
