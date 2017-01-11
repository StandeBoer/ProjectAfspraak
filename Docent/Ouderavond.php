<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

$sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_row($result)) {
    if ($row[3] == 0) {
        $_SESSION["warning"] = "U bent geen organisator!";
        header("Location: Login.php");
        die();
    }
}
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
        <link href="../css/bootstrap-datetimepicker.css" rel="stylesheet">
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
            <form action="KlasKiezen.php" method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <div class="list-group">
                            <a href="Home.php" class="list-group-item">Home</a>
                            <?php
                            $sql = 'SELECT `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
                            $result = mysqli_query($connection, $sql);
                            while ($row = mysqli_fetch_row($result)) {

                                if ($row[0] == 1 || $row[0] == 2) {
                                    echo '<a href="Ouderavond.php" class="list-group-item active" >Ouderavond</a>';
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
                        <div class="panel-default">
                            <div class="form-group">
                                <label for="usr">Onderwerp:</label>
                                <input type="text" class="form-control" name="ouderavondonderwerp" placeholder="Vul hier uw onderwerp in" style="text-align: left;">
                            </div>
                            <div class="form-group">
                                <label for="comment">Inhoud:</label>
                                <textarea type="text" class="form-control" rows="10" name="ouderavondinhoud" placeholder="Stel hier uw brief op" style="resize: none"></textarea>
                            </div>                  
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    //Show name in ouderavondnaam input of current session user
                                    $sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `mail`, `telefoonnummer` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
                                    $result = mysqli_query($connection, $sql);

                                    while ($row = mysqli_fetch_row($result)) {
                                        $_SESSION["name"] = $row[0] . ' ' . $row[1] . ' ' . $row[2];
                                        ?>
                                        <label for="contactgegevens">Contactgegevens:</label>
                                        <input type="text" class="form-control" name="ouderavondnaam" placeholder="Naam" value="<?php echo $row[0] . " " . $row[1] . " " . $row[2] ?>">
                                        <input type="text" class="form-control" name="ouderavondtelnr" placeholder="Telefoonnummer" value="<?php echo $row[4] ?>">
                                        <input type="text" class="form-control" name="ouderavondemail" placeholder="E-mailadres" value="<?php echo $row[3] ?>">
                                        <input type="hidden" class="form-control" name="contactgegevens" value="<?php echo $row[0] . " " . $row[1] . " " . $row[2] . "<br />" . $row[4]  . "<br />" . $row[3]?>">
                                    <?php } ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="datum">Datum:</label>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' name="ouderavonddatum" class="form-control" required placeholder="Datum" value="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="tijd">Tijd:</label>
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' name="ouderavondtijd" class="form-control" required placeholder="Tijd" value="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-offset-1 col-md-1">
                                    <br><br><br><br><br><input type="submit" name="klasselect" class="btn btn-primary" id="klasselect" value="Klas selecteren">
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
        <script src="../js/moment.js"></script>
        <script src="../js/bootstrap-datetimepicker.min.js"></script>
        <script>
            // Instellingen voor de datetimepicker
            $('#datetimepicker1').datetimepicker({
                locale: 'nl',
                format: 'DD-MM-YYYY'
            });

            $('#datetimepicker2').datetimepicker({
                locale: 'nl',
                format: 'HH:mm'
            });

            // Toevoegen van de datums en tijden
            $("#klasselect").click(function () {

                var date = $("#datetimepicker1").data('date');
                var time = $("#datetimepicker2").data('date');

                if (!date) {
                    $("#datetimepicker1").addClass("has-error");
                }

                if (!time) {
                    $("#datetimepicker2").addClass("has-error");
                }
            });

            // Verwijderen classen:
            $("#datetimepicker1").on("dp.change", function (e) {
                $("#datetimepicker1").removeClass("has-error");
            });

            // Verwijderen classen:
            $("#datetimepicker2").on("dp.change", function (e) {
                $("#datetimepicker2").removeClass("has-error");
            });

            // Data plaatsen:
            $("#selectclass").click(function () {

                /*
                 var postData = { 
                 'begindatum': begindatum,
                 'einddatum': einddatum,
                 'agendapunt': agendapunt,
                 'agendatekst': agendatekst
                 };
                 */

                postData = {};
                teller = 0;

                $('#items li').each(function () {
                    var date = $(this).data("date");
                    var time = $(this).data("time");
                    postData[teller] = date + "@" + time;
                    teller++;
                });

                alert(postData);

                var url = "post.php";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: postData,
                    dataType: "text",
                    success: function (data)
                    {
                        alert(data);
                    }
                });
            })
        </script>
    </body>
</html>

