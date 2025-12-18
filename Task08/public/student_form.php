<?php
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$student = null;

if ($id) {
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
}

// Получаем список групп
$groups = $db->query("SELECT * FROM groups ORDER BY group_number")->fetchAll();

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'last_name' => $_POST['last_name'],
        'first_name' => $_POST['first_name'],
        'middle_name' => $_POST['middle_name'],
        'group_id' => $_POST['group_id'],
        'gender' => $_POST['gender'],
        'birth_date' => $_POST['birth_date']
    ];
    
    if ($id) {
        // Обновление
        $sql = "UPDATE students SET 
                last_name = :last_name,
                first_name = :first_name,
                middle_name = :middle_name,
                group_id = :group_id,
                gender = :gender,
                birth_date = :birth_date
                WHERE id = $id";
    } else {
        // Добавление
        $sql = "INSERT INTO students (last_name, first_name, middle_name, group_id, gender, birth_date) 
                VALUES (:last_name, :first_name, :middle_name, :group_id, :gender, :birth_date)";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($data);
    
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редактирование' : 'Добавление' ?> студента</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1><?= $id ? 'Редактирование' : 'Добавление' ?> студента</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Фамилия:</label>
                <input type="text" name="last_name" value="<?= $student['last_name'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="first_name" value="<?= $student['first_name'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Отчество:</label>
                <input type="text" name="middle_name" value="<?= $student['middle_name'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Группа:</label>
                <select name="group_id" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>" 
                                <?= ($student['group_id'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                            <?= $group['group_number'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Пол:</label>
                <label><input type="radio" name="gender" value="М" 
                    <?= ($student['gender'] ?? '') == 'М' ? 'checked' : '' ?>> Мужской</label>
                <label><input type="radio" name="gender" value="Ж"
                    <?= ($student['gender'] ?? '') == 'Ж' ? 'checked' : '' ?>> Женский</label>
            </div>
            
            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="date" name="birth_date" value="<?= $student['birth_date'] ?? '' ?>">
            </div>
            
            <button type="submit" class="btn">Сохранить</button>
            <a href="index.php" class="btn cancel">Отмена</a>
        </form>
    </div>
</body>
</html>