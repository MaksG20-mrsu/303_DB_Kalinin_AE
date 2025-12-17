<?php
// public/exam_form.php
require_once '../includes/db.php';

$student_id = $_GET['student_id'] ?? 0;
$id = $_GET['id'] ?? 0; // ID экзамена для редактирования
$exam = null;

// Получаем информацию о студенте
$stmt = $db->prepare("SELECT s.*, g.group_number FROM students s 
                     LEFT JOIN groups g ON s.group_id = g.id 
                     WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: index.php');
    exit;
}

// Если редактируем существующий экзамен
if ($id) {
    $stmt = $db->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$id]);
    $exam = $stmt->fetch();
}

// Получаем предметы
$subjects = $db->query("SELECT * FROM subjects ORDER BY name")->fetchAll();

// Если таблица subjects пустая, создаем тестовые данные
if (empty($subjects)) {
    $db->exec("INSERT INTO subjects (name, semester, year_of_study, speciality) VALUES 
              ('Математика', 1, 1, 'Общий'),
              ('Программирование', 1, 1, 'Информатика'),
              ('Базы данных', 2, 2, 'Информатика'),
              ('Физика', 2, 1, 'Общий')");
    $subjects = $db->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'student_id' => $_POST['student_id'],
        'subject_id' => $_POST['subject_id'],
        'exam_date' => $_POST['exam_date'],
        'grade' => $_POST['grade'],
        'teacher' => $_POST['teacher']
    ];
    
    if ($id) {
        // Обновление
        $sql = "UPDATE exams SET 
                student_id = :student_id,
                subject_id = :subject_id,
                exam_date = :exam_date,
                grade = :grade,
                teacher = :teacher
                WHERE id = $id";
    } else {
        // Добавление
        $sql = "INSERT INTO exams (student_id, subject_id, exam_date, grade, teacher) 
                VALUES (:student_id, :subject_id, :exam_date, :grade, :teacher)";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($data);
    
    header("Location: exams.php?student_id=" . $student_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редактирование' : 'Добавление' ?> экзамена</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $id ? 'Редактирование' : 'Добавление' ?> экзамена</h1>
        <p><strong>Студент:</strong> <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></p>
        <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_number']) ?></p>
        
        <form method="POST">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            
            <div class="form-group">
                <label>Предмет:</label>
                <select name="subject_id" required>
                    <option value="">Выберите предмет</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>" 
                            <?= ($exam['subject_id'] ?? '') == $subject['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['name']) ?> 
                            (<?= $subject['semester'] ?> семестр)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата экзамена:</label>
                <input type="date" name="exam_date" 
                       value="<?= $exam['exam_date'] ?? date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Оценка:</label>
                <select name="grade" required>
                    <option value="5" <?= ($exam['grade'] ?? '') == 5 ? 'selected' : '' ?>>5 (Отлично)</option>
                    <option value="4" <?= ($exam['grade'] ?? '') == 4 ? 'selected' : '' ?>>4 (Хорошо)</option>
                    <option value="3" <?= ($exam['grade'] ?? '') == 3 ? 'selected' : '' ?>>3 (Удовлетворительно)</option>
                    <option value="2" <?= ($exam['grade'] ?? '') == 2 ? 'selected' : '' ?>>2 (Неудовлетворительно)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Преподаватель:</label>
                <input type="text" name="teacher" 
                       value="<?= $exam['teacher'] ?? '' ?>"
                       placeholder="ФИО преподавателя">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Сохранить</button>
                <a href="exams.php?student_id=<?= $student_id ?>" class="btn cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>