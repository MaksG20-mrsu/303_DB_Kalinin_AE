DROP TABLE IF EXISTS Grades;
DROP TABLE IF EXISTS StudyPlan;
DROP TABLE IF EXISTS Students;
DROP TABLE IF EXISTS Groups;
DROP TABLE IF EXISTS Subjects;
DROP TABLE IF EXISTS Directions;

CREATE TABLE Directions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    degree TEXT CHECK(degree IN ('Бакалавриат', 'Магистратура')) NOT NULL,
    UNIQUE(name, degree)
);

CREATE TABLE Groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    admission_year INTEGER NOT NULL,
    direction_id INTEGER NOT NULL,
    FOREIGN KEY (direction_id) REFERENCES Directions(id) ON DELETE RESTRICT
);

CREATE TABLE Students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    birth_date DATE NOT NULL,
    gender TEXT CHECK(gender IN ('М', 'Ж')) NOT NULL,
    group_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES Groups(id) ON DELETE CASCADE
);

CREATE TABLE Subjects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE StudyPlan (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL,
    semester INTEGER NOT NULL,
    lecture_hours INTEGER DEFAULT 0,
    practice_hours INTEGER DEFAULT 0,
    control_type TEXT CHECK(control_type IN ('Экзамен', 'Зачет', 'Дифференцированный зачет')) NOT NULL,
    FOREIGN KEY (group_id) REFERENCES Groups(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES Subjects(id) ON DELETE RESTRICT
);

CREATE TABLE Grades (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    study_plan_id INTEGER NOT NULL,
    score INTEGER CHECK(score >= 0 AND score <= 5), 
    exam_date DATE,
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE,
    FOREIGN KEY (study_plan_id) REFERENCES StudyPlan(id) ON DELETE CASCADE
);

INSERT INTO Directions (name, degree) VALUES 
('Фундаментальная информатика и информационные технологии', 'Бакалавриат'), 
('Прикладная математика и информатика', 'Бакалавриат'),                  
('Программная инженерия', 'Бакалавриат'),                                
('Прикладная математика и информатика', 'Магистратура'),                
('Программная инженерия', 'Магистратура');                               

INSERT INTO Groups (name, admission_year, direction_id) VALUES 
('101', 2025, 1), 
('201', 2024, 1), 
('301', 2023, 1), 
('401', 2022, 1), 
('104', 2025, 3),
('204', 2024, 3); 

INSERT INTO Groups (name, admission_year, direction_id) VALUES 
('101М', 2025, 4),
('201М', 2024, 4);

-- --- ПРЕДМЕТЫ ---
INSERT INTO Subjects (name) VALUES 
('Математический анализ'),          
('Основы программирования'),       
('Иностранный язык'),               
('История России'),                 
('Линейная алгебра'),               
('Физическая культура'),            
('Базы данных'),                    
('Операционные системы'),           
('Методы оптимизации'),             
('Машинное обучение'),              
('Философия'),                      
('Вычислительная математика');

INSERT INTO StudyPlan (group_id, subject_id, semester, lecture_hours, practice_hours, control_type) VALUES 
(1, 1, 1, 36, 36, 'Экзамен'), 
(1, 2, 1, 18, 54, 'Дифференцированный зачет'),
(1, 3, 1, 0, 72, 'Зачет'),    
(1, 4, 1, 36, 0, 'Зачет'),    
(1, 6, 1, 0, 100, 'Зачет');   

INSERT INTO StudyPlan (group_id, subject_id, semester, lecture_hours, practice_hours, control_type) VALUES 
(6, 7, 3, 36, 36, 'Экзамен'), 
(6, 8, 3, 36, 36, 'Экзамен'), 
(6, 6, 3, 0, 100, 'Зачет');   

INSERT INTO StudyPlan (group_id, subject_id, semester, lecture_hours, practice_hours, control_type) VALUES 
(7, 9, 1, 36, 18, 'Экзамен'), 
(7, 10, 1, 36, 36, 'Экзамен'), 
(7, 12, 1, 0, 200, 'Зачет');  

INSERT INTO Students (full_name, birth_date, gender, group_id) VALUES 
('Иванов Иван Иванович', '2007-05-15', 'М', 1),
('Петрова Мария Сергеевна', '2007-03-20', 'Ж', 1),
('Сидоров Алексей Петрович', '2006-12-01', 'М', 1),

('Смирнова Елена Дмитриевна', '2006-01-10', 'Ж', 6),
('Кузнецов Игорь Владимирович', '2005-11-11', 'М', 6),

('Волкова Ольга Александровна', '2003-02-15', 'Ж', 7),
('Морозов Дмитрий Евгеньевич', '2003-06-22', 'М', 7);

INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES 
(1, 1, 5, '2026-01-15'), 
(1, 2, 5, '2025-12-25'), 
(1, 3, 1, '2025-12-20'), 
(1, 4, 1, '2025-12-18'), 
(1, 5, 1, '2025-12-15'); 

INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES 
(2, 1, 4, '2026-01-15'), 
(2, 2, 4, '2025-12-25'), 
(2, 3, 1, '2025-12-20'), 
(2, 4, 1, '2025-12-18'), 
(2, 5, 1, '2025-12-15'); 

INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES 
(3, 1, 3, '2026-01-15'), 
(3, 2, 3, '2025-12-25'), 
(3, 3, 1, '2025-12-20'), 
(3, 4, 0, '2025-12-18'), 
(3, 5, 0, '2025-12-15'); 

INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES 
(4, 6, 5, '2026-01-20'), 
(4, 7, 5, '2026-01-24'), 
(4, 8, 1, '2025-12-28'), 

(5, 6, 4, '2026-01-20'), 
(5, 7, 2, '2026-01-24'), 
(5, 8, 1, '2025-12-28'); 

INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES 
(6, 9, 5, '2026-01-15'),  
(6, 10, 5, '2026-01-19'), 
(6, 11, 1, '2025-12-25'); 