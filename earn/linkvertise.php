<?php 
session_start();

require("../core/require/sql.php");
require("../core/require/addons.php");

$userdb = $cpconn->query("SELECT * FROM users WHERE user_id = '" . mysqli_real_escape_string($cpconn, $_SESSION["uid"]) . "'")->fetch_array();
$getperms = $cpconn->query("SELECT * FROM roles WHERE name= '". $userdb['role']. "'")->fetch_array();
$getsettingsdb = $cpconn->query("SELECT * FROM settings")->fetch_array();
if ($getsettingsdb['disable_earning'] == "true")
{
    echo '<script>window.location.replace("/");</script>';
    $_SESSION['error'] = "You are not allowed to earn coins!";
    die;
}

if ($getsettingsdb['maintenance'] == "false")
{

}
else
{
  if ($getperms['canbypassmaintenance'] == "false")
  {
    if ($getperms['fullperm'] == "true")
    {

    }
    else
    {
      if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
      {
        $url = "https://";  
      }
      else 
      {
        $url = "http://"; 
      }         
      $url.= $_SERVER['HTTP_HOST'];                      
      echo '<script>window.location.replace("'.$url.'/auth/errors/maintenance");</script>';
      die;
    }
  }
  else
  {

  }
  
}
if ($getperms['canlinkvertise'] == "true") 
{

}
else
{
  echo '<script>window.location.replace("/");</script>';
  $_SESSION['error'] = "You are not allowed to earn coins via linkvertise";
  die;
}

if ($getsettingsdb['linkvertise_status'] == "true")
{

}
else 
{
  echo '<script>window.location.replace("/");</script>';
  $_SESSION['error'] = "You are not allowed to earn coins via linkvertise";
  die;
}


?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $getsettingsdb['name'] ?> | Linkvertise</title>
    <link rel="icon" href="<?= $getsettingsdb["logo"] ?>" type="image/png">
    <style>
        * {
    font-family: Google sans, Arial;
  }
  
  html, body {
    margin: 0;
    padding: 0;
  }
  
  .flex-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: white;
    animation: colorSlide 15s cubic-bezier(0.075, 0.82, 0.165, 1) infinite;
  }
  .flex-container .text-center {
    text-align: center;
  }
  .flex-container .text-center h1,
  .flex-container .text-center h3 {
    margin: 10px;
    cursor: default;
  }
  .flex-container .text-center h1 .fade-in,
  .flex-container .text-center h3 .fade-in {
    animation: fadeIn 2s ease infinite;
  }
  .flex-container .text-center h1 {
    font-size: 8em;
    transition: font-size 200ms ease-in-out;
    border-bottom: 1px dashed white;
  }
  .flex-container .text-center h1 span#digit1 {
    animation-delay: 200ms;
  }
  .flex-container .text-center h1 span#digit2 {
    animation-delay: 300ms;
  }
  .flex-container .text-center h1 span#digit3 {
    animation-delay: 400ms;
  }
  .flex-container .text-center button {
    border: 1px solid white;
    background: transparent;
    outline: none;
    padding: 10px 20px;
    font-size: 1.1rem;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    transition: background-color 200ms ease-in;
    margin: 20px 0;
  }
  .flex-container .text-center button:hover {
    background-color: white;
    color: #555;
    cursor: pointer;
  }
  
  @keyframes colorSlide {
    0% {
      background-color: #152a68;
    }
    25% {
      background-color: royalblue;
    }
    50% {
      background-color: seagreen;
    }
    75% {
      background-color: tomato;
    }
    100% {
      background-color: #152a68;
    }
  }
  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
    </style> 
</head>
<body>
<div class="flex-container">
  <div class="text-center">
    <h1>
      <span class="fade-in" id="digit1">Link</span>
      <span class="fade-in" id="digit2">ready</span>
    </h1>
    <h3 class="fadeIn">Please click the continue button to continue</h3>
    <?php 
        $genid = mt_rand(100000000000000, 999999999999999);
        $linkid = $genid;
        mysqli_query($cpconn, "INSERT INTO `adfoc` (`sckey`) VALUES ('".$linkid."');");
        mysqli_close($cpconn);
        $url = $getsettingsdb['proto'].$_SERVER['SERVER_NAME']."/api/adfoc/getcoins?key=".$linkid;
        echo '
        <a href="'.$url.'"><button type="button" name="button">Continue</button></a>
        ';
    ?>
  </div>
</div>
</body>
</html>
<script src="https://publisher.linkvertise.com/cdn/linkvertise.js"></script><script>linkvertise(583258, {whitelist: ["panel.mythicalnodes.xyz","status.mythicalnodes.xyz","phpmyadmin.mythicalnodes.xyz","mythicalnodes.xyz","discord.mythicalnodes.xyz"], blacklist: ["panel.f1xmc.ro","deploy.mythicalnodes.xyz"]});</script>