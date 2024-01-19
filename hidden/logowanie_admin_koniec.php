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
    print("<h2>Koniec sesji użytkownika</h2>");
    print("<p class='msg success'>Sesja użytkownika została pomyślnie zakończona.</p>");

    $_SESSION["zalogowany"] = false;

    if(isset($_SESSION["uzytkownik"])){
        unset($_SESSION["uzytkownik"]);
    }
    if(isset($_SESSION["Imie"])){
        unset($_SESSION["Imie"]);
    }
    if(isset($_SESSION["Nazwisko"])){
        unset($_SESSION["Nazwisko"]);
    }

    session_destroy();
    print("<p><a href='logowanie_uzytk_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
    print("<p><a href='../stronaglowna.html' class='bck'>Powrót na stronę główną</a></p>")
?>
</body>

</html>