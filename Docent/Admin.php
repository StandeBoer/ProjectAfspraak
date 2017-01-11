<?php
session_start();
require_once('Functions.php');

$User = attempt_login($_SESSION["email"], $_SESSION["password"]);

$sql = 'SELECT `voornaam`, `tussenvoegsel`, `achternaam`, `rol` FROM `docent` WHERE mail = "' . $_SESSION["email"] . '"';
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_row($result)) {
    if ($row[3] == 0 || $row[3] == 1) {
        $_SESSION["warning"] = "U bent geen administrator!";
        header("Location: Login.php");
        die();
    }
}
if (!$User) {
    $_SESSION["warning"] = "U bent niet ingelogd!";
    header("Location: Login.php");
    die();
}

if (isset($_POST["NewUserSubmit"])) {
    $sql = "INSERT INTO `docent`(`voornaam`, `tussenvoegsel`, `achternaam`, `mail`, `wachtwoord`, `telefoonnummer`, `rol`, `klasid`)"
            . ' VALUES (' . '"' . $_POST["FirstName"] . '", "' . $_POST["Between"] . '", "' . $_POST["LastName"] . '", "' . $_POST["Email"] . '", "' . @password_hash($_POST["confirmcreate"], PASSWORD_DEFAULT) . '", "' . $_POST["Phone"] . '", ' . $_POST["TypeRadio"] . ', "' . $_POST["Class"] . '")';
    $result = mysqli_query($connection, $sql);
    //"Gewoon niet refreshen"
}
if (isset($_POST["EditUserSubmit"])) {
    $sql = "UPDATE `docent` SET";
    if (isset($_POST["FirstName"])) {
        $sql .= "`voornaam`=" . '"' . $_POST["FirstName"] . '"';
    }
    if (isset($_POST["Between"])) {
        $sql .= ", `tussenvoegsel`=" . '"' . $_POST["Between"] . '"';
    }
    if (isset($_POST["LastName"])) {
        $sql .= ", `achternaam`=" . '"' . $_POST["LastName"] . '"';
    }
    if (isset($_POST["Phone"])) {
        $sql .= ", `telefoonnummer`=" . '"' . $_POST["Phone"] . '"';
    }
    if (isset($_POST["Class"])) {
        $sql .= ", `klasid`=" . '"' . $_POST["Class"] . '"';
    }
    if (isset($_POST["Email"])) {
        $sql .= ", `mail`=" . '"' . $_POST["Email"] . '"';
    }
    if (isset($_POST["TypeRadio-" . $_POST["Id"]])) {
        $sql .= ", `rol`=" . $_POST["TypeRadio-" . $_POST["Id"]];
    }
    if (isset($_POST["confirm"]) && $_POST["confirm"] != "") {
        $sql .= ", `wachtwoord`=" . '"' . password_hash($_POST["confirm"], PASSWORD_DEFAULT) . '"';
    }
//Ends here WIP
    $sql .= " WHERE `docent`.`id` = " . $_POST["Id"];
    $result = mysqli_query($connection, $sql);
//Add recuired password for security
//Add change password function
}
if (isset($_POST["DeleteUserSubmit"])) {
//    if ($row[0] == $_SESSION["id"]) {
        $sql = "DELETE FROM `docent` WHERE `docent`.`id` = " . $_POST["DeleteId"];
        $result = mysqli_query($connection, $sql);
//    }
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
                            <a href="Ziekmelding.php" class="list-group-item">Ziekmelding</a>
                            <?php
                            if ($row[0] == 2) {
                                echo '<a href="Admin.php" class="list-group-item active" >Admin</a>';
                            }
                        }
                        ?>
                    </div>                    
                </div>
                <?php
                $sql = "SELECT `id`, `voornaam`, `tussenvoegsel`, `achternaam`, `mail`, `wachtwoord`, `telefoonnummer`, `rol`, `klasid` FROM `docent`";
                $result = mysqli_query($connection, $sql);
                ?>
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Gebruikers</h3>                            
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-md-5">Gebruikersnaam</th>
                                    <th class="col-md-4">Type</th>
                                    <th>     
                                        <button type="button" style="float: right" data-toggle="modal" data-target="#NewUserModal"><i class="fa fa-plus fa-md"></i></button>
                                        <div class="modal fade" id="NewUserModal" role="dialog">
                                            <!--New User Modal--><form method="post" action="">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="text-align: left">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Nieuwe gebruiker</h4>
                                                        </div>
                                                        <div class="modal-body" style="text-align: left">
                                                            Inlog<br>
                                                            <input class="form-control" type="email" name="Email" placeholder="EmailAdres" required>
                                                            <input class="form-control" type="password" id="password" placeholder="Nieuw wachtwoord" oninput="form.confirmcreate.pattern = escapeRegExp(this.value)" required>
                                                            <input class="form-control" type="password" id="password" name="confirmcreate" placeholder="Herhaal wachtwoord" required pattern="" title="Wachtwoorden moeten overeenkomen" required>
                                                            <script>
                                                                function escapeRegExp(str) {
                                                                    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
                                                                }
                                                            </script>
                                                            <hr>
                                                            Gebruikerstype<br>
                                                            <input type="radio" name="TypeRadio" value="0" checked>&nbsp;Gebruiker&nbsp;
                                                            <input type="radio" name="TypeRadio" value="1">&nbsp;Organisator&nbsp;
                                                            <input type="radio" name="TypeRadio" value="2">&nbsp;Administrator&nbsp;
                                                            <hr>
                                                            PersoonsInfo<br>
                                                            <input class="form-control" type="text" name="FirstName" placeholder="Voornaam" required>
                                                            <input class="form-control" type="text" name="Between" placeholder="Tussenvoegsel">
                                                            <input class="form-control" type="text" name="LastName" placeholder="Achternaam" required><br>
                                                            <input class="form-control" type="tel" name="Phone" placeholder="Telefoon nummer" required>
                                                            <input class="form-control" type="text" name="Class" placeholder="Klas" required>

                                                        </div>                                                       
                                                        <div class="modal-footer">                                                            
                                                            <input type="submit" name="NewUserSubmit" class="btn btn-default"  value="Versturen"></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_row($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row[1] . " " . $row[2] . " " . $row[3] ?></td>
                                        <td><?php
                                            if ($row[7] == 0) {
                                                echo "Gebruiker";
                                            }
                                            if ($row[7] == 1) {
                                                echo "Organisator";
                                            }
                                            if ($row[7] == 2) {
                                                echo "Admin";
                                            }
                                            ?> </td>
                                        <td align="right">
                                            <button type="button" data-toggle="modal" data-target="#EditUserModal-<?php echo $row[0] ?>"><i class="fa fa-pencil fa-md"></i></button>
                                            <div class="modal fade" id="EditUserModal-<?php echo $row[0] ?>" role="dialog">
                                                <!--Edit User Modal--><form method="post" action="">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: left">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Bewerk <?php echo $row[1] . " " . $row[2] . " " . $row[3] ?></h4>
                                                            </div>
                                                            <div class="modal-body" style="text-align: left">
                                                                Wachtwoord wijzigen<br>
                                                                <input class="form-control" type="password" id="password" name="Password"  placeholder="Nieuw wachtwoord" pattern="" oninput="form.confirm.pattern = escapeRegExp(this.value)">
                                                                <input class="form-control" type="password" id="password" name="confirm" placeholder="Herhaal wachtwoord" pattern="" oninput="form.Password.pattern = escapeRegExp(this.value)" title="Wachtwoorden moeten overeenkomen">
                                                                <script>
                                                                    function escapeRegExp(str) {
                                                                        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
                                                                    }
                                                                </script>
                                                                <hr>
                                                                Gebruikerstype wijzigen<br>
                                                                <?php
                                                                echo '<input type="radio" value="0" name="TypeRadio-' . $row[0] . '"';
                                                                if ($row[7] == 0 && $row[0] != $_SESSION["id"]) {
                                                                    echo "checked />&nbsp;Gebruiker&nbsp;";
                                                                } else if ($row[0] == $_SESSION["id"]) {
                                                                    echo 'disabled />&nbsp;<font color="gray">Gebruiker</font>&nbsp;';
                                                                } else {
                                                                    echo '/>&nbsp;Gebruiker&nbsp;';
                                                                }

                                                                echo '<input type="radio" value="1" name="TypeRadio-' . $row[0] . '"';
                                                                if ($row[7] == 1 && $row[0] != $_SESSION["id"]) {
                                                                    echo "checked />&nbsp;Organisator&nbsp;";
                                                                } else if ($row[0] == $_SESSION["id"]) {
                                                                    echo 'disabled />&nbsp;<font color="gray">Organisator</font>&nbsp;';
                                                                } else {
                                                                    echo '/>&nbsp;Organisator&nbsp;';
                                                                }

                                                                echo '<input type="radio" value="2" name="TypeRadio-' . $row[0] . '"';
                                                                if ($row[7] == 2 && $row[0] != $_SESSION["id"]) {
                                                                    echo "checked />&nbsp;Administrator&nbsp;";
                                                                } else if ($row[0] == $_SESSION["id"]) {
                                                                    echo 'disabled />&nbsp;<font color="gray">Administrator</font>&nbsp;';
                                                                } else {
                                                                    echo '/>&nbsp;Administrator&nbsp;';
                                                                }
                                                                ?>
                                                                <hr>
                                                                PersoonsInfo<br>
                                                                <input class="form-control" type="text" name="FirstName" placeholder="Voornaam" value="<?php echo $row[1]; ?>" required>
                                                                <input class="form-control" type="text" name="Between" placeholder="Tussenvoegsel" value="<?php echo $row[2]; ?>">
                                                                <input class="form-control" type="text" name="LastName" placeholder="Achternaam"  value="<?php echo $row[3]; ?>"required><br>
                                                                <input class="form-control" type="tel" name="Phone" placeholder="Telefoon nummer"  value="<?php echo $row[6]; ?>"required>
                                                                <input class="form-control" type="text" name="Class" placeholder="Klas" value="<?php echo $row[8]; ?>" required>
                                                                <input class="form-control" type="hidden" name="Id" value="<?php echo $row[0]; ?>">
                                                            </div>   
                                                            <div class="modal-footer">                                                            
                                                                <input type="submit" name="EditUserSubmit" class="btn btn-default"  value="Versturen"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <?php
                                            $IsAdmin = "";
                                            if ($row[0] != $_SESSION["id"]) {
                                                echo '<button type="button" data-toggle="modal" data-target="#DeleteUserModal-' . $row[0] . '" ><i class="fa fa-trash-o fa-md"></i></button>';
                                            } else {
                                                echo '<button type="button" style="opacity: 0.01; cursor: default"><i class="fa fa-trash-o fa-md"></i></button>';
                                            }
                                            ?>
                                            <div class="modal fade" id="DeleteUserModal-<?php echo $row[0] ?>" role="dialog">
                                                <!--Delete User Modal--><form method="post" action="">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: left">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Verwijder gebruiker</h4>
                                                            </div>
                                                            <div class="modal-body" style="text-align: left">
                                                                Weet u zeker dat u <?php echo $row[1] . " " . $row[2] . " " . $row[3] ?> wilt verwijderen?<br>
                                                            </div>
                                                            <div class="modal-footer" style="text-align: left">
                                                                <input type="submit" name="DeleteUserSubmit" class="btn btn-primary" style="float: left" value="Ja">
                                                                <input type="submit" class="btn btn-default" style="float: right" value="Nee">
                                                                <input type="hidden" name="DeleteId" value="<?php echo $row[0] ?>">
                                                            </div>                                                       
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>  
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>                    
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>

