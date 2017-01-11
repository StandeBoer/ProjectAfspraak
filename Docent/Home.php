<?php
session_start();
require_once('Functions.php');
if (isset($_POST["LoginSubmit"])) {
    $User = attempt_login($_POST["email"], $_POST["password"]);
    $_SESSION["email"] = $_POST["email"];
    $_SESSION["password"] = $_POST["password"];
} else {
    $User = attempt_login($_SESSION["email"], $_SESSION["password"]);
}

if (!$User) {
    $_SESSION["warning"] = "E-mail of wachtwoord is incorrect";
    header("Location: Login.php");
    die();
}

$sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `rol`, `id` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_row($result)) {
    $_SESSION["name"] = $row[0] . ' ' . $row[1] . ' ' . $row[2];
    $_SESSION["id"] = $row[4];
}
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
    <body style="background-color: #efefef;">
        <!-- Fixed navbar -->
        <?php include ("Navbar.php") ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="list-group">
                        <a href="Home.php" class="list-group-item active">Home</a>
                        <?php
                        $sql = 'SELECT `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_row($result)) {

                            if ($row[0] == 1 || $row[0] == 2) {
                                echo '<a href="Ouderavond.php" class="list-group-item">Ouderavond</a>';
                            }
                            ?>
                            <a href="10MinutenGesprek.php" class="list-group-item">10 Minuten gesprek</a>
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Geplande afspraken</h3>
                        </div>
                        <?php
                        $sql = "SELECT ouder.tussenvoegsel, ouder.achternaam, student.voornaam, tijden10minuut.datum, tijden10minuut.tijd, docent.mail
                                FROM student INNER JOIN (ouder INNER JOIN (docent INNER JOIN (10minutengesprek INNER JOIN tijden10minuut ON 10minutengesprek.id = tijden10minuut.10minutengesprekid) ON docent.id = 10minutengesprek.docentid) ON ouder.id = tijden10minuut.ouderid) ON student.id = ouder.studentid
                                WHERE (((docent.mail)='" . $_SESSION["email"] . "'));";
                        $result = mysqli_query($connection, $sql);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-md-3">Naam ouder</th>
                                    <th class="col-md-2">Naam student</th>
                                    <th class="col-md-2">Datum</th>
                                    <th class="col-md-2">Tijd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $resultWasEmpty = true;
                                while ($row = mysqli_fetch_row($result)) {
                                    $resultWasEmpty = false;
                                    echo "<tr>";
                                    echo "<td>" . $row[0] . "" . $row[1] . "</td>";
                                    echo "<td>" . $row[2] . "</td>";
                                    echo "<td>" . date("d-m-Y", strtotime($row[3])) . "</td>";
                                    echo "<td>" . date("H:i", strtotime($row[4])) . "</td>";
                                    echo "</tr>";
                                }

                                if ($resultWasEmpty) {
                                    echo "<tr>";
                                    echo "<td>Er zijn geen geplande afspraken</td>";
                                    echo "<td>  </td>";
                                    echo "<td> </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Geplande ouderavond</h3>
                        </div>
                        <!--                    Database koppeling Geplande Ouderavond                  -->
                        <?php
                        $sql = "SELECT tijdenouderavond.datum, tijdenouderavond.tijd, tijdenouderavond.klasid
                                FROM tijdenouderavond;";
                        $result = mysqli_query($connection, $sql);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-md-3">Datum</th>
                                    <th class="col-md-3">Tijd</th>
                                    <th class="col-md-3">Klas</th>
                                </tr>
                            </thead>
                            <tbody>                              
                                <?php
                                $resultWasEmpty = true;
                                while ($row = mysqli_fetch_row($result)) {
                                    $_SESSION["klassen"] = "";
                                    $resultWasEmpty = false;
                                    echo "<tr>";
                                    echo "<td>" . date("d-m-Y", strtotime($row[0])) . "</td>";
                                    echo "<td>" . date("H:i", strtotime($row[1])) . "</td>";
                                    $klassen = explode(",", $row[2]);

                                    foreach ($klassen as $klas) {
                                        $sql2 = "SELECT klasnaam FROM `klas` WHERE id=$klas";
                                        $result2 = mysqli_query($connection, $sql2);
                                        while ($row2 = mysqli_fetch_row($result2)) {
                                            $_SESSION["klassen"] = $_SESSION["klassen"] . $row2[0] . ",";
                                        }
                                    }
                                    $_SESSION["klassen"] = substr($_SESSION["klassen"], 0, -1);
                                    echo "<td>" . $_SESSION["klassen"] . "</td>";


                                    echo "</tr>";
                                }

                                if ($resultWasEmpty) {
                                    echo "<tr>";
                                    echo "<td>Er is geen ouderavond ingepland</td>";
                                    echo "<td> </td>";
                                    echo "<td> </td>";
                                    echo "</tr>";
                                }
                                ?>
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

