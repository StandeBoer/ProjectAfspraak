<?php require_once('Functions.php'); ?>
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
        <!--herhaling om de session te destroyen-->
        <?php
        session_start();
        session_destroy();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-8" style="float: none; margin-left: auto; margin-right: auto;">
                    <div class="panel panel-default">
                        <div class="panel-heading">Inloggen</div>
                        <div class="panel-body">
                            <form action="Home.php" method="post">
                                <div class="form-group">
                                    <label for="inputEmail">E-mail</label>
                                    <input name="email" type="email" class="form-control" id="inputEmail" placeholder="E-mail" value="<?php echo @htmlentities($_SESSION["email"]); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword">Wachtwoord</label>
                                    <input name="password" type="password" class="form-control" id="inputPassword" placeholder="Wachtwoord">
                                </div>
                                <div class="checkbox">
                                    <!--Session cookie toevoegen!-->
                                </div>
                                <?php
                                if (@$_SESSION["warning"] != "") {
                                    echo '<div id="flash" style="color: red" class="alert alert-danger" role="alert" align="center">';
                                    echo @$_SESSION["warning"];
                                    @$_SESSION["warning"] = "";
                                    echo "</div>";
                                }
                                ?>

                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" >Wachtwoord vergeten</button>
                                <input type="submit" name="LoginSubmit" class="btn btn-primary" style="float: right" value="Inloggen">
                                <div id="myModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Wachtwoord vergeten</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="inputEmail">E-mail</label>
                                                    <input type="email" class="form-control" id="inputEmail" placeholder="E-mail">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-primary" style="float: left" value="Verstuur"></button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Sluit</button>
                                            </div>
                                        </div>
                                    </div>
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
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>