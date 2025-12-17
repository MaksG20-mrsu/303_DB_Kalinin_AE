<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Фильтр по группе
$groupFilter = $_GET['group'] ?? '';
$where = '';
$params = [];

if ($groupFilter) {
    $where = "WHERE g.group_number = ?";
    $params[] = $groupFilter;
}

// Получаем список студентов
$sql = "SELECT s.*, g.group_number 
        FROM students s 
        LEFT JOIN groups g ON s.group_id = g.id 
        $where 
        ORDER BY g.group_number, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем список групп для фильтра
$groups = $db->query("SELECT group_number FROM groups ORDER BY group_number")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список студентов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Список студентов</h1>
        
        <!-- Фильтр по группе -->
        <form method="GET" class="filter-form">
            <label>Фильтр по группе:
                <select name="group" onchange="this.form.submit()">
                    <option value="">Все группы</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['group_number'] ?>" 
                                <?= $groupFilter == $group['group_number'] ? 'selected' : '' ?>>
                            <?= $group['group_number'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>
        
        <!-- Таблица студентов -->
        <table>
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Группа</th>
                    <th>Пол</th>
                    <th>Дата рождения</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . $student['middle_name']) ?></td>
                    <td><?= htmlspecialchars($student['group_number']) ?></td>
                    <td><?= htmlspecialchars($student['gender']) ?></td>
                    <td><?= $student['birth_date'] ?></td>
                    <td class="actions">
                        <a href="student_form.php?id=<?= $student['id'] ?>" class="btn edit">✏️ Редактировать</a>
                        <a href="student_delete.php?id=<?= $student['id'] ?>" class="btn delete" 
                           onclick="return confirm('Удалить студента?')">🗑️ Удалить</a>
                        <a href="exams.php?student_id=<?= $student['id'] ?>" class="btn exams">📚 Экзамены</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="student_form.php" class="btn add">+ Добавить студента</a>
    </div>
</body>
</html>