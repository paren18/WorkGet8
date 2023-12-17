<?php
require __DIR__ . '/vendor/autoload.php'; 

// Функция валидации для кириллического имени
function validateCyrillicName($name) {
    return preg_match('/^[\p{Cyrillic} ]+$/u', $name);
}

// Функция валидации для электронной почты
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Функция валидации для номера телефона
function validatePhoneNumber($phone) {
    return preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    $errors = [];

    // Валидация данных
    if (!validateCyrillicName($name)) {
        $errors["name"] = "$name";
    }

    if (!validateEmail($email)) {
        $errors["email"] = "$email";
    }

    if (!validatePhoneNumber($phone)) {
        $errors["phone"] = "$phone";
    }

    if (!empty($errors)) {
        $errorString = implode(', ', $errors);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Невалидные данные: $errorString"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $client = new Google_Client();
    $client->setAuthConfig('my_secret.json'); // Path to your credentials JSON file
    $client->setAccessType('offline');
    $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);


    $service = new Google_Service_Sheets($client);

    // Spreadsheet ID and range
    $spreadsheetId = '1J7yQN4LfqTYltgBJGeXAvH9ymPmvL0eXlHGNS0Pnufg';
    $range = 'Лист1'; // Update this with your sheet name
    $date =  date('Y-m-d H:i:s');

    $values = [
        [$name, $email, $phone, $date],
    ];

    $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
    $params = ['valueInputOption' => 'RAW'];


    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);


    if ($result->getUpdates()->getUpdatedCells() > 0) {
        echo json_encode(['success' => true, 'message' => 'Данные валидны '. $date], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при отправке данных в Google Sheets'], JSON_UNESCAPED_UNICODE);
    }
}
?>
