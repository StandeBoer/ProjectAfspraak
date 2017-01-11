<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

if (!$User) {
    $_SESSION["warning"] = "U bent niet ingelogd!";
    header("Location: Login.php");
    die();
}

@$date = $_POST["hiddenbegindatum"];
$dateTime = new DateTime($date);
$formatted_begindatum = date_format($dateTime, 'Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Novaict Afspraken</title>

        <link href="../stylesheet.css" rel="stylesheet" type="text/css">
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../latofonts.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">

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
            <form action="SuccesvolVerstuurd10Minuten.php" method="POST">
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
                                <a href="10MinutenGesprek.php" class="list-group-item active">10 Minuten gesprek</a>
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
                        <div class="col-md-4">
                            <select class="selectpicker" name="selectouderkiezen[]" title="Ouder kiezen" data-actions-box="true" required>
                                <?php
                                $sql = 'SELECT achternaam,id FROM ouder order by achternaam';
                                echo "<button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Ouders<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
                                foreach ($connection->query($sql) as $row) {
                                    echo "<option value=$row[id]>$row[achternaam]</option>";
                                }
                                echo "</ul>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="comment">Inhoud:</label>
                                <?php
                                $sql = "SELECT ouder.voornaam, ouder.tussenvoegsel, ouder.achternaam, ouder.mail, student.voornaam, ouder.Id " .
                                        "FROM student INNER JOIN ouder ON student.Id = ouder.studentid;";
//                                echo $sql;
                                $result = mysqli_query($connection, $sql);
                                $EMail = $_SESSION["10minutenonderwerp"] . "\n\n" . $_SESSION["10minuteninhoud"] . "\n\n" . "Contactgegevens:\n" . $_SESSION["10minutennaam"] . "\n" . $_SESSION["10minutentelnr"] . "\n" . $_SESSION["10minutenemail"] . "\n";

                                while ($row = mysqli_fetch_row($result)) {
                                    $EMail = preg_replace("<<studentnaam>>", $row[4], $EMail);
                                    $EMail = str_replace("<br />", "\r\n", $EMail);
                                }
                                echo '<textarea class="form-control" rows="20" id="comment" placeholder="Voorbeeld brief" style="resize: none; cursor: not-allowed;">';
                                echo $EMail;
                                echo '</textarea>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-6 col-md-4">
                        <br><a href="DatumTijdKiezen.php"><button type="button" class="btn btn-default">← Terug naar vorige pagina</button></a>
                    </div>
                    <div class="col-md-2">
                        <br><input type="submit" name="verstuurouder" class="btn btn-primary" style=" float: right;" value="Verstuur">
                    </div>
                </div>
            </form>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
        <!-- (Optional) Latest compiled and minified JavaScript translation files -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/i18n/defaults-*.min.js"></script>
    </body>
</html>
