<?php
include ("../Connect.php");

function confirm_query($result_set)
{
    if (!$result_set)
    {
        die("Database query failed.");
    }
}

function find_admin_by_username($username)
{
    global $connection;

    $safe_username = mysqli_real_escape_string($connection, $username);

    $query = "SELECT * ";
    $query .= "FROM docent ";
    $query .= "WHERE mail = '{$safe_username}' ";
    $query .= "LIMIT 1";
    $admin_set = mysqli_query($connection, $query);
//    confirm_query($admin_set);
    if ($admin = mysqli_fetch_assoc($admin_set))
    {
        return $admin;
    }
    else
    {
        return "E-Mail adres of wachtwoord is incorrect";
    }
}

function attempt_login($username, $password)
{
    $admin = find_admin_by_username($username);
    if ($admin)
    {
//        print_r($admin);
// found admin, now check password
        if (password_verify($password, $admin["wachtwoord"]))
        {
// password matches
            return $admin;
        }
        else
        {
// password does not match
            return false;
        }
    }
    else
    {
// admin not found
        return false;
    }
}

function password_check($password, $existing_hash)
{
    // existing hash contains format and salt at start
    $hash = crypt($password, $existing_hash);
    if ($hash === $existing_hash)
    {
        return true;
    }
    else
    {
        return false;
    }
}
?>