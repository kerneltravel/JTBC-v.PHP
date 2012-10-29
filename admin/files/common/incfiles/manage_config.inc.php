<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
$nshow_path_str = stripslashes(ii_cstr($_GET['show_path']));
$nshow_path = iconv(CHARSET, 'cp936', $nshow_path_str);
if (ii_isnull($nshow_path) || !is_dir($nshow_path))
{
  $nshow_path = ii_get_actual_route('./');
  $nshow_path_str = ii_get_actual_route('./');
}
else
{
  if (ii_right($nshow_path, 1) != '/') $nshow_path .= '/';
  if (ii_right($nshow_path_str, 1) != '/') $nshow_path_str .= '/';
}

function pp_manage_navigation()
{
  $tmpstr = ii_ireplace('manage.navigation', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_add_folderdisp()
{
  $tbackurl = $_GET['backurl'];
  $tfolder_path = stripslashes(ii_cstr($_POST['folder_path']));
  $tfolder_path = iconv(CHARSET, 'cp936', $tfolder_path);
  if (!is_dir($tfolder_path))
  {
    ii_mkdir($tfolder_path);
    jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_edit_folderdisp()
{
  $tbackurl = $_GET['backurl'];
  $tfolder_patha = stripslashes(ii_cstr($_POST['folder_patha']));
  $tfolder_patha = iconv(CHARSET, 'cp936', $tfolder_patha);
  $tfolder_pathb = stripslashes(ii_cstr($_POST['folder_pathb']));
  $tfolder_pathb = iconv(CHARSET, 'cp936', $tfolder_pathb);
  if (!is_dir($tfolder_pathb))
  {
    if (rename($tfolder_patha, $tfolder_pathb)) jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_delete_folderdisp()
{
  $tbackurl = $_GET['backurl'];
  $tfolder_path = stripslashes(ii_cstr($_GET['folder_path']));
  $tfolder_path = iconv(CHARSET, 'cp936', $tfolder_path);
  @ii_deldir($tfolder_path);
  mm_client_redirect($tbackurl);
}

function jtbc_cms_admin_manage_add_filedisp()
{
  $tbackurl = $_GET['backurl'];
  $tfile_path = stripslashes(ii_cstr($_POST['file_path']));
  $tfile_path = iconv(CHARSET, 'cp936', $tfile_path);
  $tfiletext = stripslashes(ii_cstr($_POST['filetext']));
  if (file_put_contents($tfile_path, $tfiletext)) jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_edit_filedisp()
{
  $tbackurl = $_GET['backurl'];
  $tfile_path = stripslashes(ii_cstr($_POST['file_path']));
  $tfile_path = iconv(CHARSET, 'cp936', $tfile_path);
  $tfiletext = stripslashes(ii_cstr($_POST['filetext']));
  if (file_put_contents($tfile_path, $tfiletext)) jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_delete_filedisp()
{
  $tbackurl = $_GET['backurl'];
  $tfile_path = stripslashes(ii_cstr($_GET['file_path']));
  $tfile_path = iconv(CHARSET, 'cp936', $tfile_path);
  @unlink($tfile_path);
  mm_client_redirect($tbackurl);
}

function jtbc_cms_admin_manage_count_foldersizedisp()
{
  $tfloder = stripslashes(ii_cstr($_GET['folder']));
  $tfloder = iconv(CHARSET, 'cp936', $tfloder);
  if (is_dir($tfloder)) $tsize = ii_get_dirsize($tfloder);
  else $tsize = 0;
  $tsize = ii_csize($tsize);
  echo $tsize;
  exit();
}

function jtbc_cms_admin_manage_uploaddisp()
{
  $tbackurl = $_GET['backurl'];
  $tpath = stripslashes(ii_cstr($_GET['path']));
  $tpath = iconv('cp936', CHARSET, realpath($tpath));
  if (!ii_isnull($tpath))
  {
    if (ii_left($tpath, 1) == '/')
    {
      if (ii_right($tpath, 1) != '/') $tpath .= '/';
    }
    else
    {
      if (ii_right($tpath, 1) != '\\') $tpath .= '\\';
    }
    $tfilesize = ii_get_num($_FILES['file1']['size']);
    if ($tfilesize > 0)
    {
      $tfilename = $_FILES['file1']['name'];
      $tfilename = iconv(CHARSET, 'cp936', $tpath . $tfilename);
      $tmp_filename = $_FILES['file1']['tmp_name'];
      if (move_uploaded_file($tmp_filename, $tfilename)) mm_client_redirect($tbackurl);
      else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_action()
{
  switch($_GET['action'])
  {
    case 'add_folder':
      jtbc_cms_admin_manage_add_folderdisp();
      break;
    case 'edit_folder':
      jtbc_cms_admin_manage_edit_folderdisp();
      break;
    case 'delete_folder':
      jtbc_cms_admin_manage_delete_folderdisp();
      break;
    case 'add_file':
      jtbc_cms_admin_manage_add_filedisp();
      break;
    case 'edit_file':
      jtbc_cms_admin_manage_edit_filedisp();
      break;
    case 'delete_file':
      jtbc_cms_admin_manage_delete_filedisp();
      break;
    case 'count_foldersize':
      jtbc_cms_admin_manage_count_foldersizedisp();
      break;
    case 'upload':
      jtbc_cms_admin_manage_uploaddisp();
      break;
  }
}

function jtbc_cms_admin_manage_add_folder()
{
  $tmpstr = ii_ireplace('manage.add_folder', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_edit_folder()
{
  $tmpstr = ii_ireplace('manage.edit_folder', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_add_file()
{
  $tmpstr = ii_ireplace('manage.add_file', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_edit_file()
{
  $tmpedittype = '.asp.aspx.css.cfm.htm.html.ini.inc.jtbc.jsp.jspa.js.jtml.php.phtml.shtml.txt.vbs.xml.xsl.xslt';
  $tmptypestr = stripslashes(ii_cstr($_GET['file_path']));
  $tmptypestr = ii_get_lrstr($tmptypestr, '.', 'right');
  if (!ii_cinstr($tmpedittype, $tmptypestr, '.')) mm_client_alert(ii_itake('manage.cannot', 'lng'), -1);
  $tmpstr = ii_ireplace('manage.edit_file', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_list()
{
  global $nshow_path, $nshow_path_str;
  $tfloders = Array();
  $tfiles = Array();
  $twebdir = dir($nshow_path);
  while($tentry = $twebdir -> read())
  {
    if (is_file($nshow_path . $tentry)) $tfiles[iconv('cp936', CHARSET, $tentry)] = array(filesize($nshow_path . $tentry), filemtime($nshow_path . $tentry));
    else if (!(is_numeric(strpos($tentry, '.')))) $tfloders[iconv('cp936', CHARSET, $tentry)] = iconv('cp936', CHARSET, $tentry);
  }
  $twebdir -> close();
  $tmpstr = ii_ireplace('manage.list', 'tpl');
  $tmpstr = str_replace('{$path}', ii_htmlencode(iconv('cp936', CHARSET, realpath($nshow_path))), $tmpstr);
  $tmpstr = str_replace('{$foldercount}', count($tfloders), $tmpstr);
  $tmpstr = str_replace('{$filescount}', count($tfiles), $tmpstr);
  $tmprstr = '';
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  foreach ($tfloders as $key => $val)
  {
    $tmptstr = str_replace('{$nfoldername}', $key, $tmpastr);
    $tmptstr = str_replace('{$nfoldernamestr}', ii_encode_scripts($key), $tmptstr);
    $tmptstr = str_replace('{$nfolderpath}', urlencode($nshow_path_str . $key), $tmptstr);
    $tmptstr = str_replace('{$nfolderpaths}', $nshow_path_str . $key, $tmptstr);
    $tmprstr .= $tmptstr;
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmprstr = '';
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_idb}');
  foreach ($tfiles as $key => $val)
  {
    $tmptstr = str_replace('{$nfilename}', $key, $tmpastr);
    $tmptstr = str_replace('{$nfilenamestr}', ii_encode_scripts($key), $tmptstr);
    $tmptstr = str_replace('{$nftype}', ii_fileico($key), $tmptstr);
    $tmptstr = str_replace('{$nfiletime}', date('Y-m-d G:i:s', $val[1]), $tmptstr);
    $tmptstr = str_replace('{$nfilesize}', ii_csize($val[0]), $tmptstr);
    $tmptstr = str_replace('{$nfilepath}', urlencode($nshow_path_str . $key), $tmptstr);
    $tmptstr = str_replace('{$nfilepaths}', $nshow_path_str . $key, $tmptstr);
    $tmprstr .= $tmptstr;
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'add_folder':
      return jtbc_cms_admin_manage_add_folder();
      break;
    case 'edit_folder':
      return jtbc_cms_admin_manage_edit_folder();
      break;
    case 'add_file':
      return jtbc_cms_admin_manage_add_file();
      break;
    case 'edit_file':
      return jtbc_cms_admin_manage_edit_file();
      break;
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
