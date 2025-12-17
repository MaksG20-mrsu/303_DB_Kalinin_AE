#!/usr/bin/env php
<?php
require_once 'database.php';

$db = getDB();


$currentYear = date('Y');
$stmt = $db->prepare("SELECT group_number FROM groups WHERE graduation_year >= ? ORDER BY group_number");
$stmt->execute([$currentYear]);
$activeGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);


echo "Доступные группы:\n";
foreach ($activeGroups as $group) {
    echo "  {$group}\n";
}
echo "\n";


echo "Введите номер группы для фильтрации (или нажмите Enter для всех групп): ";
$input = trim(fgets(STDIN));


$selectedGroup = null;
if (!empty($input)) {
    if (!in_array($input, $activeGroups)) {
        echo "Ошибка: Группа '{$input}' не найдена или не является действующей.\n";
        exit(1);
    }
    $selectedGroup = $input;
}

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

if ($selectedGroup) {
    $sql .= " AND g.group_number = :groupNumber";
    $params[':groupNumber'] = $selectedGroup;
}

$sql .= " ORDER BY g.group_number, s.last_name, s.first_name";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

displayTable($students);

function displayTable($students) {
    if (empty($students)) {
        echo "Нет данных для отображения.\n";
        return;
    }
    
    $widths = [
        'group' => 15,
        'direction' => 40,
        'fio' => 30,
        'gender' => 6,
        'birth_date' => 12,
        'student_id' => 15
    ];
    
    echo "┌" . str_repeat("─", $widths['group']) . "┬"
                . str_repeat("─", $widths['direction']) . "┬"
                . str_repeat("─", $widths['fio']) . "┬"
                . str_repeat("─", $widths['gender']) . "┬"
                . str_repeat("─", $widths['birth_date']) . "┬"
                . str_repeat("─", $widths['student_id']) . "┐\n";
    
    printf("│%-{$widths['group']}s│%-{$widths['direction']}s│%-{$widths['fio']}s│%-{$widths['gender']}s│%-{$widths['birth_date']}s│%-{$widths['student_id']}s│\n",
        "Группа", "Направление", "ФИО", "Пол", "Дата рожд.", "№ студ. билета");
    
    echo "├" . str_repeat("─", $widths['group']) . "┼"
                . str_repeat("─", $widths['direction']) . "┼"
                . str_repeat("─", $widths['fio']) . "┼"
                . str_repeat("─", $widths['gender']) . "┼"
                . str_repeat("─", $widths['birth_date']) . "┼"
                . str_repeat("─", $widths['student_id']) . "┤\n";
    
    foreach ($students as $row) {
        $fio = $row['last_name'] . ' ' . $row['first_name'] . ' ' . ($row['middle_name'] ?? '');
        $gender = $row['gender'] == 'M' ? 'Муж' : 'Жен';
        $birthDate = date('d.m.Y', strtotime($row['birth_date']));
        
        printf("│%-{$widths['group']}s│%-{$widths['direction']}s│%-{$widths['fio']}s│%-{$widths['gender']}s│%-{$widths['birth_date']}s│%-{$widths['student_id']}s│\n",
            $row['group_number'],
            $row['direction'],
            $fio,
            $gender,
            $birthDate,
            $row['student_id']);
    }
    
    echo "└" . str_repeat("─", $widths['group']) . "┴"
                . str_repeat("─", $widths['direction']) . "┴"
                . str_repeat("─", $widths['fio']) . "┴"
                . str_repeat("─", $widths['gender']) . "┴"
                . str_repeat("─", $widths['birth_date']) . "┴"
                . str_repeat("─", $widths['student_id']) . "┘\n";
}
?>