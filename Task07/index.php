<?php require_once 'database.php'; ?>
<?php
$db = getDB();
$currentYear = date('Y');


$stmt = $db->prepare("SELECT group_number FROM groups WHERE graduation_year >= ? ORDER BY group_number");
$stmt->execute([$currentYear]);
$activeGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);


$selectedGroup = $_GET['group'] ?? '';


$sql = "SELECT 
            g.group_number,
            g.direction,
            s.last_name,
            s.first_name,
            s.middle_name,
            s.gender,
            s.birth_date,
            s.student_id
        FROM students s
        JOIN groups g ON s.group_id = g.id
        WHERE g.graduation_year >= :currentYear";
        
$params = [':currentYear' => $currentYear];

if ($selectedGroup && in_array($selectedGroup, $activeGroups)) {
    $sql .= " AND g.group_number = :groupNumber";
    $params[':groupNumber'] = $selectedGroup;
}

$sql .= " ORDER BY g.group_number, s.last_name, s.first_name";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #4a6fa5 0%, #2e4a76 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        label {
            font-weight: 600;
            color: #495057;
        }
        
        select {
            padding: 10px 15px;
            border: 2px solid #4a6fa5;
            border-radius: 8px;
            font-size: 16px;
            color: #495057;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        select:hover {
            border-color: #2e4a76;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
        }
        
        button {
            padding: 10px 25px;
            background: linear-gradient(135deg, #4a6fa5 0%, #2e4a76 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .reset-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .table-container {
            padding: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }
        
        thead {
            background: linear-gradient(135deg, #4a6fa5 0%, #2e4a76 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th:last-child {
            border-right: none;
        }
        
        tbody tr {
            border-bottom: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(5px);
        }
        
        td {
            padding: 15px;
            color: #495057;
        }
        
        .gender-male {
            color: #4a6fa5;
            font-weight: 600;
        }
        
        .gender-female {
            color: #d63384;
            font-weight: 600;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 18px;
        }
        
        .student-id {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #4a6fa5;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎓 Список студентов университета</h1>
            <p>Текущий учебный год: <?php echo $currentYear; ?></p>
        </header>
        
        <section class="filter-section">
            <form method="GET" action="" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <label for="group">Фильтр по группе:</label>
                <select name="group" id="group">
                    <option value="">Все группы</option>
                    <?php foreach ($activeGroups as $group): ?>
                        <option value="<?php echo htmlspecialchars($group); ?>" 
                                <?php echo $selectedGroup === $group ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($group); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Применить фильтр</button>
                <a href="?" class="reset-btn" style="text-decoration: none;">
                    <button type="button" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">Сбросить</button>
                </a>
            </form>
        </section>
        
        <div class="table-container">
            <?php if (!empty($students)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Группа</th>
                            <th>Направление подготовки</th>
                            <th>ФИО студента</th>
                            <th>Пол</th>
                            <th>Дата рождения</th>
                            <th>Номер студенческого билета</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php
                            $fio = htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? ''));
                            $genderClass = $student['gender'] == 'M' ? 'gender-male' : 'gender-female';
                            $genderText = $student['gender'] == 'M' ? 'Мужской' : 'Женский';
                            $birthDate = date('d.m.Y', strtotime($student['birth_date']));
                            ?>
                            <tr>
                                <td><span class="badge"><?php echo htmlspecialchars($student['group_number']); ?></span></td>
                                <td><?php echo htmlspecialchars($student['direction']); ?></td>
                                <td><?php echo $fio; ?></td>
                                <td class="<?php echo $genderClass; ?>"><?php echo $genderText; ?></td>
                                <td><?php echo $birthDate; ?></td>
                                <td><span class="student-id"><?php echo htmlspecialchars($student['student_id']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>📭 Нет данных для отображения. Выберите другую группу или проверьте наличие студентов.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <footer>
            <p>Всего студентов: <?php echo count($students); ?></p>
            <p>© <?php echo date('Y'); ?> Университетская система. Лабораторная работа №7.</p>
        </footer>
    </div>
</body>
</html>