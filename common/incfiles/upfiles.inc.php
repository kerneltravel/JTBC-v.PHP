<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function uu_get_upload_user()
{
  global $nusername, $admc_username;
  $tusername = $nusername;
  if (ii_isnull($tusername)) $tusername = $admc_username;
  if (ii_isnull($tusername)) $tusername = 'null';
  return $tusername;
}

function uu_get_upload_filename($filetype)
{
  global $upbasefname;
  if (ii_isnull($upbasefname)) $tfilename = ii_format_date(ii_now(), 20) . ii_random(2) . '.' . $filetype;
  else $tfilename = $upbasefname . ii_format_date(ii_now(), 20) . ii_random(2) . '.' . $filetype;
  return $tfilename;
}

function uu_get_upload_foldername()
{
  global $upbasefolder;
  if (ii_isnull($upbasefolder)) $tfoldername = ii_format_date(ii_now(), 2);
  else $tfoldername = $upbasefolder . '/' . ii_format_date(ii_now(), 2);
  return $tfoldername;
}

function uu_upload_create_database_note($genre, $filename, $field)
{
  global $conn;
  global $variable;
  global $nupident;
  $tdatabase = $variable['common.upload.ndatabase'];
  $tidfield = $variable['common.upload.nidfield'];
  $tfpre = $variable['common.upload.nfpre'];
  $tgenre = ii_left($genre, 50);
  $tfilename = ii_left($filename, 250);
  $tfield = ii_left($field, 250);
  $tuser = uu_get_upload_user();
  $tsqlstr = "insert into $tdatabase (" . ii_cfnames($tfpre, 'genre') . "," . ii_cfnames($tfpre, 'upident') . "," . ii_cfnames($tfpre, 'filename') . "," . ii_cfnames($tfpre, 'field') . "," . ii_cfnames($tfpre, 'user') . "," . ii_cfnames($tfpre, 'time') . ") values ('$tgenre','$nupident','$tfilename','$tfield','$tuser','" . ii_now() . "')";
  return ii_conn_query($tsqlstr, $conn);
}

function uu_upload_update_database_note($genre, $filename, $field, $id)
{
  global $conn;
  global $variable;
  global $nupident;
  $tdatabase = $variable['common.upload.ndatabase'];
  $tidfield = $variable['common.upload.nidfield'];
  $tfpre = $variable['common.upload.nfpre'];
  $tgenre = ii_left($genre, 50);
  $tfilename = ii_left($filename, 10000);
  $tfield = ii_left($field, 250);
  $tid = ii_get_num($id);
  if (ii_isnull($tfield)) return;
  $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'valid') . "=0," . ii_cfnames($tfpre, 'voidreason') . "=2 where " . ii_cfnames($tfpre, 'fid') . "=$tid and " . ii_cfnames($tfpre, 'genre') . "='$tgenre' and " . ii_cfnames($tfpre, 'upident') . "='$nupident' and " . ii_cfnames($tfpre, 'field') . "='$tfield'";
  ii_conn_query($tsqlstr, $conn);
  $tary = explode('|', $tfilename);
  foreach($tary as $key => $val)
  {
    if (!ii_isnull($val))
    {
      $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'fid') . "=$tid," . ii_cfnames($tfpre, 'valid') . "=1 where " . ii_cfnames($tfpre, 'genre') . "='$tgenre' and " . ii_cfnames($tfpre, 'upident') . "='$nupident' and " . ii_cfnames($tfpre, 'field') . "='$tfield ' and " . ii_cfnames($tfpre, 'filename') . "='$val'";
      ii_conn_query($tsqlstr, $conn);
    }
  }
}

function uu_upload_delete_database_note($genre, $idary)
{
  global $conn;
  global $variable;
  global $nupident;
  $tgenre = ii_left($genre, 50);
  $tidary = $idary;
  if (ii_cidary($tidary))
  {
    $tdatabase = $variable['common.upload.ndatabase'];
    $tidfield = $variable['common.upload.nidfield'];
    $tfpre = $variable['common.upload.nfpre'];
    $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'valid') . "=0," . ii_cfnames($tfpre, 'voidreason') . "=1 where " . ii_cfnames($tfpre, 'genre') . "='$tgenre' and " . ii_cfnames($tfpre, 'upident') . "='$nupident' and " . ii_cfnames($tfpre, 'fid') . " in ($tidary)";
    return ii_conn_query($tsqlstr, $conn);
  }
}

function uu_upload_init()
{
  global $variable;
  global $nupmaxsize, $upload_tpl_href, $upload_tpl_kong, $upload_tpl_back;
  global $upform, $uptext, $upfname, $upftype, $upbasefname, $upbasefolder;
  if (ii_get_num($nupmaxsize) == 0) $nupmaxsize = ii_get_num($variable['common.nupmaxsize'], 0);
  $upload_tpl_href = ii_itake('global.tpl_upfiles.a_href_self', 'tpl');
  $upload_tpl_kong = ii_itake('global.tpl_config.html_kong', 'tpl');
  $upload_tpl_back = str_replace('{$explain}', ii_itake('global.lng_config.back', 'lng'), $upload_tpl_href);
  $upload_tpl_back = str_replace('{$value}', 'javascript:history.go(-1);', $upload_tpl_back);
  $upform = $_GET['upform'];
  $uptext = $_GET['uptext'];
  $upfname = $_GET['upfname'];
  $upftype = $_GET['upftype'];
  $upbasefname = $_GET['upbasefname'];
  $upbasefolder = $_GET['upbasefolder'];
}

function uu_upload_msg($msg)
{
  global $upload_tpl_kong, $upload_tpl_back;
  mm_clear_show(ii_ireplace('global.lng_upfiles.file_' . $msg, 'lng') . $upload_tpl_kong . $upload_tpl_back);
}

function uu_upload_files()
{
  uu_upload_init();
  global $ngenre;
  global $nupmaxsize, $nuptype, $nuppath;
  global $upform, $uptext, $upftype;
  $tfilesize = ii_get_num($_FILES['file1']['size']);
  if ($tfilesize <= 0) uu_upload_msg('null');
  if ($tfilesize > $nupmaxsize) uu_upload_msg('max');
  $tfilename = $_FILES['file1']['name'];
  $tmp_filename = $_FILES['file1']['tmp_name'];
  $tfiletype = strtolower(ii_get_filetype($tfilename));
  if (ii_cinstr($nuptype, $tfiletype, '.'))
  {
    $tfilefolder = $nuppath . uu_get_upload_foldername();
    if (!(is_dir($tfilefolder))) ii_mkdir($tfilefolder);
    $tfilename = $tfilefolder . '/' . uu_get_upload_filename($tfiletype); 
    if (move_uploaded_file($tmp_filename, $tfilename))
    {
      chmod($tfilename, 0777);
      uu_upload_create_database_note($ngenre, $tfilename, $uptext);
      mm_client_redirect('?type=upload&upform=' . $upform . '&uptext=' . $uptext . '&upftype=' . $upftype . '&upfname=' . $tfilename);
    }
    else uu_upload_msg('sudd');
  }
  else uu_upload_msg('uptype');
}

function uu_upload_files_html($strers)
{
  uu_upload_init();
  $tmpstr = ii_ireplace('global.tpl_upfiles.' . $strers, 'tpl');
  mm_clear_show($tmpstr);
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
