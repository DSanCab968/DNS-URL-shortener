<html>
<body>


    <?php  

    $url = $_POST["url"];

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die("URL no válida");
    }else{

        echo "<p>Your email address is: ".$url."</p>";

    }

    /*
    $dominio = parse_url($url, PHP_URL_HOST);
    $hostArr = explode(".",$dominio);
    $host = $hostArr[1].".".$hostArr[2];
    echo "<p>Dominio: $host</p>";
    */

    #Revisar si hay algo en el subdominio
    $host = parse_url($url, PHP_URL_HOST);
    $result = dns_get_record($host, DNS_A + DNS_TXT + DNS_CNAME);
    echo "<pre>";
    print_r($result);
    echo "</pre>";



    ?>

</body>
</html>
