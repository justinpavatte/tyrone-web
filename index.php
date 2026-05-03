<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';

    $data = [
        'model' => 'llama3:latest',
        'prompt' => $prompt,
        'stream' => false
    ];

    $ch = curl_init('http://100.116.188.35:11434/api/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);

    if ($response === false) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        echo $response;
    }

    curl_close($ch);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tyrone</title>
    <style>
        body {
            background: #111;
            color: #eee;
            font-family: Arial;
            max-width: 800px;
            margin: 40px auto;
        }
        textarea {
            width: 100%;
            height: 100px;
            background: #222;
            color: #fff;
            border: 1px solid #444;
            padding: 10px;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            background: #444;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        #output {
            margin-top: 20px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<h2>Hi, I'm Tyrone, how may I help you?</h2>

<textarea id="prompt"></textarea>
<button onclick="send()">Send</button>

<div id="output"></div>

<script>
async function send() {
    const prompt = document.getElementById('prompt').value;

    document.getElementById('output').innerText = 'Thinking...';

    const res = await fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt })
    });

    const data = await res.json();

    if (data.response) {
        document.getElementById('output').innerText = data.response;
    } else {
        document.getElementById('output').innerText = JSON.stringify(data, null, 2);
    }
}
</script>

</body>
</html>
