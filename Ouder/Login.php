<?php
session_start();
require_once('../Connect.php');
if (isset($_POST["ParentLogin"])) {
    $sql = 'SELECT * FROM ouder WHERE postcode = "' . $_POST["PostalCode"] . '" AND huisnummer = ' . $_POST["HouseNumber"] . " LIMIT 1";
    $result = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($result);
    if ($count == 1) {
        while ($row = mysqli_fetch_row($result)) {
            $_SESSION["name"] = $row[1] . ' ' . $row[2] . ' ' . $row[3];
        }
    } else {
        header("Location: Login.php");
        die();
    }
}
if (isset($_POST["Logout"])) {
    session_start();
    session_destroy();
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
    </head>
    <body  style="background-color: #efefef;">
        <!-- Fixed navbar -->
        <?php include ("NavbarBase.php") ?>

        <div class="container">
            <div class="row">
                <div class="col-md-8" style="float: none; margin-left: auto; margin-right: auto;">
                    <div class="panel panel-default">
                        <div class="panel-heading">Inloggen</div>
                        <div class="panel-body">
                            <?php
                            if (isset($_SESSION["from"])) {
                                $from = $_SESSION["from"];
                            } else {
                                $from = "Home";
                            }
                            ?>
                            <form method="POST" action="<?php echo $from ?>.php">
                                <div class = "form-group">
                                    <label for = "inputEmail">Postcode</label>
                                    <input type = "PostalCode" name = "PostalCode" class = "form-control" placeholder = "Postcode" value = "<?php echo @$_SESSION["PostalCode"]; ?>">
                                </div>
                                <div class = "form-group">
                                    <label for = "inputPassword">Huisnummer</label>
                                    <input type = "HouseNumber" name = "HouseNumber" class = "form-control" placeholder = "Huisnummer" value = "<?php echo @$_SESSION["HouseNumber"]; ?>">
                                </div>
                                <button type = "submit" name = "ParentLogin" class = "btn btn-primary">Inloggen</button>
                            </form>
                            <?php
                            if (@$_SESSION["warning"] != "") {
                                echo '<br><div id="flash" style="color: red" class="alert alert-danger" role="alert" align="center">';
                                echo @$_SESSION["warning"];
                                @$_SESSION["warning"] = "";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>