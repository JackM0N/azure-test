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
    <title>Logowanie</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
</head>

<body>
<?php
    print("<h2>Logowanie do serwisu</h2>");
    if(!isset($_POST["Konto"]) || (trim($_POST["Konto"]) == "") || !isset($_POST["Haslo"]) || (trim($_POST["Haslo"]) == "")){
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

        print("<p class='msg error'>Podczas przetwarzania danych wystąpił błąd<br> Szczegóły: Nieprawidłowa nazwa konta lub hasło</p>");
        die("<p><a href='logowanie_uzytk_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
    }else{
        $KontoForm = trim($_POST["Konto"]);
        $HasloForm = trim($_POST["Haslo"]);
        $tablica_znaki = array("-", "~", "`", ":", ";", "{", "}", "+", "(", ")", "@", "!", "#", "^", "*", "[", "]", "<", ">", "\\", "\/", "?", ".", ",", "'", "/");

        $KontoForm = str_ireplace($tablica_znaki, "", $KontoForm);

        require_once("polaczenie_privbd.php");

        if(isset($polaczenie) && $polaczenie != null){
            try{
            $sql = "EXECUTE dbo.Uzytkownik_Wyswietl '$KontoForm';";

            $komenda_sql = $polaczenie->prepare($sql);
            
            $komenda_sql->execute();

            $zbior_wierszy = $komenda_sql->fetchAll(PDO::FETCH_ASSOC);
            
            if($komenda_sql->rowCount() != 1){
                $_SESSION["zalogowany"] = false;

                if(isset($_SESSION["uzytkownik"])){
                    unset($_SESSION["uzytkownik"]);
                }
                
                throw new Exception("Nieprawidłowa nazwa konta lub hasło (2)");
            }else{
                    foreach($zbior_wierszy as $wiersz){
                        $IdKlient = $wiersz["IdKlient"];
                        $Imie = $wiersz["Imie"]; 
                        $Nazwisko = $wiersz["Nazwisko"]; 
                        $Haslo = $wiersz["Haslo"]; 
                    }

                if(password_verify($HasloForm, $Haslo) == true){
                    $_SESSION["zalogowany"] = true;
                    $_SESSION["uzytkownik"] = $KontoForm;
                    $_SESSION["Imie"] = $Imie;
                    $_SESSION["Nazwisko"] = $Nazwisko;
                    $_SESSION["IdKlient"] = $IdKlient;

                    print("<p class='msg success'>Witaj <strong>$Imie $Nazwisko</strong>!<br>
                    Jesteś zalogowany(a) jako <strong>$KontoForm</strong></p>");

                    print("<p><br><a class='btn' href='zlecenie_uzytk_tabela.php'>Przejdź do wykazu pracowników</a></p>");
                    print("<p><br><a class='bck' href='logowanie_uzytk_koniec.php'>Wyloguj</a></p>");
                }else{
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
                    if(isset($_SESSION["IdKlient"])){
                        unset($_SESSION["IdKlient"]);
                    }
            
                    throw new Exception("Nieprawidłowa nazwa konta lub hasło (3)");
                }
            }   
            }catch(Exception $e){
                print("<p class='msg error'>Podczas przetwarzania danych wystąpił błąd<br> Szczegóły:".$e->getMessage()."</p>");
            }finally{
                $polaczenie = null;

                print("<p><a href='logowanie_uzytk_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
            }
        }
    }
?>
</body>

</html>