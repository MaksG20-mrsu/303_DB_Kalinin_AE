<?php
// Упрощенная версия exams.php
require_once '../includes/db.php';

$student_id = $_GET['student_id'] ?? 0;
if (!$student_id) die("Не указан студент");

// Получаем студента
$student = $db->query("SELECT * FROM students WHERE id = $student_id")->fetch();

// Получаем экзамены
$exams = $db->query("
    SELECT e.*, s.name as subject 
    FROM exams e 
    LEFT JOIN subjects s ON e.subject_id = s.id 
    WHERE e.student_id = $student_id 
    ORDER BY e.exam_date
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Экзамены</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>Экзамены: <?= $student['last_name'] ?> <?= $student['first_name'] ?></h1>
    
    <table>
        <tr><th>Предмет</th><th>Дата</th><th>Оценка</th><th>Действия</th></tr>
        <?php foreach ($exams as $exam): ?>
        <tr>
            <td><?= $exam['subject'] ?></td>
            <td><?= $exam['exam_date'] ?></td>
            <td><?= $exam['grade'] ?></td>
            <td>
                <a href="exam_form.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>">✏️</a>
                <a href="exam_delete.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" 
                   onclick="return confirm('Удалить?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <p>
        <a href="exam_form.php?student_id=<?= $student_id ?>">➕ Добавить экзамен</a> | 
        <a href="index.php">Назад</a>
    </p>
</body>
</html>