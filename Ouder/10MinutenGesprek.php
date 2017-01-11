<?php
session_start();
require_once('../Connect.php');
if (isset($_GET["key"])) {
    $_SESSION["key"] = $_GET["key"];
    $_SESSION["from"] = "10MinutenGesprek";
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
        <link href="StylesheetOuder.css" rel="stylesheet" type="text/css">
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
    <body  style="background-color: #efefef;">
        <?php include ("Navbar.php") ?>
        <div class="container">
            <form action="Home.php" method="POST">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Afspraak bevestigen/wijzigen</h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <!--                                Datum dropdown                      -->
                                <select class="selectpicker" multiple id="selectdatum" name="datumkiezen" data-max-options="1" title="Kies een datum en tijd">
                                    <?php
                                    $sql = "SELECT * "
                                            . "FROM 10minutengesprek INNER JOIN tijden10minuut ON `10minutengesprek`.Id = tijden10minuut.`10minutengesprekid` "
                                            . 'WHERE 10minutengesprek.url="' . $_SESSION["key"] . '"';
                                    echo "<button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'><span class='caret'></span></button>";
                                    echo "<ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
                                    foreach ($connection->query($sql) as $row) {
                                        echo "<option>" . date("d-m-Y", strtotime($row[datum])) . " - " . date("H:i", strtotime($row[tijd])) . "</option>";
                                    }
                                    echo "</ul>";
                                    ?>
                                </select>                                
                            </div>
                            <div class="col-md-6">
                                <label>Opmerkingen:</label>
                                <textarea class="form-control" rows="15" style="resize: none;"></textarea>
                            </div>
                            <div class="col-md-offset-10">
                                <input class="btn btn-primary" type="submit" name="verstuurbevestiging10MinutenGesprek"  value="Verstuur bevestiging" style="margin-top: 20px; margin-right: 15px; float: right">
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
        <!-- (Optional) Latest compiled and minified JavaScript translation files -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/i18n/defaults-*.min.js"></script>
        <script>
            $(document).on("change", '#selectdatum', function (e) {
                var selectdatum = $(this).val();

                $.ajax({
                    type: "POST",
                    data: {department: selectdatum},
                    url: 'admin/users/get_name_list.php',
                    dataType: 'json',
                    success: function (json) {

                        var $el = $("#name");
                        $el.empty(); // remove old options
                        $el.append($("<option></option>")
                                .attr("value", '').text('Please Select'));
                        $.each(json, function (value, key) {
                            $el.append($("<option></option>")
                                    .attr("value", value).text(key));
                        });
                    }
                });

            });
        </script>
    </body>
</html>
