<?php 
require("../../core/require/page.php");
$usrdb = $cpconn->query("SELECT * FROM users WHERE user_id = '" . mysqli_real_escape_string($cpconn, $_SESSION["uid"]) . "'")->fetch_array();
//Looks into perms if users has acces to see this page!
$perms = $cpconn->query("SELECT * FROM roles WHERE name='".$usrdb['role']."'")->fetch_array();

if ($perms['caneditusers'] == "true" || $perms['fullperm'] == "true")
{
  //Do nothing
}
else
{
    echo '<script>window.location.replace("/");</script>';
    die;
}

?>