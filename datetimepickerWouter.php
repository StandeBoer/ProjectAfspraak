<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Nam</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-datetimepicker.css" rel="stylesheet">

        <style type="text/css">
            .remove {
                margin-top: 3px;
                cursor: pointer;
            }
        </style>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Tijden toevoegen</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' name="begindatum" class="form-control" placeholder="Datum" value="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>									
                                </div>
                                <div class="col-md-2">
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' name="begindatum" class="form-control" placeholder="Tijd" value="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>									
                                </div>
                                <div class="col-md-2">
                                    <button id="addTime" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Toevoegen</button>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-group" id="items">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>					
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>

        <script src="js/moment.js"></script>
        <script src="js/bootstrap-datetimepicker.min.js"></script>
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
                    $("#items").prepend('<li class="list-group-item" data-date="' + date + '" data-time="' + time + '">' + date + ' @ ' + time + '<span class="glyphicon glyphicon-remove pull-right remove" aria-hidden="true"></span></li>');

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