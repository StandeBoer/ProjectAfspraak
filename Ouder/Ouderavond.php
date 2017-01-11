<?php
session_start();
require_once('../Connect.php');
if (isset($_GET["key"])) {
    $_SESSION["key"] = $_GET["key"];
    $_SESSION["from"] = "Ouderavond";
}
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
    while ($row = mysqli_fetch_row($result)) {

        $sql = "SELECT `url` FROM `ouderavond` " .
                "WHERE ouderavond.ouderid = " . $row[0];
        echo $sql;
        $result = mysqli_query($connection, $sql);
    }
    while ($row = mysqli_fetch_row($result)) {
        if ($row[0] != $_SESSION["key"]) {
            $_SESSION["warning"] = "Dit is niet uw afspraak!";
            header("Location: Login.php");
            die();
        }
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
            <form action="Home.php" method="POST">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Afspraak bevestigen/wijzigen</h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-3">
                                <?php
                                $sql = 'SELECT `id`, `datum`, `tijd`, `klasid` FROM `tijdenouderavond` ORDER BY id DESC LIMIT 1 ';
                                $result = mysqli_query($connection, $sql);

                                while ($row = mysqli_fetch_row($result)) {
                                    echo '<label>Ouderavond ' . date("d-m-Y", strtotime($row[1])) . "</label>";
                                }
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?php
                                $sql = 'SELECT `id`, `datum`, `tijd`, `klasid` FROM `tijdenouderavond` ORDER BY id DESC LIMIT 1 ';
                                $result = mysqli_query($connection, $sql);

                                while ($row = mysqli_fetch_row($result)) {
                                    echo '<label>Tijd  ' . date("H:i", strtotime($row[2])) . "</label>";
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <!-- This is needed for putting the next elements to the next row-->
                                <label><span style="color:white;">.</span></label>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <label><input type="radio" name="ConfirmRadio[]" value="ja" checked>Ja, hierbij bevestig ik mijn komst</label>
                                <div style="margin-top: 10px;"><label>Aantal Personen: <input class="inputaantalpers" type="number" value="" name="AantalPersonen" min="0" max="5"></label></div><br>
                                <label><input type="radio" name="ConfirmRadio[]" value="nee">Nee, ik ben niet aanwezig</label> 
                            </div>
                            <div class="col-md-6">
                                <br> 
                                <label>Opmerkingen:</label>
                                <textarea name="Opmerkingen" class="form-control" rows="10" style="resize: none;"></textarea>
                            </div>
                            <div class="col-md-offset-10">
                                <input class="btn btn-primary" type="submit" name="verstuurbevestigingouderavond"  value="Verstuur bevestiging" style="margin-top: 10px; margin-right: 15px; float: right">                                
                            </div>                            
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>

