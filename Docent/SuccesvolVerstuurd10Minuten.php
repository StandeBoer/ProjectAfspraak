<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

$sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
$result = mysqli_query($connection, $sql);

if (!$User) {
    $_SESSION["warning"] = "U bent niet ingelogd!";
    header("Location: Login.php");
    die();
}

$sql = 'SELECT voornaam,id FROM ouder';
$token10min = sha1(uniqid($sql, true)); //----dit moet verbeterd worden, veiligheids risico!
//insert in sql
if (isset($_POST["verstuurouder"])) {
    $sql = 'UPDATE `10minutengesprek` SET `url`="' . $token10min . '" WHERE 10minutengesprek.id=' . $_SESSION["AfspraakID"];
    $result = mysqli_query($connection, $sql);
}

//$url = "http://example.com/activate.php?token=$token"; //----voor activatie, aanpassen 
$url10min = "http://localhost/Project-Afspraak/Ouder/10MinutenGesprek.php?token=$token10min";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Novaict Afspraken</title>

        <link href="../stylesheet.css" rel="stylesheet" type="text/css">
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../latofonts.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body  style="background-color: #efefef;">
        <!-- Fixed navbar -->
        <?php include ("Navbar.php") ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="list-group">
                        <a href="Home.php" class="list-group-item">Home</a>
                        <?php
                        $sql = 'SELECT `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_row($result)) {

                            if ($row[0] == 1 || $row[0] == 2) {
                                echo '<a href="Ouderavond.php" class="list-group-item">Ouderavond</a>';
                            }
                            ?>
                            <a href="10MinutenGesprek.php" class="list-group-item active" >10 Minuten gesprek</a>
                            <a href="Ziekmelding.php" class="list-group-item">Ziekmelding</a>
                            <?php
                            if ($row[0] == 2) {
                                echo '<a href="Admin.php" class="list-group-item">Admin</a>';
                            }
                        }
                        ?>
                    </div>                    
                </div>
                <div class="col-md-9">
                    <div class="alert alert-success">
                        <strong>Verzonden!</strong> Uw uitnodiging is succesvol verstuurd.
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Geadresseerde overzicht</h3>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Naam ouder</th>
                                    <th>E-mailadres</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    foreach ($_POST["selectouderkiezen"] as $Ouder) {
                                        $sql = "SELECT ouder.voornaam, ouder.tussenvoegsel, ouder.achternaam, ouder.mail, student.voornaam, ouder.Id " .
                                                "FROM student INNER JOIN ouder ON student.Id = ouder.studentid " .
                                                "WHERE ouder.id=" . $Ouder . ";";
                                        $result = mysqli_query($connection, $sql);
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row[0] . " " . $row[1] . " " . $row[2] . "</td>";
                                            echo "<td>" . $row[3] . "</td>";
                                            echo "</tr>";
                                            $EMail = $_SESSION["10minuteninhoud"];
                                            $EMail = preg_replace("<<studentnaam>>", $row[4], $EMail);
                                            $EMail = str_replace("\r\n", "<br />", $EMail);
                                            $EMail .= '<br /><br />Klik <a href="http://localhost/Project-Afspraak/Ouder/10MinutenGesprek.php?key=' . htmlentities($token10min) . '">hier</a> om de afspraak te maken';
                                            $EMail .= "<br /><br />Met vriendelijke groet,<br /><br />" . $_SESSION["contactgegevens"];

                                            // use wordwrap() if lines are longer than 70 characters
                                            $EMail = wordwrap($EMail, 70);
                                            mail($row[3], $_SESSION["10minutenonderwerp"], $EMail, "From: afspraak@novaict.nl \r\nContent-type: text/html; charset=iso-8859-1");
                                        }
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>

