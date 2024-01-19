<?php
    session_name("USER");
    session_start();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styl_prywatne.css" type="text/css">
    <title>Zlecenia</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
</head>

<body>
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
    print("<p><a href='logowanie_uzytk_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
} else if (($_SESSION["zalogowany"]) == true && (isset($_SESSION["uzytkownik"]))) {
    if(!isset($_GET["IdZlecenie"]) || (trim($_GET["IdZlecenie"]) == "") || !is_numeric($_GET["IdZlecenie"]) || !trim(preg_match('/^[0-9]+$/', $_GET["IdZlecenie"]))){
        print("<p class='msg error'>Nie można edytować danych zlecenia, ponieważ nie zostało ono wybrane</p>");
        die("<p><a href='brygada_tabela.php' class='bck'>Powrót do wykazu zleceń</a></p>");
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

            $komenda_sql = "EXECUTE dbo.Zlecenie_Szczegoly $IdZlecenie";

            $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
            
            if(sqlsrv_has_rows($zbior_wierszy) == false){
                print("<p class='msg error'>W bazie danych nie ma zapisanych danych brygady 
                        o identyfikatorze <strong>$IdZlecenie<strong>.</p>");
            }else{
                while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                    $StatusZleceniaNazwa = $wiersz["StatusZleceniaNazwa"];
                    $IdBrygady = $wiersz["IdBrygady"];
                    $Specjalizacja = $wiersz["Specjalizacja"];
                    $ImieNazwiskoKlienta = $wiersz["ImieNazwiskoKlienta"];
                    $DaneAdresoweKlienta = $wiersz["DaneAdresoweKlienta"];
                    $DaneKontaktoweKlienta = $wiersz["DaneKontaktoweKlienta"];
                    $Obiekt = $wiersz["Obiekt"]; 
                    $NumerLokalu = $wiersz["NumerLokalu"]; 
                    $DataRozpoczecia = $wiersz["DataRozpoczecia"]->format("Y-m-d"); 
                    $TerminRealizacji = $wiersz["TerminRealizacji"]->format("Y-m-d"); 
                    if($wiersz["DataZakonczenia"] != null){
                    $DataZakonczenia = $wiersz["DataZakonczenia"]->format("Y-m-d");
                    }else{
                    $DataZakonczenia = 'Brak';
                    }
                    $Cena = number_format($wiersz["Cena"],2);
                    if($wiersz["Uwagi"]!=null){
                        $Uwagi = $wiersz["Uwagi"];
                    }else{
                        $Uwagi = 'Brak';
                    }
                }

                if($zbior_wierszy != null){
                    sqlsrv_free_stmt($zbior_wierszy);
                }
                
                print("<h2>Szczegóły zlecenia numer $IdZlecenie</h2>
                    <ul class='zle'>
                        <li>Identyfikator zlecenia: <strong>$IdZlecenie</strong></li>
                        <li>Identyfikator brygady: <strong>$IdBrygady</strong></li>
                        <li>Specjalizacja brygady: <strong>$Specjalizacja</strong></li>
                        <li>Zamawiający: <strong>$ImieNazwiskoKlienta</strong></li>
                        <li>Dane adresowe zamawiającego: <strong>$DaneAdresoweKlienta</strong></li>
                        <li>Dane kontaktowe zamawiającego: <strong>$DaneKontaktoweKlienta</strong></li>
                        <li>Obiekt zlecenia: <strong>$Obiekt</strong></li>
                        <li>Numer lokalu*: <strong>$NumerLokalu</strong></li>
                        <li>Data rozpoczęcia: <strong>$DataRozpoczecia</strong></li>
                        <li>Termin realizacji: <strong>$TerminRealizacji</strong></li>
                        <li>Data zakończenia: <strong>$DataZakonczenia</strong></li>
                        <li>Pełna kwota za wykonanie zlecenia: <strong>$Cena</strong></li>
                        <li>Status zlecenia: <strong>$StatusZleceniaNazwa</strong></li>
                        <li>Uwagi: <strong>$Uwagi</strong></li>
                    </ul>
                ");

                print("<h2>Usługi</h2>");
                print("<table class='szczegoly'>
                    <thead>
                        <tr class='uslugi'>
                            <td><a href='zlecenie_szczegoly.php?sort=UslugaNazwa' class='head'>Nazwa usługi</a></td>
                            <td><a href='zlecenie_szczegoly.php?sort=Ilosc' class='head'>Ilość</a></td>
                            <td><a href='zlecenie_szczegoly.php?sort=Cena' class='head'>Wartość usług</a></td>
                        </tr>
                    </thead>
                    <tbody>");
        
        if(!isset($_GET["sort"])){
            $sort = 'IdZlecenie';
        }else{
            $sort = $_GET["sort"];
        }

        $komenda_sql_u = "EXECUTE dbo.Usluga_Szczegoly $IdZlecenie;";

        $zbior_wierszy_u = sqlsrv_query($polaczenie, $komenda_sql_u);        
        
        if(sqlsrv_has_rows($zbior_wierszy_u) == false){
            print("<tr><td colspan = '3'>Brak danych usług w bazie</td></tr>");
        }else{
            while($wiersz = sqlsrv_fetch_array($zbior_wierszy_u, SQLSRV_FETCH_ASSOC)){
                $UslugaNazwa = $wiersz["UslugaNazwa"];
                $Ilosc = $wiersz["Ilosc"];
                $Cena = number_format($wiersz["Cena"],2);
                

                print("<tr class='uslugi'>
                            <td>$UslugaNazwa</td>
                            <td class='il'>$Ilosc</td>
                            <td>$Cena</td>   
                </tr>");
            }

            if($zbior_wierszy_u != null){
                sqlsrv_free_stmt($zbior_wierszy_u);
            }
        }

        print("</tbody>
                </table>");

                print("<h2>Materiały</h2>");
                print("<table class='szczegoly'>
                    <thead>
                        <tr class='uslugi'>
                            <td><a href='zlecenie_szczegoly.php?sort=Nazwa' class='head'>Nazwa matriału</a></td>
                            <td><a href='zlecenie_szczegoly.php?sort=CenaZaplacona' class='head'>Cena</a></td>
                            <td><a href='zlecenie_szczegoly.php?sort=WykorzystanieMaterialu' class='head'>Wykorzystanie materiału</a></td>
                        </tr>
                    </thead>
                    <tbody>");

        $komenda_sql_m = "EXECUTE dbo.Material_Szczegoly $IdZlecenie;";

        $zbior_wierszy_m = sqlsrv_query($polaczenie, $komenda_sql_m);        
        
        if(sqlsrv_has_rows($zbior_wierszy_m) == false){
            print("<tr><td colspan = '3'>Brak danych matriałów w bazie</td></tr>");
        }else{
            while($wiersz = sqlsrv_fetch_array($zbior_wierszy_m, SQLSRV_FETCH_ASSOC)){
                $MaterialNazwa = $wiersz["Nazwa"];
                $CenaZaplacona = number_format($wiersz["CenaZaplacona"],2);
                $WykorzystanieMaterialu = $wiersz["WykorzystanieMaterialu"];
                

                print("<tr class='uslugi'>
                            <td>$MaterialNazwa</td>
                            <td class='il'>$CenaZaplacona</td>
                            <td>$WykorzystanieMaterialu</td>   
                </tr>");
            }

            if($zbior_wierszy_m != null){
                sqlsrv_free_stmt($zbior_wierszy_m);
            }
        }

        print("</tbody>
                </table><br>");
            sqlsrv_close($polaczenie);
            print("<p><a href='zlecenie_uzytk_tabela.php' class='bck'>Powrót do wykazu zleceń</a></p>");
            }
        }
    }
}
?>
</body>

</html>