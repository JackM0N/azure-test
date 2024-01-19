<?php
    session_name("USER");
    session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styl_prywatne.css" type="text/css">
    <title>Zlecenia</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
    <script src="skrypt.js"></script>
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
    $server = "DESKTOP-BK5MMO6\SQL1A";

    $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

    //Łączenie z serwerem
    $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

    if($polaczenie == false){
        print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
        die(print_r(sqlsrv_errors(), true));
    }else{
        print("<h2>Twoje zamówienia</h2>");
        print("<table>
                    <thead>
                        <tr>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=IdZlecenie' class='head'>Identyfikator</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=StatusZlecenieNazwa' class='head'>Status</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=DaneKlienta' class='head'>Dane klienta</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=Obiekt' class='head'>Obiekt</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=DataRozpoczecia' class='head'>Data rozpoczęcia</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=TerminRealizacji' class='head'>Termin realizacji</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=DataZakonczenia' class='head'>Data zakończenia</a></td>
                            <td><a href='zlecenie_uzytk_tabela.php?sort=Cena' class='head'>Wartość [PLN]</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>");
        
        if(!isset($_GET["sort"])){
            $sort = 'IdZlecenie';
        }else{
            $sort = $_GET["sort"];
        }

        $Id = $_SESSION["IdKlient"];

        $komenda_sql = "EXECUTE dbo.Zlecenie_Dane_Uzytk $Id;";

        $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
        
        if(sqlsrv_has_rows($zbior_wierszy) == false){
            print("<tr><td colspan = '9'>Brak danych zamówień w bazie</td></tr>");
        }else{
            while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                $IdZlecenie = $wiersz["IdZlecenie"];
                $StatusZleceniaNazwa = $wiersz["StatusZleceniaNazwa"]; 
                $DaneKlienta = $wiersz["DaneKlienta"]; 
                $Obiekt = $wiersz["Obiekt"]; 
                $DataRozpoczecia = $wiersz["DataRozpoczecia"]; 
                $TerminRealizacji = $wiersz["TerminRealizacji"]; 
                $DataZakonczenia = $wiersz["DataZakonczenia"]; 
                $Cena = number_format($wiersz["Cena"],2,"."," ");
                

                print("<tr>
                            <td>$IdZlecenie</td>
                            <td>$StatusZleceniaNazwa</td>
                            <td>$DaneKlienta</td>
                            <td>$Obiekt</td>
                            <td>");
                            print(date_format($DataRozpoczecia,'Y-m-d'));
                            print("</td>
                            <td>");
                            print(date_format($TerminRealizacji,'Y-m-d'));
                            print("</td>");
                            if($DataZakonczenia != null){
                                print("<td>");
                                print(date_format($DataZakonczenia,'Y-m-d'));
                            }else{
                                print("<td>Brak");    
                            }
                            print("</td>
                            <td>$Cena</td>
                            <td><a href='zlecenie_uzytk_edytuj.php?IdZlecenie=$IdZlecenie' class='btn upd'>Edytuj</a></td>
                            <td><a href='zlecenie_uzytk_szczegoly.php?IdZlecenie=$IdZlecenie' class='btn info'>Szczegóły</a></td>             
                </tr>");
            }

            if($zbior_wierszy != null){
                sqlsrv_free_stmt($zbior_wierszy);
            }
        }

        print("</tbody>
                </table>");

        print("<button type='button' onclick='zamiana();' id='butn'>+ Nowe zlecenie</button>");
        print("<p><br><a class='bck' href='logowanie_uzytk_koniec.php' id='butn2'>Wyloguj</a></p>");
        print("<div id='dodawanieForm'>");
        print("<h2>Nowe zlecenie</h2>
        <form id='zlecenieDodaj' method='get' action='zlecenie_uzytk_dodaj.php'>
        <fieldset>
            <legend>Parametry tabeli</legend>
            <p class='lbl'>
                <label for='IdZlecenie'>IdZlecenie: </label>");
            
                $komenda_sql_id = "EXECUTE dbo.IdZlecenie_Wybierz;";

                $zbior_wierszy_id = sqlsrv_query($polaczenie, $komenda_sql_id);

                while($wiersz = sqlsrv_fetch_array($zbior_wierszy_id, SQLSRV_FETCH_ASSOC)){
                    $Ilosc = $wiersz["Ilosc"];
                    $Id = $Ilosc +1; 
                    
                    print("<input id='IdZlecenie' type='text' maxlength='10' name='IdZlecenie' value='$Id' readonly>");
                } 

            print("
            </p>
            <p class='lbl'>
                <label for='Status'>Status: </label>
                <select id='Status' name='Status' size='1'>
                    <option value='-1'>Wybierz status...</option>");

                    $komenda_sql_stat = "EXECUTE dbo.Status_Wyswietl;";

                    $zbior_wierszy_stat = sqlsrv_query($polaczenie, $komenda_sql_stat);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_stat, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdStatusZlecenia"];
                        $Status = $wiersz["StatusZleceniaNazwa"]; 
                        
                        print("<option value='$Id'>$Status</option>");
                        break;
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_stat != null)
                        sqlsrv_free_stmt($zbior_wierszy_stat);
                    
        print("
        <p class='lbl'>
                <label for='Obiekt'>Obiekt: </label>
                <select id='Obiekt' name='Obiekt' size='1'>
                    <option value='0'>Wybierz obiekt...</option>");

                    $komenda_sql_ob = "EXECUTE dbo.Obiekt_Wyswietl;";

                    $zbior_wierszy_ob = sqlsrv_query($polaczenie, $komenda_sql_ob);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_ob, SQLSRV_FETCH_ASSOC)){
                        $Id = $wiersz["IdObiekt"];
                        $DaneObiekt = $wiersz["DaneObiekt"]; 
                        
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
                    <option value='0'>Wybierz brygadę...</option>");

                    $komenda_sql_bryg = "EXECUTE dbo.Brygada_Przydziel_Wyswietl;";

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
                <input id='DataRozpoczecia' type='date' name='DataRozpoczecia' required>
            </p>
            <p class='lbl'>
                <label for='TerminRealizacji'>Proponowany termin <br>realizacji: </label><br>
                <input id='TerminRealizacji' type='date' name='TerminRealizacji'>
            </p>
            <p class='lbl'>
                <label for='Cena'>Cena: </label>
                <input id='Cena' type='number' name='Cena' step='.01' value='0.00' readonly>
            </p>
            <p>Uwagi (opcjonalne):</p>
            <textarea name='Uwagi' id='Uwagi' maxlength='200'></textarea>
        </fieldset>
        <p><input type='submit' value='Zapisz'><input type='reset' value='Wyczyść pola'></p>
        <p><a class='bck' href='logowanie_uzytk_koniec.php'>Wyloguj</a></p>
    </form>");
    print("</div>");

    print("<div class='inform'>
    <h5>Drogi użytkowniku!</h5>
    <p>
        Jeśli stworzyłeś/aś już zamówienie, osoba z naszej firmy skontaktuje się z tobą w ciągu najbliższych 24 godzin, aby omówić szczegóły zlecenia  dodanie wybranych usług 
        czy ustalenie dokładnego terminu realizacji. W razie pytań proszę dzwonić na numer: +48 268 245 381.
    </p>
    <p>Dziękujemy za skorzystanie z naszych usług</p>
</div>");

    sqlsrv_close($polaczenie);
    }
}
?>
</body>

</html>