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
    if(!isset($_GET["IdZlecenie"]) || (trim($_GET["IdZlecenie"]) == "") || !is_numeric($_GET["IdZlecenie"]) || !trim(preg_match('/^[0-9]+$/', $_GET["IdZlecenie"]))){
        print("<p class='msg error'>Nie można edytować danych zlecenia, ponieważ nie została ona wybrana</p>");
        die("<p><a href='zlecenie_admin_tabela.php' class='bck'>Powrót do wykazu zleceń</a></p>");
    }else{
        $server = "DESKTOP-BK5MMO6\SQL1A";

        $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

        //Łączenie z serwerem
        $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

        if($polaczenie == false){
            print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
            die(print_r(sqlsrv_errors(), true));
        }else{
            $IdZlecenie = trim($_GET["IdZlecenie"]);

            $komenda_sql = "EXECUTE dbo.Zlecenie_Dane_Edycja $IdZlecenie;";

            $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
            
            if(sqlsrv_has_rows($zbior_wierszy) == false){
                print("<p class='msg error'>W bazie danych nie ma zapisanych danych brygady 
                        o identyfikatorze <strong>$IdZlecenie<strong>.</p>");
            }else{
                while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                    $IdZlecenie = $wiersz["IdZlecenie"];
                    $IdBrygada = $wiersz["IdBrygada"];
                    $IdStatusZlecenia = $wiersz["IdStatusZlecenia"];
                    $StatusZleceniaNazwa = $wiersz["StatusZleceniaNazwa"];
                    $IdKlienta = $wiersz["IdKlienta"];
                    $DaneKlienta = $wiersz["DaneKlienta"];
                    $IdObiekt = $wiersz["IdObiekt"]; 
                    $Obiekt = $wiersz["Obiekt"]; 
                    $Specjalizacja = $wiersz["Specjalizacja"];
                    $DataRozpoczecia = $wiersz["DataRozpoczecia"]->format("Y-m-d"); 
                    $TerminRealizacji = $wiersz["TerminRealizacji"]->format("Y-m-d");
                    if($wiersz["DataZakonczenia"] != ''){
                    $DataZakonczenia = $wiersz["DataZakonczenia"]->format("Y-m-d"); 
                    }else{
                    $DataZakonczenia = null;
                    }
                    $Cena = $wiersz["Cena"];
                    $Uwagi = $wiersz["Uwagi"];
                }

                if($zbior_wierszy != null){
                    sqlsrv_free_stmt($zbior_wierszy);
                }
                
                print("<h2>Edycja danych zlecenia</h2>
                <form id='zlecenieEdytuj' method='get' action='zlecenie_admin_edytuj_potw.php'>
                <fieldset>
                    <legend>Parametry tabeli</legend>
                <p class='lbl'>
                    <label for='IdZlecenie'>IdZlecenie: </label>
                    <input id='IdZlecenie' type='text' maxlength='10' name='IdZlecenie' readonly value='$IdZlecenie'>
                </p>
                <p class='lbl'>
                <label for='Status'>Status: </label>
                <select id='Status' name='Status' size='1'>
                    <option value='$IdStatusZlecenia'>$StatusZleceniaNazwa</option>");

                    $komenda_sql_stat = "EXECUTE dbo.Status_Bez $IdStatusZlecenia;";

                    $zbior_wierszy_stat = sqlsrv_query($polaczenie, $komenda_sql_stat); 
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_stat, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdStatusZlecenia"];
                        $StatusZleceniaNazwa = $wiersz["StatusZleceniaNazwa"]; 
                        
                        print("<option value='$Id'>$StatusZleceniaNazwa</option>");
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_stat != null)
                        sqlsrv_free_stmt($zbior_wierszy_stat);
                    
        print("
            <p class='lbl'>
                <label for='Klient'>Klient: </label>
                <select id='Klient' name='Klient' size='1'>
                    <option value='$IdKlienta'>$DaneKlienta</option>");

                    $komenda_sql_kli = "EXECUTE dbo.Klient_Wyswietl_Bez $IdKlienta;";

                    $zbior_wierszy_kli = sqlsrv_query($polaczenie, $komenda_sql_kli);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_kli, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdKlient"];
                        $DaneKlienta = $wiersz["DaneKlienta"]; 
                        
                        print("<option value='$Id'>$DaneKlienta</option>");
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_kli != null)
                        sqlsrv_free_stmt($zbior_wierszy_kli);
                    
        print("
        <p class='lbl'>
                <label for='Obiekt'>Obiekt: </label>
                <select id='Obiekt' name='Obiekt' size='1'>
                    <option value='$IdObiekt'>$Obiekt</option>");

                    $komenda_sql_ob = "EXECUTE dbo.Obiekt_Wyswietl_Bez $IdObiekt;";

                    $zbior_wierszy_ob = sqlsrv_query($polaczenie, $komenda_sql_ob);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_ob, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdObiekt"];
                        $DaneObiekt = $wiersz["Obiekt"]; 
                        
                        print("<option value='$Id'>$DaneObiekt</option>");
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_ob != null)
                        sqlsrv_free_stmt($zbior_wierszy_ob);
                    
        print("
        <p class='lbl'>
                <label for='Brygada'>Brygada: </label>
                <select id='Brygada' name='Brygada' size='1'>
                    <option value='$IdBrygada'>$IdBrygada $Specjalizacja</option>");

                    $komenda_sql_bryg = "EXECUTE dbo.Brygada_Wyswietl_Bez $IdBrygada;";

                    $zbior_wierszy_bryg = sqlsrv_query($polaczenie, $komenda_sql_bryg);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_bryg, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdBrygada"];
                        $Specjalizacja = $wiersz["Specjalizacja"]; 
                        
                        print("<option value='$Id'>$Id $Specjalizacja</option>");
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_bryg != null)
                        sqlsrv_free_stmt($zbior_wierszy_bryg);
                    
        print("
            <p class='lbl'>
                <label for='DataRozpoczecia'>Data rozpoczęcia: </label>
                <input id='DataRozpoczecia' type='date' name='DataRozpoczecia' required value='$DataRozpoczecia'>
            </p>
            <p class='lbl'>
                <label for='TerminRealizacji'>Termin realizacji: </label>
                <input id='TerminRealizacji' type='date' name='TerminRealizacji' required value='$TerminRealizacji'>
            </p>
            <p class='lbl'>
                <label for='DataZakonczenia'>Data zakonczenia: </label>
                <input id='DataZakonczenia' type='date' name='DataZakonczenia' value='$DataZakonczenia'>
            </p>
            <p class='lbl'>
                <label for='Cena'>Cena: </label>
                <input id='Cena' type='text' name='Cena' required value=$Cena>
            </p>
            <p>Uwagi (opcjonalne):</p>
            <textarea name='Uwagi' id='Uwagi' maxlength='200'>$Uwagi</textarea>
        </fieldset>
                <p>
                <input type='submit' value='Zapisz'>
                <input type='reset' value='Przywróć poprzednie'>
                <a href='zlecenie_admin_tabela.php' class='btn bck'>Anuluj</a>
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