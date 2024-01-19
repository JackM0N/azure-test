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
    if(!isset($_GET["IdPracownik"]) || (trim($_GET["IdPracownik"]) == "") || !is_numeric($_GET["IdPracownik"])){
        print("<p class='msg error'>Nie można edytować danych pracownika, ponieważ nie został on wybrany</p>");
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

            $komenda_sql = "SELECT IdPracownik, Imie, Nazwisko, Specjalizacja, NumerTelefonu, AdresEmail, Uwagi
                            FROM dbo.Pracownik
                            WHERE IdPracownik = $IdPracownik;";

            $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
            
            if(sqlsrv_has_rows($zbior_wierszy) == false){
                print("<p class='msg error'>W bazie danych nie ma zapisanych danych pracownika 
                        o identyfikatorze <strong>$IdPracownik<strong>.</p>");
            }else{
                while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                    $Imie = $wiersz["Imie"]; 
                    $Nazwisko = $wiersz["Nazwisko"]; 
                    $Specjalizacja = $wiersz["Specjalizacja"]; 
                    $NumerTelefonu = $wiersz["NumerTelefonu"];
                    $AdresEmail = $wiersz["AdresEmail"];
                    $Uwagi = $wiersz["Uwagi"];
                }

                if($zbior_wierszy != null){
                    sqlsrv_free_stmt($zbior_wierszy);
                }
            
                sqlsrv_close($polaczenie);
                
                print("<h2>Edycja danych pracownik</h2>
                <form id='pracownikEdytuj' method='get' action='pracownik_admin_edytuj_potw.php'>
                <fieldset>
                    <legend>Parametry tabeli</legend>
                    <p class='lbl'>
                        <label for='IdPracownik'>IdPracownik: </label>
                        <input id='IdPracownik' type='text' maxlength='10' name='IdPracownik' value='$IdPracownik' readonly>
                    </p>
                    <p class='lbl'>
                        <label for='Imie'>Imie: </label>
                        <input id='Imie' type='text' maxlength='30' name='Imie' value='$Imie'>
                    </p>
                    <p class='lbl'>
                        <label for='Nazwisko'>Nazwisko: </label>
                        <input id='Nazwisko' type='text' maxlength='50' name='Nazwisko' value='$Nazwisko'>
                    </p>
                    <p class='lbl'>
                        <label for='Specjalizacja'>Specjalizacja: </label>
                        <input id='Specjalizacja' type='text' maxlength='50' name='Specjalizacja' value='$Specjalizacja'>
                    </p>
                    <p class='lbl'>
                        <label for='NumerTelefonu'>Numer telefonu: </label>
                        <input id='NumerTelefonu' type='text' maxlength='20' name='NumerTelefonu' value='$NumerTelefonu'>
                    </p>
                    <p class='lbl'>
                        <label for='AdresEmail'>Adres e-mail: </label>
                        <input id='AdresEmail' type='text' maxlength='50' name='AdresEmail' value='$AdresEmail'>
                    </p>
                    <p>Uwagi (opcjonalne):</p>
                    <textarea name='Uwagi' id='Uwagi' maxlength='200'>$Uwagi</textarea>
                </fieldset>
                <p>
                <input type='submit' value='Zapisz'>
                <input type='reset' value='Przywróć poprzednie'>
                <a href='pracownik_admin_tabela.php' class='btn bck'>Anuluj</a>
                </p>
            </form>");
            }
        }
    }
}
?>
</div>
</body>

</html>