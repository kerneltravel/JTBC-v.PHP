<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
header("cache-control: no-cache, must-revalidate");
header("pragma: no-cache");

function jtbc_cms_interface_list()
{
  $tkeyword = ii_get_safecode($_GET['keyword']);
  $tfid = ii_get_num($_GET['fid']);
  echo ap_review_output_note($tkeyword, $tfid, 5);
}

function jtbc_cms_interface()
{
  switch($_GET['type'])
  {
    case 'list':
      return jtbc_cms_interface_list();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
