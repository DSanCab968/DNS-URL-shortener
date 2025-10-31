<html>
<body>

<?php 


    // Configura tu dominio y tus credenciales de IONOS
    $DOMAIN = ""; // cámbialo por tu dominio real
    $IONOS_API_USER = "";  // email de IONOS
    $IONOS_API_PASS = ""; // contraseña o token














    $url = $_POST["url"];

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die("❌ URL no válida");
    }

    #Crear hash
    $hash = substr(hash('sha256', $url), 0, 6);
    $subdomain = $hash . "." . $DOMAIN;

    echo "<h3>Procesando URL...</h3>";
    echo "<p>URL original: $url</p>";
    echo "<p>Hash: $hash</p>";
    echo "<p>Subdominio: $subdomain</p>";

    #Comprobar si ya existe un registro TXT
    $checkUrl = "https://api.hosting.ionos.com/dns/v1/zones";
    $ch = curl_init($checkUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$IONOS_API_USER:$IONOS_API_PASS");
    $response = curl_exec($ch);
    curl_close($ch);

    $zones = json_decode($response, true);
    if (!$zones) die("❌ Error al obtener zonas DNS de IONOS");

    $zoneId = null;
    foreach ($zones as $zone) {
        if ($zone["name"] === $DOMAIN) {
            $zoneId = $zone["id"];
            break;
        }
    }

    if (!$zoneId) die("❌ No se encontró la zona DNS de $DOMAIN");

    #Consultar registros existentes
    $getRecordsUrl = "https://api.hosting.ionos.com/dns/v1/zones/$zoneId/records";
    $ch = curl_init($getRecordsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$IONOS_API_USER:$IONOS_API_PASS");
    $response = curl_exec($ch);
    curl_close($ch);

    $records = json_decode($response, true);
    $exists = false;

    foreach ($records as $r) {
        if ($r["type"] === "TXT" && $r["name"] === $hash) {
            $exists = true;
            $original = $r["content"];
            echo "<p>✅ Ya existe este hash. URL original: <a href='$original' target='_blank'>$original</a></p>";
            echo "<p>URL corta: <a href='https://$subdomain' target='_blank'>https://$subdomain</a></p>";
            exit;
        }
    }

    // 5. Si no existe, creamos el registro TXT
    echo "<p>🆕 Creando registro TXT en IONOS...</p>";

    $data = [
        [
            "name" => $hash,
            "type" => "TXT",
            "content" => $url,
            "ttl" => 60
        ]
    ];

    $createUrl = "https://api.hosting.ionos.com/dns/v1/zones/$zoneId/records";
    $ch = curl_init($createUrl);
    curl_setopt($ch, CURLOPT_USERPWD, "$IONOS_API_USER:$IONOS_API_PASS");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if (strpos($response, "id") !== false) {
        echo "<p>✅ Registro TXT creado correctamente.</p>";
        echo "<p>URL corta: <a href='https://$subdomain' target='_blank'>https://$subdomain</a></p>";
    } else {
        echo "<p>❌ Error creando el registro DNS:</p>";
        echo "<pre>$response</pre>";
    }




?>


</body>
</html>
