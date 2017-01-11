<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

if (!$User) {
    $_SESSION["warning"] = "U bent niet ingelogd!";
    header("Location: Login.php");
    die();
}

//SQL voor docent gegevens ophalen
$sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `rol`, `id` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_row($result)) {
    $_SESSION["name"] = $row[0] . ' ' . $row[1] . ' ' . $row[2];
    $_SESSION["id"] = $row[4];
}

@$date = $_POST["deadlinedatum"];
$dateTime = new DateTime($date);
$formatted_deadline = date_format($dateTime, 'Y-m-d');

//Show info in input of current session user
if (isset($_POST["datumtijdselect"])) {
    $sql = "INSERT INTO `10minutengesprek`(`docentid`, `deadline`)"
            . ' VALUES (' . '"' . $_SESSION["id"] . '", "' . $formatted_deadline . '")';
    $result = mysqli_query($connection, $sql);
}

if (isset($_POST["datumtijdselect"])) {
    $_SESSION["10minutenonderwerp"] = $_POST["10minutenonderwerp"];
    $_SESSION["10minuteninhoud"] = $_POST["10minuteninhoud"];
    $_SESSION["10minutennaam"] = $_POST["10minutennaam"];
    $_SESSION["10minutentelnr"] = $_POST["10minutentelnr"];
    $_SESSION["10minutenemail"] = $_POST["10minutenemail"];
    $_SESSION["contactgegevens"] = $_POST["contactgegevens"];
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
    <body  style="background-color: #efefef;">
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
                            <a href="10MinutenGesprek.php" class="list-group-item active" name="10minutenmenu" active>10 Minuten gesprek</a>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Datum/tijd toevoegen</h3>
                                    </div>
                                    <div class="panel-body" align="center">
                                        <div class="row">
                                            <form action="" method="POST" >
                                                <div class="col-md-3">
                                                    <div class='input-group date' id='datetimepicker1'>
                                                        <input type='text' name="begindatum" class="form-control" placeholder="Datum" value="" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <br />
                                                    <div style="background-color: #EFEFEF;">
                                                        <?php
                                                        @$date = $_POST["hiddenbegindatum"];
                                                        @$time = $_POST["hiddenbegintijd"];
                                                        $dateTime = new DateTime($date);
                                                        $formatted_begindatum = date_format($dateTime, 'Y-m-d');
                                                        $check = $connection->query("SELECT * FROM tijden10minuut INNER JOIN 10minutengesprek ON tijden10minuut.10minutengesprekid = 10minutengesprek.id WHERE datum='$formatted_begindatum' AND tijd='$time' AND 10minutengesprek.docentid = " . $_SESSION['id']);
                                                        if (!mysqli_num_rows($check) > 0) {
                                                            if (isset($_POST["hiddenbegindatum"]) && isset($_POST["hiddenbegintijd"])) {
                                                                $_SESSION["DateTimeText"] = $_SESSION["DateTimeText"] . $_POST["hiddenbegindatum"] . " - " . $_POST["hiddenbegintijd"] . "<br />";
                                                            }
                                                        }
                                                        echo $_SESSION["DateTimeText"];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class='input-group date' id='datetimepicker2'>
                                                        <input type='text' name="begintijd" class="form-control" placeholder="Tijd" value="" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>									
                                                </div>
                                                <div class="col-md-2">
                                                    <!--<button id="addTime" type="button" name="toevoegen" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Toevoegen</button>-->
                                                    <input type="submit" name="toevoegen" id="addTime" value="Toevoegen" class="btn btn-success">
                                                    <?php
                                                    $sql = "SELECT `id` FROM `10minutengesprek`ORDER BY id DESC LIMIT 1;";
                                                    $result = mysqli_query($connection, $sql);
                                                    while ($row = mysqli_fetch_row($result)) {
                                                        $_SESSION["AfspraakID"] = $row[0];
                                                    }



                                                    //Insert date and time into database
                                                    if (isset($_POST["toevoegen"])) {
                                                        $check = $connection->query("SELECT * FROM tijden10minuut INNER JOIN 10minutengesprek ON tijden10minuut.10minutengesprekid = 10minutengesprek.id WHERE datum='$formatted_begindatum' AND tijd='$time' AND 10minutengesprek.docentid = " . $_SESSION['id']);
                                                        if (mysqli_num_rows($check) > 0) {
                                                            echo 'datum-tijd combinatie bestaat al!';
                                                        } else {
                                                            $sql = "INSERT INTO `tijden10minuut`(`10minutengesprekid`,`datum`, `tijd`)"
                                                                    . ' VALUES (' . '"' . $_SESSION["AfspraakID"] . '","' . $formatted_begindatum . '", "' . $time . '")';
                                                            $result = mysqli_query($connection, $sql);
                                                        }
                                                        if (isset($_POST["datumtijdselect"])) {
                                                            $_SESSION["10minutenonderwerp"] = $_POST["10minutenonderwerp"];
                                                            $_SESSION["10minuteninhoud"] = $_POST["10minuteninhoud"];
                                                            $_SESSION["10minutennaam"] = $_POST["10minutennaam"];
                                                            $_SESSION["10minutentelnr"] = $_POST["10minutentelnr"];
                                                            $_SESSION["10minutenemail"] = $_POST["10minutenemail"];
                                                        }
                                                    }
                                                    ?>
                                                </div>                                                
                                                <div class="col-md-12">
                                                    <br><ul class="list-inline" id="items">
                                                    </ul>
                                                </div>
                                            </form>                                            
                                        </div>                                      
                                    </div>
                                </div>
                            </div>
                            <form action="OuderKiezen.php" method="POST">
                                <div class="col-md-offset-9 col-md-3">
                                    <!--Labels needed for getting the button in position-->
                                    <label><span style="color: #EFEFEF;">.</span></label>
                                    <br><label><span style="color: #EFEFEF;">______</span></label>
                                    <input type="submit" name="ouderselect" class="btn btn-primary" value="Ouders selecteren">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/moment.js"></script>
        <script src="../js/bootstrap-datetimepicker.min.js"></script>
        <!--Script voor date en time picker-->
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
            $("#addTime").click(function () {
                var date = $("#datetimepicker1").data('date');
                var time = $("#datetimepicker2").data('date');
                if (!date) {
                    $("#datetimepicker1").addClass("has-error");
                }
                if (!time) {
                    $("#datetimepicker2").addClass("has-error");
                }
                if (date && time) {
                    // Toevoegen van de items
                    $("#items").prepend('<li class="list-group-item datepickerspace" data-date="' + date + '" data-time="' + time + '">' + date + ' || ' + time + '<span class="glyphicon glyphicon-remove pull-right remove" style="color:#F01818;" aria-hidden="true"></span></li>');
                    $("#items").prepend('<input type="hidden" name="hiddenbegindatum" value="' + date + '"><input type="hidden" name="hiddenbegintijd" value="' + time + '">');
                    // Data weghalen
                    $('#datetimepicker1').data().DateTimePicker.date(null);
                    $('#datetimepicker2').data().DateTimePicker.date(null);
                }
            });
            // Verwijderen van de items.
            $('#items').on('click', '.remove', function () {
                $(this).parent().remove();
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