<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

if (!$User) {
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
            <form action="KlasKiezenZiekmelding.php" method="POST">
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
                                <a href="10MinutenGesprek.php" class="list-group-item">10 Minuten gesprek</a>
                                <a href="Ziekmelding.php" class="list-group-item active" >Ziekmelding</a>
                                <?php
                                if ($row[0] == 2) {
                                    echo '<a href="Admin.php" class="list-group-item">Admin</a>';
                                }
                            }
                            ?>
                        </div>                    
                    </div>
                    <div class="col-md-9">
                        <div class="panel-default">
                            <div class="form-group">
                                <label for="usr">Onderwerp:</label>
                                <input type="text" class="form-control" name="ziekmeldingonderwerp" placeholder="Vul hier uw onderwerp in">
                            </div>
                            <div class="form-group">
                                <label for="comment">Inhoud:</label>
                                <textarea class="form-control" rows="10" name="ziekmeldinginhoud" placeholder="Stel hier uw brief op" style="resize: none"></textarea>
                            </div>                  
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    //Show info in input of current session user
                                    $sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `mail`, `telefoonnummer` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
                                    $result = mysqli_query($connection, $sql);

                                    while ($row = mysqli_fetch_row($result)) {
                                        $_SESSION["name"] = $row[0] . ' ' . $row[1] . ' ' . $row[2];
                                        ?>
                                        <label for="contactgegevens">Contactgegevens:</label>
                                        <input type="text" class="form-control" name="ziekmeldingnaam" placeholder="Naam" value="<?php echo $row[0] . " " . $row[1] . " " . $row[2] ?>">
                                        <input type="text" class="form-control" name="ziekmeldingtelnr" placeholder="Telefoonnummer" value="<?php echo $row[4] ?>">
                                        <input type="text" class="form-control" name="ziekmeldingemail" placeholder="E-mailadres" value="<?php echo $row[3] ?>">
                                        <input type="hidden" class="form-control" name="contactgegevens" value="<?php echo $row[0] . " " . $row[1] . " " . $row[2] . "\n" . $row[4] . "\n" . $row[3] ?>">
                                    <?php } ?>
                                </div>
                                <div class="col-md-offset-6 col-md-1" style="margin-top: 60px;">
                                    <br><br><input type="submit" name="klasselectziekmelding" class="btn btn-primary" value="Klas selecteren">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>