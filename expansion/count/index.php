<?php
require('../../common/incfiles/common.inc.php');
require('common/incfiles/config.inc.php');
$myid = ii_get_num($_GET['id']);
$mygenre = ii_get_safecode($_GET['genre']);
$mydatabase = mm_cndatabase(ii_cvgenre($mygenre));
$myidfield = mm_cnidfield(ii_cvgenre($mygenre));
$myfpre = mm_cnfpre(ii_cvgenre($mygenre));
if (!ii_isnull($mydatabase))
{
  $mysqlstr = "select " . ii_cfnames($myfpre, 'count') . " from $mydatabase where $myidfield=$myid";
  $myrs = @ii_conn_query($mysqlstr, $conn);
  $myrs = @ii_conn_fetch_array($myrs);
  if ($myrs)
  {
    $mysqlstr = "update $mydatabase set " . ii_cfnames($myfpre, 'count') . "=" . ii_cfnames($myfpre, 'count') . "+1 where $myidfield=$myid";
    @ii_conn_query($mysqlstr, $conn);
    echo $myrs[0];
  }
}
?>