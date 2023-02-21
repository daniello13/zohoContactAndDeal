<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/zohoCRM.css') }}" >
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script>
        $(document).ready(function () {
            $("form").submit(function (event) {
                if( $("#createDeal").prop('checked') &&  $("#DealName").val() < 3) {
                    $("#error").text("Для угоди потрібно вказати більше символів");
                    $("#error").show();
                    $("#error").css("background-color", "red");
                    return;
                }
                if($("#LastName").val().length > 3){
                    var response = $.ajax({
                    type: "POST",
                    url: "sendData",
                    data: { 
                        _token: "{{ csrf_token() }}",
                        Contact_Last_Name: $("#LastName").val(),
                        to_create_Deal: $("#createDeal").prop('checked'),
                        Deal_Name: $("#DealName").val()
                    },
                    success: function(result) {
                        
                    },
                    error: function (jqXHR, exception) {
                        if (jqXHR.status === 0) {
                            alert('Not connect. Verify Network.');
                        } else if (jqXHR.status == 404) {
                            alert('Requested page not found (404).');
                        } else if (jqXHR.status == 500) {
                            alert('Internal Server Error (500).');
                        } else if (exception === 'parsererror') {
                            alert('Requested JSON parse failed.');
                        } else if (exception === 'timeout') {
                            alert('Time out error.');
                        } else if (exception === 'abort') {
                            alert('Ajax request aborted.');
                        } else {
                            alert('Uncaught Error. ' + jqXHR.responseText);
                        }
                        }
                    });
                    response.done(function(msg){
                        $("#error").show();
                        $("#error").text("Запит виконано");
                        $("#error").css("background-color", "green");
                        // $("#error").hide(10000);
                    });
                }
            })
        });
        </script>
    </head>
    <body class="antialiased">
        <div class="plash">
            <p> Ви були залогінені як користувач ZohoCRM </p>
            <br>
            <p> access-token: {{ $access_token }}</p> 
            <i>Додати контакт</i>
            <form action="" onSubmit="return false">
                {{ csrf_field() }}
                Вкажіть прізвище <input type="text" id="LastName" name="LastName"><br>
                Створити Угоду з ним? <input type="checkbox" id="createDeal" name="create_Deal"><br>
                Введіть назву угоди <input type="text" id="DealName" name="Deal_Name"><br>
                <span id="error"></span>
                <br>
                <input type="submit" id="create" value="Створити">
            </form>
        </div>
    </body>
</html>
