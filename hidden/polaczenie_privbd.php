<?php
    $server = "DESKTOP-BK5MMO6\SQL1A";
    $baza_danych = "P15_P1";

    try{
        $polaczenie = new PDO("sqlsrv:server=$server; Database=$baza_danych");
        
        //Obsł wyjątków
        $polaczenie->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //UTF-8
        $polaczenie->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
    }catch(Exception $e){
        print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.<br> Szczegóły: ".$e->getMessage()."</p>");
    }
?>