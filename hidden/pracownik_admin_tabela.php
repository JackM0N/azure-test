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
    <header>
        <div class="logo">
            FRB
        </div>
        <div class="contact">
            <h3>Jesteś zalogowany jako:</h3> 
            <h4>
                <?php
                    if (isset($_SESSION["zalogowany"]) && ($_SESSION["zalogowany"]) == false || (!isset($_SESSION["zalogowany"])) || (!isset($_SESSION["uzytkownik"]))) {
                        print("ARGH!");
                    } else if (($_SESSION["zalogowany"]) == true && (isset($_SESSION["uzytkownik"]))) {
                        print($_SESSION["Imie"]." ".$_SESSION["Nazwisko"]." (".$_SESSION["uzytkownik"].")");
                    }
                ?>
            </h4>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="zlecenie_admin_tabela.php">ZLECENIA</a></li>
            <li><a href="pracownik_admin_tabela.php">PRACOWNICY</a></li>
            <li><a href="brygada_admin_tabela.php">BRYGADY</a></li>
            <li><a href="logowanie_admin_koniec.php">WYLOGUJ</a></li>
        </ul>
    </nav>
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
        $server = "DESKTOP-BK5MMO6\SQL1A";

        $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

        //Łączenie z serwerem
        $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

        if ($polaczenie == false) {
            print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
            die(print_r(sqlsrv_errors(), true));
        } else {
            print("<h2>Pracownicy</h2>");
            print("<table>
                    <thead>
                        <tr>
                            <td><a href='pracownik_admin_tabela.php?sort=IdPracownik' class='head'>Identyfikator</a></td>
                            <td><a href='pracownik_admin_tabela.php?sort=Imie' class='head'>Imie</a></td>
                            <td><a href='pracownik_admin_tabela.php?sort=Nazwisko' class='head'>Nazwisko</a></td>
                            <td><a href='pracownik_admin_tabela.php?sort=Specjalizacja' class='head'>Specjalizacja</a></td>
                            <td><a href='pracownik_admin_tabela.php?sort=NumerTelefonu' class='head'>Numer telefonu</a></td>
                            <td><a href='pracownik_admin_tabela.php?sort=AdresEmail' class='head'>Adres email</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>");

            if (!isset($_GET["sort"])) {
                $sort = 'Nazwisko';
            } else {
                $sort = $_GET["sort"];
            }

            $komenda_sql = "SELECT IdPracownik, Imie, Nazwisko, Specjalizacja, NumerTelefonu, AdresEmail
                        FROM dbo.Pracownik
                        ORDER BY $sort ASC;";

            if (isset($_GET["szukaj"]) && is_numeric($_GET["szukaj"]) && $_GET["szukaj"] != "") {
                $szukana = trim($_GET["szukaj"]);
                $komenda_sql = "SELECT IdPracownik, Imie, Nazwisko, Specjalizacja, NumerTelefonu, AdresEmail
                        FROM dbo.Pracownik
                        WHERE IdPracownik = $szukana
                        ORDER BY $sort ASC;";
            }

            if (isset($_GET["szukaj"]) && !is_numeric($_GET["szukaj"]) && $_GET["szukaj"] != "") {
                $szukana = trim($_GET["szukaj"]);
                $komenda_sql = "SELECT IdPracownik, Imie, Nazwisko, Specjalizacja, NumerTelefonu, AdresEmail
                        FROM dbo.Pracownik
                        WHERE Imie = '$szukana' OR Nazwisko LIKE '%$szukana%' OR Specjalizacja = '$szukana' OR NumerTelefonu = '$szukana' OR AdresEmail = '$szukana'
                        ORDER BY $sort ASC;";
            }

            $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);

            if (sqlsrv_has_rows($zbior_wierszy) == false) {
                print("<tr><td colspan = '8'>Brak danych pracowników w bazie</td></tr>");
            } else {
                while ($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)) {
                    $IdPracownik = $wiersz["IdPracownik"];
                    $Imie = $wiersz["Imie"];
                    $Nazwisko = $wiersz["Nazwisko"];
                    $Specjalizacja = $wiersz["Specjalizacja"];
                    $NumerTelefonu = $wiersz["NumerTelefonu"];
                    $AdresEmail = $wiersz["AdresEmail"];

                    print("<tr>
                            <td>$IdPracownik</td>
                            <td>$Imie</td>
                            <td>$Nazwisko</td>
                            <td>$Specjalizacja</td>
                            <td>$NumerTelefonu</td>
                            <td>$AdresEmail</td>
                            <td><a href='pracownik_admin_edytuj.php?IdPracownik=$IdPracownik' class='btn upd'>Edytuj</a></td>
                            <td><a href='pracownik_admin_usun.php?IdPracownik=$IdPracownik' class='btn del'>Usuń</a></td>        
                </tr>");
                }

                if ($zbior_wierszy != null) {
                    sqlsrv_free_stmt($zbior_wierszy);
                }
            }

            print("</tbody>
                </table>");

            sqlsrv_close($polaczenie);

            print("<button type='button' onclick='zamiana();' id='butn'>+ Nowy pracownik</button>");
            print("<div id='dodawanieForm'>");
            print("<h2>Nowy pracownik</h2>
        <form id='pracownikDodaj' method='get' action='pracownik_admin_dodaj.php'>
        <fieldset>
            <legend>Parametry tabeli</legend>
            <p class='lbl'>
                <label for='IdPracownik'>IdPracownik: </label>
                <input id='IdPracownik' type='text' maxlength='10' name='IdPracownik' required pattern='[0-9]*'>
            </p>
            <p class='lbl'>
                <label for='Imie'>Imie: </label>
                <input id='Imie' type='text' maxlength='30' name='Imie' required pattern='[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ ]+'>
            </p>
            <p class='lbl'>
                <label for='Nazwisko'>Nazwisko: </label>
                <input id='Nazwisko' type='text' maxlength='50' name='Nazwisko' required pattern='[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ ]+'>
            </p>
            <p class='lbl'>
                <label for='Specjalizacja'>Specjalizacja: </label>
                <input id='Specjalizacja' type='text' maxlength='50' name='Specjalizacja' required pattern='[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ ]+'>
            </p>
            <p class='lbl'>
                <label for='NumerTelefonu'>Numer telefonu: </label>
                <input id='NumerTelefonu' type='text' maxlength='20' name='NumerTelefonu' required pattern='\+[0-9]{2} [0-9]{3} [0-9]{3} [0-9]{3}|\+[0-9]{2} [0-9]{2} [0-9]{4} [0-9]{3}'>
            </p>
            <p class='lbl'>
                <label for='AdresEmail'>Adres e-mail: </label>
                <input id='AdresEmail' type='email' maxlength='50' name='AdresEmail' required>
            </p>
            <p>Uwagi (opcjonalne):</p>
            <textarea name='Uwagi' id='Uwagi' maxlength='200'></textarea>
        </fieldset>
        <p><input type='submit' value='Zapisz'><input type='reset' value='Wyczyść pola'></p>
    </form>");
            print("</div>");
        }
    }
    ?>
    </div>
</body>

</html>