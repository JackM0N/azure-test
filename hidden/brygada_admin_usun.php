<?php
    session_name("PSIN");
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styl_admin.css" type="text/css">
    <title>Firma budowlana</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie, firma, budowlana, remontowo-budowlana">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
    <script src="skrypt.js"></script>
</head>
<body>
    <div class="main">
<?php
if (isset($_SESSION["zalogowany"]) && ($_SESSION["zalogowany"]) == false || (!isset($_SESSION["zalogowany"])) || (!isset($_SESSION["uzytkownik"]))) {
    print("<h2>Odmowa dostępu</h2>");

    $_SESSION["zalogowany"] = false;

    if (isset($_SESSION["uzytkownik"])) {
        unset($_SESSION["uzytkownik"]);
    }
    if (isset($_SESSION["Imie"])) {
        unset($_SESSION["Imie"]);
    }
    if (isset($_SESSION["Nazwisko"])) {
        unset($_SESSION["Nazwisko"]);
    }

    session_destroy();
    print("<p class='msg error'>Ta funkcja jest dostępna tylko dla zalogowoanych użytkowników</p>");
    print("<p><a href='logowanie_admin_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
} else if (($_SESSION["zalogowany"]) == true && (isset($_SESSION["uzytkownik"]))) {
    if(!isset($_GET["IdBrygada"]) || (trim($_GET["IdBrygada"]) == "") || !is_numeric($_GET["IdBrygada"]) || !trim(preg_match('/^[0-9]+$/' ,$_GET["IdBrygada"]))){
        print("<p class='msg error'>Nie można usunąć danych brygady, ponieważ nie została ona wybrana</p>");
        die("<p><a href='brygada_admin_tabela.php' class='bck'>Powrót do wykazu brygad</a></p>");
    }else{
        $IdBrygada = trim($_GET["IdBrygada"]);

        print("<h2>Usuwanie danych brygady</h2>");

        print("<p class='msg warn'>Czy na pewno usunąć dane brygady o identyfikatorze <strong>$IdBrygada</strong>?</p>");
        print("<p><a href='brygada_admin_usun_potw.php?IdBrygada=$IdBrygada' class='btn del'>Tak, usuń</a>
        <a href='brygada_admin_tabela.php' class='bck'>Nie, wróć do wykazu</a></p>");
    }
}
?>
</div>
</body>

</html>