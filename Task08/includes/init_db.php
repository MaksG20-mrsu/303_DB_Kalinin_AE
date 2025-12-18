<?php
// includes/init_db.php
function initDatabase($db) {
    // Группы
    $db->exec("CREATE TABLE IF NOT EXISTS groups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        group_number VARCHAR(10) UNIQUE NOT NULL,
        speciality TEXT,
        year_start INTEGER
    )");
    
    // Студенты
    $db->exec("CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        last_name TEXT NOT NULL,
        first_name TEXT NOT NULL,
        middle_name TEXT,
        group_id INTEGER,
        gender TEXT CHECK(gender IN ('М', 'Ж')),
        birth_date DATE,
        FOREIGN KEY (group_id) REFERENCES groups(id)
    )");
    
    // Предметы
    $db->exec("CREATE TABLE IF NOT EXISTS subjects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        semester INTEGER,
        year_of_study INTEGER,
        speciality TEXT
    )");
    
    // Экзамены
    $db->exec("CREATE TABLE IF NOT EXISTS exams (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL,
        subject_id INTEGER NOT NULL,
        exam_date DATE NOT NULL,
        grade INTEGER CHECK(grade BETWEEN 2 AND 5),
        teacher TEXT,
        FOREIGN KEY (student_id) REFERENCES students(id),
        FOREIGN KEY (subject_id) REFERENCES subjects(id)
    )");
    
    // Наполнение тестовыми данными (если таблицы пустые)
    $stmt = $db->query("SELECT COUNT(*) FROM groups");
    if ($stmt->fetchColumn() == 0) {
        // Добавляем группы
        $groups = [
            ['ИВТ-101', 'Информатика', 2023],
            ['ИВТ-102', 'Информатика', 2023],
            ['ПМИ-101', 'Математика', 2023],
            ['ФИЗ-101', 'Физика', 2023]
        ];
        
        $stmt = $db->prepare("INSERT INTO groups (group_number, speciality, year_start) VALUES (?, ?, ?)");
        foreach ($groups as $group) {
            $stmt->execute($group);
        }
        
        // Добавляем предметы
        $subjects = [
            ['Математический анализ', 1, 1, 'Информатика'],
            ['Программирование', 1, 1, 'Информатика'],
            ['Физика', 2, 1, 'Информатика'],
            ['Базы данных', 3, 2, 'Информатика']
        ];
        
        $stmt = $db->prepare("INSERT INTO subjects (name, semester, year_of_study, speciality) VALUES (?, ?, ?, ?)");
        foreach ($subjects as $subject) {
            $stmt->execute($subject);
        }
    }
}
?>