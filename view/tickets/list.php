<?php 
use MythicalDash\SettingsManager;
include(__DIR__ . '/../requirements/page.php');

$ticketsPerPage = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $ticketsPerPage;

$searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchCondition = '';
$ownerKeyCondition = " `ownerkey` = '" . mysqli_real_escape_string($conn, $_COOKIE['token']) . "'";
if (!empty($searchKeyword)) {
    $searchCondition = " WHERE (`subject` LIKE '%$searchKeyword%' OR `description` LIKE '%$searchKeyword%') AND" . $ownerKeyCondition;
} else {
    $searchCondition = " WHERE" . $ownerKeyCondition;
}
$statusCondition = " `status` IN ('open', 'closed')";
$tickets_query = "SELECT * FROM mythicaldash_tickets" . mysqli_real_escape_string($conn,$searchCondition) . (mysqli_real_escape_string($conn,$searchCondition) ? ' AND ' : ' WHERE ') . mysqli_real_escape_string($conn,$statusCondition) . " ORDER BY `id` LIMIT ".mysqli_real_escape_string($conn,$offset).", ".mysqli_real_escape_string($conn,$ticketsPerPage)."";
$result = $conn->query(stripslashes($tickets_query));
$totalTicketsQuery = "SELECT COUNT(*) AS total_tickets FROM mythicaldash_tickets" . mysqli_real_escape_string($conn,$searchCondition) . (mysqli_real_escape_string($conn,$searchCondition) ? ' AND ' : ' WHERE ') . mysqli_real_escape_string($conn,$statusCondition);
$totalResult = $conn->query(stripslashes($totalTicketsQuery));
$totalTickets = $totalResult->fetch_assoc()['total_tickets'];
$totalPages = ceil($totalTickets / $ticketsPerPage);
?>


<!DOCTYPE html>
<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-semi-dark"
    data-assets-path="<?= $appURL ?>/assets/" data-template="vertical-menu-template">

<head>
    <?php include(__DIR__ . '/../requirements/head.php'); ?>
    <title>
        <?= SettingsManager::getSetting("name") ?> - <?= $lang['ticket']?>
    </title>
</head>

<body>
<?php
  if (SettingsManager::getSetting("show_snow") == "true") {
    include(__DIR__ . '/../components/snow.php');
  }
  ?>
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
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><?= $lang['help_center']?> / <?= $lang['ticket']?></span></h4>

                        <?php include(__DIR__ . '/../components/alert.php') ?>
                        <div id="ads">
                            <?php
                            if (SettingsManager::getSetting("enable_ads") == "true") {
                                ?>
                                <?= SettingsManager::getSetting("ads_code") ?>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- Search Form -->
                        <form class="mt-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="<?= $lang['search']?> <?= $lang['ticket']?>..." name="search"
                                <?php $displaySearchKeyword = str_replace("%", "", $searchKeyword);?>

                                    value="<?= $displaySearchKeyword ?>">
                                <button class="btn btn-outline-secondary" type="submit"><?= $lang['search']?></button>
                            </div>
                        </form>
                        <!-- Users List Table -->
                        <div class="card">
                            <h5 class="card-header">
                                <?= $lang['ticket']?>
                                <button class="btn btn-primary float-end" data-bs-toggle="modal"
                                    data-bs-target="#createticket"><?= $lang['ticket_new'] ?></button>
                            </h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= $lang['table_id']?></th>
                                            <th><?= $lang['ticket_subject']?></th>
                                            <th><?= $lang['ticket_priority']?></th>
                                            <th><?= $lang['ticket_status']?></th>
                                            <th><?= $lang['actions']?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['id'] . "</td>";
                                                echo "<td>" . $row['subject'] . "</td>";
                                                echo "<td>" . $row['priority'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";
                                                echo "<td><a href=\"/help-center/tickets/view?ticketuuid=" . $row['ticketuuid'] . "\" class=\"btn btn-primary\">Open</a></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><br<center><td class='text-center'colspan='5'><br>".$lang['error_not_found_in_database']."<br><br>&nbsp;</td></center></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mt-4">
                                <?php
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '&search=' . $searchKeyword . '">' . $i . '</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                        <div id="ads">
                            <?php
                            if (SettingsManager::getSetting("enable_ads") == "true") {
                                ?>
                                <?= SettingsManager::getSetting("ads_code") ?>
                                <br>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php include(__DIR__ . '/../components/footer.php') ?>
                    <div class="content-backdrop fade"></div>
                    <?php include(__DIR__ . '/../components/modals.php') ?>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <?php include(__DIR__ . '/../requirements/footer.php') ?>
    <script src="<?= $appURL ?>/assets/js/app-user-list.js"></script>
</body>

</html>
