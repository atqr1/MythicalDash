<?php
require("../../core/require/sql.php");
header('Content-type: application/json');
ini_set("display_errors", 0);


if (!$cpconn->ping()) {
    $data = array(
        "error" => "Host can't reach the MySQL db",
        "status" => array(
            "client" => "Unknown",
            "panel" => "Unknown",
            "mysql" => "FAILED"
        )
    );
    header('HTTP/1.1 500 MYSQL ERROR');
    die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}



if (!isset($_GET['api_key'])) {
    $data = array(
        "error" => "No API key was specified"
    );
    header('HTTP/1.1 402 NO API KEY');

    die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$apikey = $cpconn->query("SELECT * FROM api_keys WHERE skey = '".mysqli_real_escape_string($cpconn, $_GET['api_key'])."'");

if ($apikey->num_rows == 0) {
    $data = array(
        "error" => "API key can't be found"
    );
    header('HTTP/1.1 401 BAD API KEY');

    die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if (!isset($_GET['email'])) {
    $data = array(
        "error" => "Some information was not given"
    );
    header('HTTP/1.1 403 MISSING INFO');

    die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$user = $cpconn->query("SELECT * FROM users WHERE email = '".mysqli_real_escape_string($cpconn, $_GET['email'])."'");

if ($user->num_rows == 0) {
    $data = array(
        "error" => "The info was not found in the db or the API dose not exist"
    );
    header('HTTP/1.1 404 NOT FOUND');

    die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$user = $user->fetch_object();
$llg = date("Y-m-d H:i:s", $user->last_login); 

$data = array(
    "error" => null,
    "user" => array(
        "username" => $user->username,
        "first_name" => $user->first_name,
        "last_name" => $user->last_name,
        "email" => $user->email,
        "info" => array(
            "database_id" => $user->id,
            "user_id" => $user->user_id,
            "afk_min" => $user->minutes_idle,
            "register_ip" => $user->register_ip,
            "lastlogin_ip" => $user->lastlogin_ip,
            "lastlogin" => $llg,
            "banned" => $user->banned == "1" ? "Yes" : "No",
            "banned_reason" => $user->banned_reason
        ),
        "preferences" => array(
            "role" => $user->role,
            "visibility" => $user->visibility,
            "avatar" => $user->avatar,
            "background" => $user->background,
            "about_me" => $user->aboutme
        ),
        "resources" => array(
            "coins" => $user->coins,
            "memory" => $user->memory,
            "disk" => $user->disk_space,
            "ports" => $user->ports,
            "databases" => $user->databases,
            "backups" => $user->backup_limit,
            "cpu" => $user->cpu,
            "server_limit" => $user->server_limit
        ),
        "secret" => array(
            "password" => $user->password,
            "session_id" => $user->session_id
        ),
    )
);
header('HTTP/1.1 200 OK');
die(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>