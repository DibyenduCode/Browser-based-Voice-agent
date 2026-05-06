<?php

header("Content-Type: application/json");

/* =========================================
   GET INPUT
========================================= */

$raw = file_get_contents("php://input");

$data = json_decode($raw, true);

/* SUPPORT JSON + FORM + GET */

$message = trim(
    $data["message"] ??
    $_POST["message"] ??
    $_GET["message"] ??
    ""
);

/* EMPTY CHECK */

if (empty($message)) {

    echo json_encode([
        "success" => false,
        "reply" => "No message received"
    ]);

    exit;
}

/* =========================================
   N8N WEBHOOK
========================================= */

$webhook = "https://dibyendun8n.site/webhook/b3a0b758-b4af-4ad2-9c1b-b30857f2ccce";

/* =========================================
   PAYLOAD
========================================= */

$payload = [
    "message" => $message,
    "source" => "voice-agent",
    "time" => date("Y-m-d H:i:s")
];

/* =========================================
   CURL REQUEST
========================================= */

$ch = curl_init($webhook);

curl_setopt_array($ch, [

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_POST => true,

    CURLOPT_POSTFIELDS => json_encode($payload),

    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],

    CURLOPT_TIMEOUT => 120,

    CURLOPT_CONNECTTIMEOUT => 20,

    CURLOPT_FOLLOWLOCATION => true
]);

/* =========================================
   WAIT FOR FULL RESPONSE
========================================= */

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$error = curl_error($ch);

curl_close($ch);

/* =========================================
   CURL ERROR
========================================= */

if ($error) {

    echo json_encode([
        "success" => false,
        "reply" => "Workflow connection failed",
        "error" => $error
    ]);

    exit;
}

/* =========================================
   DEBUG LOG
========================================= */

file_put_contents(
    "debug.txt",
    "\n\n====================================\n".
    "TIME: ".date("Y-m-d H:i:s")."\n".
    "USER: ".$message."\n".
    "HTTP CODE: ".$httpCode."\n".
    "RAW RESPONSE:\n".$response."\n",
    FILE_APPEND
);

/* =========================================
   PARSE RESPONSE
========================================= */

$result = json_decode($response, true);

$reply = "";

/* =========================================
   HANDLE JSON RESPONSE
========================================= */

/* { "reply": "hello" } */

if (isset($result["reply"])) {

    $reply = $result["reply"];
}

/* { "output": "hello" } */

elseif (isset($result["output"])) {

    $reply = $result["output"];
}

/* ARRAY RESPONSE */

elseif (is_array($result) && isset($result[0]["output"])) {

    $reply = $result[0]["output"];
}

/* PLAIN TEXT */

elseif (!empty(trim($response))) {

    $reply = strip_tags($response);
}

/* FALLBACK */

else {

    $reply = "No valid response from workflow";
}

/* =========================================
   CLEAN FOR VOICE
========================================= */

$reply = strip_tags($reply);

$reply = html_entity_decode($reply);

$reply = preg_replace('/\s+/', ' ', $reply);

$reply = trim($reply);

/* LIMIT VERY LONG RESPONSE */

if (strlen($reply) > 3000) {

    $reply = substr($reply, 0, 3000);
}

/* =========================================
   FINAL RESPONSE
========================================= */

echo json_encode([
    "success" => true,
    "reply" => $reply
]);

?>