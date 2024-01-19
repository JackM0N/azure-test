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
    if(!isset($_GET["IdPracownik"]) || (trim($_GET["IdPracownik"]) == "") || !is_numeric($_GET["IdPracownik"])
    || !isset($_GET["Imie"]) || (trim($_GET["Imie"]) == "")
    || !isset($_GET["Nazwisko"]) || (trim($_GET["Nazwisko"]) == "")
    || !isset($_GET["Specjalizacja"]) || (trim($_GET["Specjalizacja"]) == "")
    || !isset($_GET["NumerTelefonu"]) || (trim($_GET["NumerTelefonu"]) == "")
    || !isset($_GET["AdresEmail"]) || (trim($_GET["AdresEmail"]) == "")){
        print("<p class='msg error'> Nie można zapisać danych pracownika, ponieważ są one niekompletne lub błędne</p>");

        die("<p><a href='pracownik_admin_tabela.php' class='bck'>Powrót do wykazu pracowników</a></p>");
    }else{

        $server = "DESKTOP-BK5MMO6\SQL1A";

        $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

        //Łączenie z serwerem
        $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

        if($polaczenie == false){
            print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
            die(print_r(sqlsrv_errors(), true));
        }else{
            
            $IdPracownik = trim($_GET["IdPracownik"]);
            $Imie = trim($_GET["Imie"]); 
            $Nazwisko = trim($_GET["Nazwisko"]); 
            $Specjalizacja = trim($_GET["Specjalizacja"]); 
            $NumerTelefonu = trim($_GET["NumerTelefonu"]);
            $AdresEmail = trim($_GET["AdresEmail"]);
            $Uwagi = trim($_GET["Uwagi"]);

            $komenda_sql = "UPDATE dbo.Pracownik
                            SET Imie = '$Imie', 
                                Nazwisko = '$Nazwisko', 
                                Specjalizacja = '$Specjalizacja', 
                                NumerTelefonu = '$NumerTelefonu', 
                                AdresEmail = '$AdresEmail', 
                                Uwagi = '$Uwagi' 
                            WHERE IdPracownik = $IdPracownik;";

            $rezultat = sqlsrv_query($polaczenie, $komenda_sql);        
            
            if($rezultat == false){
                print("<p class='msg error'> Edycja danych pracownika <strong>$Imie $Nazwisko</strong> w bazie nie powiodła się.</p>");
            }else{
                print("<p class='msg success'> Dane pracownika <strong>$Imie $Nazwisko</strong> zostały zapisane w bazie.</p>");
                print("<p><a href='pracownik_admin_tabela.php' class='bck'>Powrót do wykazu pracowników</a></p>");
                if($rezultat != null){
                    sqlsrv_free_stmt($rezultat);
                }
            }

            sqlsrv_close($polaczenie);
        }
    }
}
?>
</div>
</body>

</html>