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
    if(!isset($_GET["IdBrygada"]) || (trim($_GET["IdBrygada"]) == "") || !is_numeric($_GET["IdBrygada"]) || !trim(preg_match('/^[0-9]+$/', $_GET["IdBrygada"]))){
        print("<p class='msg error'>Nie można edytować danych brygady, ponieważ nie została ona wybrana</p>");
        die("<p><a href='brygada_admin_tabela.php' class='bck'>Powrót do wykazu brygad</a></p>");
    }else{
        $server = "DESKTOP-BK5MMO6\SQL1A";

        $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

        //Łączenie z serwerem
        $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

        if($polaczenie == false){
            print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
            die(print_r(sqlsrv_errors(), true));
        }else{
            $IdBrygada = trim($_GET["IdBrygada"]);

            $komenda_sql = "EXECUTE dbo.Brygada_Wyswietl_IdBrygada $IdBrygada;";

            $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
            
            if(sqlsrv_has_rows($zbior_wierszy) == false){
                print("<p class='msg error'>W bazie danych nie ma zapisanych danych brygady 
                        o identyfikatorze <strong>$IdBrygada<strong>.</p>");
            }else{
                while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                    $IdBrygada = $wiersz["IdBrygada"];
                    $Brygadzista = $wiersz["IdBrygadzista"];
                    $BrygadzistaNazwiskoImie = $wiersz["BrygadzistaNazwiskoImie"];
                    $LiczbaPracownikow = $wiersz["LiczbaPracownikow"]; 
                    $Specjalizacja = $wiersz["Specjalizacja"];
                    $Uwagi = $wiersz["Uwagi"];
                }

                if($zbior_wierszy != null){
                    sqlsrv_free_stmt($zbior_wierszy);
                }
                
                print("<h2>Edycja danych brygady</h2>
                <form id='brygadaEdytuj' method='get' action='brygada_admin_edytuj_potw.php'>
                <fieldset>
                    <legend>Parametry tabeli</legend>
                    <p class='lbl'>
                        <label for='IdBrygada'>IdBrygada: </label>
                        <input id='IdBrygada' type='text' maxlength='10' name='IdBrygada' value='$IdBrygada' readonly>
                    </p>
                    <p class='lbl'>
                        <label for='Brygadzista'>Brygadzista: </label>
                        <select id='Brygadzista' name='Brygadzista' size='1'>
                            <option value='$Brygadzista'>$BrygadzistaNazwiskoImie</option>");
                    
                        $komenda_sql_bryg = "EXECUTE dbo.Pracownik_Wyswietl_Brygadzista_BezId $Brygadzista;";

                        $zbior_wierszy_bryg = sqlsrv_query($polaczenie, $komenda_sql_bryg);        
                                
                        while($wiersz = sqlsrv_fetch_array($zbior_wierszy_bryg, SQLSRV_FETCH_ASSOC)){
                            $IdPracownik = $wiersz["IdPracownik"];
                            $NazwiskoImie = $wiersz["NazwiskoImie"]; 
                            
                            print("<option value='$IdPracownik'>$NazwiskoImie</option>");
                        }

                print("
                    </select>
                    </p>
                    <p class='lbl'>
                        <label for='LiczbaPracownikow'>Liczba pracowników: </label>
                        <input id='LiczbaPracownikow' type='text' maxlength='3' name='LiczbaPracownikow' required pattern='[0-9]*' value='$LiczbaPracownikow'>
                    </p>
                    <p class='lbl'>
                        <label for='Specjalizacja'>Specjalizacja: </label>
                        <input id='Specjalizacja' type='text' maxlength='50' name='Specjalizacja' required value='$Specjalizacja'>
                    </p>
                    <p>Uwagi (opcjonalne):</p>
                    <textarea name='Uwagi' id='Uwagi' maxlength='200'>$Uwagi</textarea>
                </fieldset>
                <p>
                <input type='submit' value='Zapisz'>
                <input type='reset' value='Przywróć poprzednie'>
                <a href='brygada_admin_tabela.php' class='btn bck'>Anuluj</a>
                </p>
            </form>");
            sqlsrv_close($polaczenie);
            }
        }
    }
}
?>
</div>
</body>

</html>