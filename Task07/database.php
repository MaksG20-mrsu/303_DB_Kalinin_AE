<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $this->pdo = new PDO('sqlite:university.db');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->createTables();
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    private function createTables() {

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_number VARCHAR(10) UNIQUE NOT NULL,
            direction VARCHAR(100) NOT NULL,
            graduation_year INTEGER NOT NULL
        )");
        
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            last_name VARCHAR(50) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            middle_name VARCHAR(50),
            gender VARCHAR(1) NOT NULL CHECK(gender IN ('M', 'F')),
            birth_date DATE NOT NULL,
            student_id VARCHAR(20) UNIQUE NOT NULL,
            group_id INTEGER NOT NULL,
            FOREIGN KEY (group_id) REFERENCES groups(id)
        )");
        

        $this->seedData();
    }
    
    private function seedData() {

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM groups");
        if ($stmt->fetchColumn() > 0) return;
        

        $groups = [
            ['ИВТ-21-1', 'Информатика и вычислительная техника', 2025],
            ['ИВТ-21-2', 'Информатика и вычислительная техника', 2025],
            ['ПИ-20-1', 'Прикладная информатика', 2024],
            ['ПИ-20-2', 'Прикладная информатика', 2024],
            ['БИ-22-1', 'Бизнес-информатика', 2026]
        ];
        
        foreach ($groups as $group) {
            $stmt = $this->pdo->prepare("INSERT INTO groups (group_number, direction, graduation_year) VALUES (?, ?, ?)");
            $stmt->execute($group);
        }
        

        $students = [
            ['Иванов', 'Иван', 'Иванович', 'M', '2002-05-15', '2021ИВТ001', 1],
            ['Петров', 'Петр', 'Петрович', 'M', '2001-08-20', '2021ИВТ002', 1],
            ['Сидорова', 'Мария', 'Сергеевна', 'F', '2002-03-10', '2021ИВТ003', 1],
            ['Козлов', 'Алексей', 'Дмитриевич', 'M', '2001-11-30', '2021ИВТ004', 2],
            ['Смирнова', 'Ольга', 'Владимировна', 'F', '2002-01-25', '2021ИВТ005', 2],
            ['Николаев', 'Дмитрий', 'Александрович', 'M', '2000-07-12', '2020ПИ001', 3],
            ['Федорова', 'Екатерина', 'Игоревна', 'F', '2000-09-18', '2020ПИ002', 3],
            ['Васнецов', 'Артем', 'Олегович', 'M', '2002-12-05', '2022БИ001', 5]
        ];
        
        $stmt = $this->pdo->prepare("INSERT INTO students (last_name, first_name, middle_name, gender, birth_date, student_id, group_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($students as $student) {
            $stmt->execute($student);
        }
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}
?>