<?php
include(__DIR__ . '/../requirements/page.php');
if (isset($_GET['getcoins'])) {
    $coins = $userdb['coins'];
    $idlemins = $userdb['minutes_afk'];
    $lastseen = $userdb['last_seen'];

    function minutesToSeconds($minutes)
    {
        return $minutes * 60;
    }
    $minutes = $settings['afk_min'];
    $seconds = minutesToSeconds($minutes);

    $idlecheck = $lastseen + $seconds;

    $currenttime = new DateTime();
    $currenttimestamp = $currenttime->getTimestamp();


    if ($idlecheck <= $currenttimestamp) {
        $data1 = $coins + $settings['afk_coins_per_min'];
        $data2 = $idlemins + $settings['afk_min'];
        try {
            $conn->query("UPDATE `mythicaldash_users` SET `coins` = '$data1' WHERE `mythicaldash_users`.`api_key` = '" . $_COOKIE['token'] . "';");
            $conn->query("UPDATE `mythicaldash_users` SET `minutes_afk` = '$data2' WHERE `mythicaldash_users`.`api_key` = '" . $_COOKIE['token'] . "';");
            $conn->query("UPDATE `mythicaldash_users` SET `last_seen` = '$currenttimestamp' WHERE `mythicaldash_users`.`api_key` = '" . $_COOKIE['token'] . "';");
            echo '<script>window.location.replace("/earn/afk");</script>';
        } catch (Exception $e) {
            echo $e;
        }

    } else {
        header('location: /earn/afk?e=Please do not abuse');
        die();
    }
} else {
    function minutesToSeconds($minutes)
    {
        return $minutes * 60;
    }
    $minutes = $settings['afk_min'];
    $seconds = minutesToSeconds($minutes);
    $nseconds = $seconds + 1;
}
?>
<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-semi-dark"
    data-assets-path="<?= $appURL ?>/assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <?php include(__DIR__ . '/../requirements/head.php'); ?>
    <title>
        <?= $settings['name'] ?> | AFK
    </title>
    <meta http-equiv="refresh" content="<?= $nseconds ?>;" />
    <link rel="stylesheet" href="<?= $appURL ?>/assets/vendor/css/pages/page-help-center.css" />
</head>

<body>
    <div id="preloader" class="discord-preloader">
        <div class="spinner"></div>
    </div>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include(__DIR__ . '/../components/sidebar.php') ?>
            <div class="layout-page">
                <?php include(__DIR__ . '/../components/navbar.php') ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Earn /</span> AFK</h4>
                        <?php include(__DIR__ . '/../components/alert.php') ?>
                        <div class="row">
                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <div class="card-title text-center">AFK</div>
                                    </div>
                                    <div class="card-body text-center">
                                        <p>For every minute you idle here, you get one coin. With those coins that you
                                            earn,
                                            you can purchase things from the shop. </p>
                                        <p>You currently have
                                            <?= $userdb['coins'] ?> coin(s)!
                                        </p>
                                        <p>You have been idling for
                                            <?= $userdb['minutes_afk'] ?> minute(s)!
                                        </p>
                                        <p>You will get more coins in <span id="timer"></span>!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include(__DIR__ . '/../components/footer.php') ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <?php include(__DIR__ . '/../requirements/footer.php') ?>
    <script src="<?= $appURL ?>/assets/js/dashboards-ecommerce.js"></script>
    
    <?php
    function secondsToMilliseconds($s)
    {
        return $s * 1000;
    }
    echo '<script>';
    echo "setInterval(function () { $.ajax({ url: '/earn/afk?getcoins=yes', success: function (data) { console.log(\"Earned A Coin!\"); } }); }, " . secondsToMilliseconds($nseconds) . ")";
    echo '</script>';
    ?>
    <script>
        setInterval(function () {
            $('#stats').load(location.href + " #stats>*", "")
        }, <?= secondsToMilliseconds($nseconds) ?>)
    </script>
    <script>
        var timeleft = <?= $seconds ?>;
        var Timer = setInterval(function () {
            if (timeleft <= 0) {
                timeleft = <?= $seconds ?>
        } else {
                document.getElementById("timer").innerHTML = timeleft + " second(s)";
            }
            timeleft -= 1;
        }, 1000);
    </script>
</body>

</html>