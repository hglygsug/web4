<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Форма регистрации</h1>
        <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
            <p class="error-text"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="fullName">ФИО:</label>
            <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($values['fullName'] ?? '') ?>" class="<?= isset($errors['fullName']) ? 'input-error' : '' ?>" required>

            <label for="phone">Телефон:</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($values['phone'] ?? '') ?>" class="<?= isset($errors['phone']) ? 'input-error' : '' ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'input-error' : '' ?>" required>

            <label for="birthDate">Дата рождения:</label>
            <input type="date" id="birthDate" name="birthDate" value="<?= htmlspecialchars($values['birthDate'] ?? '') ?>" class="<?= isset($errors['birthDate']) ? 'input-error' : '' ?>" required>

            <label>Пол:</label>
            <input type="radio" id="male" name="gender" value="Мужской" <?= (isset($values['gender']) && $values['gender'] == 'Мужской') ? 'checked' : '' ?> required>
            <label for="male">Мужской</label>
            <input type="radio" id="female" name="gender" value="Женский" <?= (isset($values['gender']) && $values['gender'] == 'Женский') ? 'checked' : '' ?> required>
            <label for="female">Женский</label>

            <label for="languages">Любимый язык программирования:</label>
            <select id="languages" name="languages[]" multiple required>
                <?php $langs = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go']; ?>
                <?php foreach ($langs as $lang): ?>
                <option value="<?= $lang ?>" <?= (isset($values['languages']) && in_array($lang, $values['languages'])) ? 'selected' : '' ?>><?= $lang ?></option>
                <?php endforeach; ?>
            </select>

            <label for="bio">Биография:</label>
            <textarea id="bio" name="bio" class="<?= isset($errors['bio']) ? 'input-error' : '' ?>" required><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>

            <label for="contract">С контрактом ознакомлен(а):</label>
            <input type="checkbox" id="contract" name="contract" <?= isset($values['contract']) && $values['contract'] ? 'checked' : '' ?> required>

            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>
