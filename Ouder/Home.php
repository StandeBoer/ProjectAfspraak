<?php
session_start();
require_once('../Connect.php');
if (isset($_POST["ParentLogin"])) {
    $sql = 'SELECT * FROM ouder WHERE postcode = "' . $_POST["PostalCode"] . '" AND huisnummer = ' . $_POST["HouseNumber"] . " LIMIT 1";
    $result = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($result);
    $_SESSION["PostalCode"] = $_POST["PostalCode"];
    $_SESSION["HouseNumber"] = $_POST["HouseNumber"];
    if ($count == 1) {
        while ($row = mysqli_fetch_row($result)) {
            $_SESSION["name"] = $row[1] . ' ' . $row[2] . ' ' . $row[3];
            $_SESSION["id"] = $row[0];
            $_SESSION["mail"] = $row[6];
        }
    } else {
        $_SESSION["warning"] = "Postcode of huisnummer is incorrect";
        header("Location: Login.php");
        die();
    }
} else if (isset($_SESSION["PostalCode"])) {
    $sql = 'SELECT * FROM ouder WHERE postcode = "' . $_SESSION["PostalCode"] . '" AND huisnummer = ' . $_SESSION["HouseNumber"] . " LIMIT 1";
    $result = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($result);
    if ($count != 1) {
        $_SESSION["warning"] = "U bent niet ingelogd!";
        header("Location: Login.php");
        die();
    }
}
if (!isset($_SESSION["PostalCode"])) {
    $_SESSION["warning"] = "U bent niet ingelogd!";
    header("Location: Login.php");
    die();
}

//url verdwijderen als deadline overschreden is
//if (isset($_SESSION["PostalCode"])) {
//    $sql = 'SELECT * FROM `10minutengesprek`';
//    $result = mysqli_query($connection, $sql);
//
//    $currentdate = str_replace(":", "-", date('Y:m:d'));
//    while ($row = mysqli_fetch_row($result)) {
//        $deadline = str_replace(":", "-", $row[2]);
//        $datetime1 = new DateTime($currentdate);
//        $datetime2 = new DateTime($deadline);
//        $interval = $datetime1->diff($datetime2);
//        $day = $interval->format("%R%a");
//        if ($day <= 0) {
//            $sql = 'UPDATE `10minutengesprek` SET `url`="' . "" . '" WHERE 1';
//            $result = mysqli_query($connection, $sql);
//        } else {
//            
//        }
//    }
//}

//<!--SQL voor de Ouderavond afzeggen button in Ouder/Home.php-->
$sql = "SELECT * FROM `ouderavond` WHERE ouderavond.ouderid = " . $_SESSION['id'];
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_row($result);

if (isset($_POST['OuderavondAfzeggenJa'])) {
//SQL code om aantal personen in de database op 0 te zetten
    $sql = "UPDATE `ouderavond` 
                                SET `aantalpersonen` = '0'             
                                WHERE (((ouderavond.id)=" . $_POST['OuderavondAfzeggenID'] . "));";
    $result = mysqli_query($connection, $sql);

    echo '<div class="alert alert-success" role="alert" align="center" id="flash">
                                    <h3>Afzeggen succesvol!</h3>
                                </div>';
}

//<!--SQL voor de Afspraak afzeggen button in Ouder/Home.php-->
$sql = "SELECT * FROM `tijden10minuut` WHERE tijden10minuut.ouderid = " . $_SESSION['id'];
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_row($result);

if (isset($_POST['AfspraakAfzeggenJa'])) {
//SQL code om aantal personen in de database op 0 te zetten
    $sql = "UPDATE `tijden10minuut` 
                                SET `ouderid` = '0'             
                                WHERE (((tijden10minuut.id)=" . $_POST['AfspraakAfzeggenID'] . "));";
    $result = mysqli_query($connection, $sql);
}
if (isset($_POST["verstuurbevestiging10MinutenGesprek"])) {
    $datumtijd = explode(" - ", $_POST['datumkiezen']);
//insert in sql
    $sql = 'UPDATE `tijden10minuut` SET `ouderid` = ' . $_SESSION["id"] . ' WHERE datum = "' . date("Y-m-d", strtotime($datumtijd[0])) . '" AND tijd = "' . date("H:i:s", strtotime($datumtijd[1])) . '"';
    $result = mysqli_query($connection, $sql);


    $sql = 'SELECT ouder.mail ' .
            'FROM ouder INNER JOIN (10minutengesprek INNER JOIN tijden10minuut ON 10minutengesprek.`id` = tijden10minuut.`10minutengesprekid`) ON ouder.Id = tijden10minuut.ouderid ' .
            'WHERE 10minutengesprek.url="' . $_SESSION["key"] . '"';
    $result = mysqli_query($connection, $sql);
    while ($row = mysqli_fetch_row($result)) {
        $MailAdres = $row[0];
    }
    $sql = 'UPDATE `tijden10minuut` SET `ouderid`=' . $_SESSION["id"] . " " .
            'WHERE datum = ' . $datumtijd[0] . ' AND tijd = ' . $datumtijd[1];
    $result = mysqli_query($connection, $sql);
    $EMail = "Hierbij bevestig ik dat ik kom op een tien minuten gesprek op " . $datumtijd[0] . " om " . $datumtijd[1] . "<br />";

    if (isset($_POST["Opmerkingen"])) {
        $EMail .= "Opmerkingen: " . $_POST["Opmerkingen"];
    }
    mail("afspraak@mailinator.com", "Bevestiging Ouderavond: " . $_SESSION["name"], $EMail, "From: " . $_SESSION["mail"] . "\r\nContent-type: text/html; charset = iso-8859-1");
}
if (isset($_POST["verstuurbevestigingouderavond"])) {
    if ($_POST["ConfirmRadio"] = "ja") {
        $sql = "UPDATE `ouderavond` SET `aantalpersonen` = " . $_POST["AantalPersonen"] .
                "WHERE `ouderid`=" . $_SESSION["id"];
        $result = mysqli_query($connection, $sql);
        $sql = "SELECT mail " .
                'FROM ouder INNER JOIN ouderavond ON ouder.id = ouderavond.ouderid ' .
                'WHERE ouderavond.url="' . $_SESSION["key"] . '"';
        $result = mysqli_query($connection, $sql);
        while ($row = mysqli_fetch_row($result)) {
            $MailAdres = $row[0];
        }
        $sql = 'SELECT `id`, `datum`, `tijd`, `klasid` FROM `tijdenouderavond` ORDER BY id DESC LIMIT 1 ';
        $result = mysqli_query($connection, $sql);
        while ($row = mysqli_fetch_row($result)) {
            $EMail = "Hierbij bevestig ik dat ik kom op de ouderavond van " . $row[1] . " op " . $row[2] . "<br />";

            if ($_POST["AantalPersonen"] == 1) {
                $EMail .= "Wij komen met " . $_POST["AantalPersonen"] . " persoon<br />";
            } else {
                $EMail .= "Wij komen met " . $_POST["AantalPersonen"] . " personen<br />";
            }
            if (isset($_POST["Opmerkingen"])) {
                $EMail .= "Opmerkingen: " . $_POST["Opmerkingen"];
            }
            mail("afspraak@mailinator.com", "Bevestiging 10 minuten gesprek: " . $_SESSION["name"], $EMail, "From: " . $_SESSION["mail"] . " \r\nContent-type: text/html; charset = iso-8859-1");
        }
    }
}
?>

<!--        Geef iets het id="flash" om het na een aantal seconde te laten verdwijnen       -->
<script type="text/javascript">
    window.onload = function ()
    {
        timedHide(document.getElementById('flash'), 4);
    }

    function timedHide(element, seconds)
    {
        if (element) {
            setTimeout(function () {
                element.style.display = 'none';
            }, seconds * 1000);
        }
    }
</script>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE = edge">
        <meta name="viewport" content="width = device-width, initial-scale = 1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Novaict Afspraken</title>

        <link href="../stylesheet.css" rel="stylesheet" type="text/css">
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../latofonts.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel = "stylesheet" href = "StylesheetOuder.css">

        <!--HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body style="background-color: #efefef;">
        <!-- Fixed navbar -->
        <?php include ("Navbar.php") ?>
        <?php
        if (isset($_POST["verstuurbevestigingouderavond"])) {
            echo '<div class = "alert alert-success" role = "alert" align = "center" id = "flash">
        <h3>Bevestiging succesvol verstuurd!</h3>
        </div>';
        }
        ?>
        <div class="container">
            <!--<form action="" method="post" >-->
            <div class="row">    
                <div class="col-md-1"> </div>
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Geplande afspraken</h3>
                        </div>
                        <?php
                        $sql = "SELECT docent.voornaam, docent.tussenvoegsel, docent.achternaam, tijden10minuut.datum, tijden10minuut.tijd, tijden10minuut.ouderid, tijden10minuut.id " .
                                "FROM docent INNER JOIN (10minutengesprek INNER JOIN tijden10minuut ON 10minutengesprek.Id = tijden10minuut.10minutengesprekid) ON docent.Id = 10minutengesprek.docentid " .
                                "WHERE tijden10minuut.ouderid =" . $_SESSION["id"];
                        $result = mysqli_query($connection, $sql);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-md-3">Naam docent</th>
                                    <th class="col-md-3">Datum</th>
                                    <th class="col-md-3">Tijd</th>
                                    <th class="col-md-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $resultWasEmpty = true;
                                while ($row = mysqli_fetch_row($result)) {
                                    $resultWasEmpty = false;
                                    echo "<tr>";
                                    echo "<td>" . $row[0] . " " . $row[1] . " " . $row[2] . "</td>";
                                    echo "<td>" . date("d-m-Y", strtotime($row[3])) . "</td>";
                                    echo "<td>" . date("H:i", strtotime($row[4])) . "</td>";
                                    echo '<td> <button  class = "btn btn-primary" type = "button" value = "Afzeggen" data-toggle = "modal" data-target = "#AfspraakAfzeggen">Afzeggen</button></td>';
                                    echo "</tr>";
                                }

                                if ($resultWasEmpty) {
                                    echo "<tr>";
                                    echo "<td>Er is geen afspraak ingepland</td>";
                                    echo "<td>  </td>";
                                    echo "<td> </td>";
                                    echo "</tr>";
                                }
                                ?>                             
                            </tbody>
                        </table>
                    </div>
                    <div class = "modal fade" id = "AfspraakAfzeggen" role = "dialog">
                        <form method = "post" action = "">
                            <div class = "modal-dialog">
                                <!--Modal content-->
                                <div class = "modal-content">
                                    <div class = "modal-header" style = "text-align: left">
                                        <button type = "button" class = "close" data-dismiss = "modal">&times;
                                        </button>
                                        <h4 class = "modal-title">Afspraak afzeggen</h4>
                                    </div>
                                    <div class = "modal-body" style = "text-align: left">
                                        Weet u zeker dat u de afspraak wilt afzeggen?<br>
                                    </div>
                                    <div class = "modal-footer" style = "text-align: left">
                                        <input type = "submit" name = "AfspraakAfzeggenJa" class = "btn btn-primary" style = "float: left" value = "Ja">
                                        <input type = "submit" class = "btn btn-default" style = "float: right" value = "Nee">
                                        <input type = "hidden" name = "AfspraakAfzeggenID" value = "' . $row[6] . '">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Geplande Ouderavond</h3>
                        </div>
                        <!--                    Database koppeling Geplande Ouderavond                  -->
                        <?php
                        $sql = "SELECT tijdenouderavond.datum, tijdenouderavond.tijd, ouderavond.ouderid, ouderavond.id " .
                                "FROM (ouder INNER JOIN (tijdenouderavond INNER JOIN ouderavond ON tijdenouderavond.Id = ouderavond.tijdenouderavondid) ON ouder.Id = ouderavond.ouderid) INNER JOIN student ON ouder.studentid = student.Id " .
                                'WHERE (((ouderavond.ouderid) = ' . $_SESSION["id"] . ' )) AND ouderavond.aantalpersonen > 0';

                        $result = mysqli_query($connection, $sql);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-md-4">Datum</th>
                                    <th class="col-md-5">Tijd</th>
                                    <th class="col-md-1"></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <?php
                                $resultWasEmpty = true;
                                while ($row = mysqli_fetch_row($result)) {
                                    $resultWasEmpty = false;
                                    echo "<tr>";
                                    echo "  <td>" . date("d-m-Y", strtotime($row[0])) . "</td>";
                                    echo "  <td>" . date("H:i", strtotime($row[1])) . "</td>";
                                    echo '  <td><button  class = "btn btn-primary" type = "button" value = "Afzeggen" data-toggle = "modal" data-target = "#OuderavondAfzeggen">Afzeggen</button></td>';
                                    echo "</tr>";
                                    $OuderavondAfzeggenID = $row[3];
                                }

                                if ($resultWasEmpty) {
                                    echo "<tr>";
                                    echo "<td>Er is geen ouderavond ingepland</td>";
                                    echo "<td>  </td>";
                                    echo "<td> </td>";
                                    echo "</tr>";
                                }
                                ?> 
                            </tbody>
                        </table>
                    </div>
                    <div class = "modal fade" id = "OuderavondAfzeggen" role = "dialog">
                        <form method = "post" action = "">
                            <div class = "modal-dialog">
                                <!--Modal content-->
                                <div class = "modal-content">
                                    <div class = "modal-header" style = "text-align: left">
                                        <button type = "button" class = "close" data-dismiss = "modal">&times;
                                        </button>
                                        <h4 class = "modal-title">Afspraak afzeggen</h4>
                                    </div>
                                    <div class = "modal-body" style = "text-align: left">
                                        Weet u zeker dat u de afspraak wilt afzeggen?<br>
                                    </div>
                                    <div class = "modal-footer" style = "text-align: left">
                                        <input type = "submit" name = "OuderavondAfzeggenJa" class = "btn btn-primary" style = "float: left" value = "Ja">
                                        <input type = "submit" class = "btn btn-default" style = "float: right" value = "Nee">
                                        <input type = "hidden" name = "OuderavondAfzeggenID" value = "<?php echo $OuderavondAfzeggenID ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--                    <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Nieuwe afspraken</h3>
                                            </div>
                                            //<?php
//                        $sql = "SELECT docent.voornaam, docent.tussenvoegsel, docent.achternaam, tijden10minuut.datum, tijden10minuut.tijd, tijden10minuut.ouderid " .
//                                "FROM docent INNER JOIN (10minutengesprek INNER JOIN tijden10minuut ON 10minutengesprek.Id = tijden10minuut.10minutengesprekid) ON docent.Id = 10minutengesprek.docentid WHERE tijden10minuut.ouderid = 0";
//
//                        $result = mysqli_query($connection, $sql);
//                        
                    ?>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="col-md-4">Naam docent</th>
                                                        <th class="col-md-3">Datum</th>
                                                        <th class="col-md-3">Tijd</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                    <?php
//                                $resultWasEmpty = true;
//                                while ($row = mysqli_fetch_row($result)) {
//                                    $resultWasEmpty = false;
//                                    echo "<tr>";
//                                    echo "<td>" . $row[0] . " " . $row[1] . " " . $row[2] . "</td>";
//                                    echo "<td>" . date("d-m-Y", strtotime($row[3])) . "</td>";
//                                    echo "<td>" . date("H:i", strtotime($row[4])) . "</td>";
//                                    echo "</tr>";
//                                }
//
//                                if ($resultWasEmpty) {
//                                    echo "<tr>";
//                                    echo "<td>Er is geen nieuwe afspraak beschikbaar</td>";
//                                    echo "<td>  </td>";
//                                    echo "<td> </td>";
//                                    echo "</tr>";
//                                }
                    ?>                                 
                                                </tbody>
                                            </table>
                                        </div>-->
                </div>
            </div>
            <!--</form>-->
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>