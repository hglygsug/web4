<?php
$host = 'localhost';  
$dbname = 'u68656';  
$username = 'u68656';  
$password = '6481553';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}
function setErrors($errors) {
    setcookie('errors', serialize($errors), time() + 3600, "/"); 
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST['fullName']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $birthDate = trim($_POST['birthDate']);
    $gender = $_POST['gender'];
    $languages = $_POST['languages'];
    $bio = trim($_POST['bio']);
    $contract = isset($_POST['contract']) ? 1 : 0; 

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $fullName) || strlen($fullName) > 150) {
        $errors['fullName'] = "ФИО должно содержать только буквы и пробелы, и быть не более 150 символов.";
    }

    if (!preg_match("/^\+?[0-9]{1}[\-]?[0-9]{3}[\-]?[0-9]{3}[\-]?[0-9]{2}[\-]?[0-9]{2}$/", $phone)) {
        $errors['phone'] = "Телефон должен содержать только цифры и, возможно, плюс для международного формата.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный E-mail.";
    }

    
    if (strtotime($birthDate) === false) {
        $errors['birthDate'] = "Некорректная дата рождения.";
    }

    if (empty($languages)) {
        $errors['languages'] = "Выберите хотя бы один язык программирования.";
    }

    if (empty($bio)) {
        $errors['bio'] = "Биография не может быть пустой.";
    }

    if (!empty($errors)) {
        setErrors($errors);
        setcookie('fullName', $fullName, time() + 3600, "/");
        setcookie('phone', $phone, time() + 3600, "/");
        setcookie('email', $email, time() + 3600, "/");
        setcookie('birthDate', $birthDate, time() + 3600, "/");
        setcookie('gender', $gender, time() + 3600, "/");
        setcookie('languages', serialize($languages), time() + 3600, "/");
        setcookie('bio', $bio, time() + 3600, "/");
        setcookie('contract', $contract, time() + 3600, "/");
        header("Location: index.php");
        exit();
    }

    setcookie('fullName', $fullName, time() + 365*24*60*60, "/");
    setcookie('phone', $phone, time() + 365*24*60*60, "/");
    setcookie('email', $email, time() + 365*24*60*60, "/");
    setcookie('birthDate', $birthDate, time() + 365*24*60*60, "/");
    setcookie('gender', $gender, time() + 365*24*60*60, "/");
    setcookie('languages', serialize($languages), time() + 365*24*60*60, "/");
    setcookie('bio', $bio, time() + 365*24*60*60, "/");
    setcookie('contract', $contract, time() + 365*24*60*60, "/");

    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullName, phone, email, birthDate, gender, bio, contract) 
                               VALUES (:fullName, :phone, :email, :birthDate, :gender, :bio, :contract)");
        $stmt->execute([
            ':fullName' => $fullName,
            ':phone' => $phone,
            ':email' => $email,
            ':birthDate' => $birthDate,
            ':gender' => $gender,
            ':bio' => $bio,
            ':contract' => $contract
        ]);

        $userId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (:user_id, :language)");
        foreach ($languages as $language) {
            $stmt->execute([
                ':user_id' => $userId,
                ':language' => $language
            ]);
        }

        setcookie('errors', "", time() - 3600, "/");

        echo "Данные успешно сохранены!";
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}
?>
