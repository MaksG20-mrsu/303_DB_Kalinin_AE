<?php
// public/exam_delete.php
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$student_id = $_GET['student_id'] ?? 0;

if (!$id || !$student_id) {
    header('Location: index.php');
    exit;
}

// Получаем информацию об экзамене
$stmt = $db->prepare("SELECT e.*, s.last_name, s.first_name 
                     FROM exams e 
                     JOIN students s ON e.student_id = s.id 
                     WHERE e.id = ?");
$stmt->execute([$id]);
$exam = $stmt->fetch();

if (!$exam) {
    header("Location: exams.php?student_id=" . $student_id);
    exit;
}

// Обработка подтверждения удаления
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        $stmt = $db->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: exams.php?student_id=" . $student_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удаление экзамена</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 500px;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Удаление экзамена</h1>
        
        <div class="warning">
            <p>Вы действительно хотите удалить запись об экзамене?</p>
            <p><strong>Студент:</strong> <?= htmlspecialchars($exam['last_name'] . ' ' . $exam['first_name']) ?></p>
            <p><strong>Дата экзамена:</strong> <?= $exam['exam_date'] ?></p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="exams.php?student_id=<?= $student_id ?>" class="btn">Отмена</a>
        </form>
    </div>
</body>
</html>