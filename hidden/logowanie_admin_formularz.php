<?php
    session_name("PSIN");
    session_start();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="firma, budowlana, remontowo-budowlana">
    <link rel="stylesheet" href="styl_prywatne.css" type="text/css">
    <title>Logowanie</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
</head>

<body>
    <?php

    print("<h2>Logowanie do serwisu jako administrator</h2>
                <form id='frmLogowanie' method='post' action='logowanie_admin_weryfikacja.php'>
                <fieldset>
                    <legend>Dane admina</legend>
                    <p class='lbl'>
                        <label for='Konto'>Nazwa konta: </label>
                        <input id='Konto' type='text' maxlength='30' name='Konto'>
                    </p>
                    <p class='lbl'>
                        <label for='Haslo'>Haslo: </label>
                        <input id='Haslo' type='password' maxlength='30' name='Haslo'>
                    </p>
                </fieldset>
                <div class='zaw'>
                <p>
                <input type='submit' value='Zaloguj'>
                <input type='reset' value='Wyczyść'>
                </p>
                </div>
            </form>");
            print("<p><a href='../stronaglowna.html' class='bck'>Powrót na stronę główną</a></p>");
    ?>
</body>

</html>