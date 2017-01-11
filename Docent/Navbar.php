<?php include ("../Connect.php"); ?>
<div class="row">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <ul class="nav nav-justified">
                    <li><a class="navbar-brand" href="Home.php"><span style="color: white;">NovaIct Afspraken</span></a></li>
                    <!--Welkom tekst met behulp van SQL en sessions-->
                    <li><a class="navbar-brand" style="margin: auto;"><span style="color: white;"><?php echo 'Welkom: ' . $_SESSION["name"]; ?></span></a></li>
                    <li><a class="navbar-brand" href="Login.php" style="float: right;"><span style="color: white;">Uitloggen </span></a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
