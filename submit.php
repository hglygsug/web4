<?php
session_start();
$host = 'localhost';
$dbname = 'u68656'; 
$username = 'u68656'; 
$password = '6481553'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$errors = [];
$values = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
    $values = [
        'fullName' => trim($_POST['fullName']),
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email']),
        'birthDate' => trim($_POST['birthDate']),
        'gender' => $_POST['gender'] ?? '',
        'languages' => $_POST['languages'] ?? [],
        'bio' => trim($_POST['bio']),
        'contract' => isset($_POST['contract']) ? 1 : 0,
    ];

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s-]{2,150}$/u", $values['fullName'])) {
        $errors['fullName'] = "ФИО должно содержать только буквы, пробелы и дефисы (от 2 до 150 символов)";
    }
    if (!preg_match("/^\\+?[0-9]{11}$/", $values['phone'])) {
        $errors['phone'] = "Телефон должен быть в формате +71234567890";
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Введите корректный e-mail";
    }
    if (!$values['birthDate'] || strtotime($values['birthDate']) === false) {
        $errors['birthDate'] = "Введите корректную дату рождения";
    }
    if (empty($values['languages'])) {
        $errors['languages'] = "Выберите хотя бы один язык программирования";
    }
    if (strlen($values['bio']) < 10) {
        $errors['bio'] = "Биография должна содержать минимум 10 символов";
    }
    if (!$values['contract']) {
        $errors['contract'] = "Необходимо подтвердить ознакомление с контрактом";
    }

    if (!empty($errors)) {
        setcookie('form_errors', serialize($errors), 0, '/');
        setcookie('form_values', serialize($values), 0, '/');
        header("Location: register.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullName, phone, email, birthDate, gender, bio, contract) VALUES (:fullName, :phone, :email, :birthDate, :gender, :bio, :contract)");
        $stmt->execute([
            ':fullName' => $values['fullName'],
            ':phone' => $values['phone'],
            ':email' => $values['email'],
            ':birthDate' => $values['birthDate'],
            ':gender' => $values['gender'],
            ':bio' => $values['bio'],
            ':contract' => $values['contract'],
        ]);
        
        $userId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (:user_id, :language)");
        foreach ($values['languages'] as $language) {
            $stmt->execute([':user_id' => $userId, ':language' => $language]);
        }
        
        
        setcookie('saved_values', serialize($values), time() + 31536000, '/');
        setcookie('form_errors', '', time() - 3600, '/'); 
        setcookie('form_values', '', time() - 3600, '/'); 
        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        die("Ошибка: " . $e->getMessage());
    }
}
$errors = isset($_COOKIE['form_errors']) ? unserialize($_COOKIE['form_errors']) : [];
$values = isset($_COOKIE['form_values']) ? unserialize($_COOKIE['form_values']) : unserialize($_COOKIE['saved_values'] ?? '[]');
?>