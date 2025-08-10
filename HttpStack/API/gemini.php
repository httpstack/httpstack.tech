<?php

if($_POST){ 

    $apiKey = ''; // Replace with your actual API key



    function callGeminiAPI($prompt, $apiKey) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

        $data = array(
            "contents" => array(
                array(
                    "parts" => array(
                        array(
                            "text" => $prompt
                        )
                    )
                )
            )
        );

        $json_data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Keep this true for production

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code !== 200) {
            echo "Error: HTTP Status Code " . $http_code . "\n";
            echo "Response: " . $response . "\n";
            return null;
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        // Check if the expected structure exists before accessing
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            echo "Error: Unexpected response format from Gemini API.\n";
            return null;
        }
    }

    $userPrompt = $_POST['prompt'];
    $geminiResponse = callGeminiAPI($userPrompt, $apiKey);

    if ($geminiResponse) {
        echo "Gemini's response: " . $geminiResponse;
    } else {
        echo "Failed to get a response from Gemini.";
    }
}else{
?>
<!DOCTYPE html>
<html>

<head>
    <title>Gemini API Test</title>
</head>

<body>
    <form action="gemini.php" method="post">
        <label for="prompt">Enter your prompt:</label><br>
        <textarea id="prompt" name="prompt" rows="4" cols="50"></textarea><br><br>
        <input type="submit" value="Submit">
    </form>
</body>

</html>
<?php
}
?>
