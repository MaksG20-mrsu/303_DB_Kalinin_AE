#!/usr/bin/env python3
import csv
import re
import sqlite3
from datetime import datetime

def parse_movie_title(title):
    """Парсит название фильма и извлекает год"""
    match = re.search(r'(.+)\s+\((\d{4})\)', title)
    if match:
        movie_title = match.group(1).strip().replace("'", "''")
        year = int(match.group(2))
        return movie_title, year
    return title.replace("'", "''"), None

def generate_sql():
    sql_commands = []
    
    sql_commands.append("DROP TABLE IF EXISTS movies;")
    sql_commands.append("DROP TABLE IF EXISTS ratings;")
    sql_commands.append("DROP TABLE IF EXISTS tags;")
    sql_commands.append("DROP TABLE IF EXISTS users;")
    
    sql_commands.append("""
    CREATE TABLE movies (
        id INTEGER PRIMARY KEY,
        title TEXT NOT NULL,
        year INTEGER,
        genres TEXT
    );
    """)
    
    sql_commands.append("""
    CREATE TABLE ratings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        movie_id INTEGER,
        rating REAL,
        timestamp INTEGER
    );
    """)
    
    sql_commands.append("""
    CREATE TABLE tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        movie_id INTEGER,
        tag TEXT,
        timestamp INTEGER
    );
    """)
    
    sql_commands.append("""
    CREATE TABLE users (
        id INTEGER PRIMARY KEY,
        name TEXT,
        email TEXT,
        gender TEXT,
        register_date TEXT,
        occupation TEXT
    );
    """)
    
    print("Обработка movies.csv...")
    with open('dataset/movies.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            title, year = parse_movie_title(row['title'])
            genres = row['genres'].replace("'", "''")
            sql = f"INSERT INTO movies (id, title, year, genres) VALUES ({row['movieId']}, '{title}', {year if year else 'NULL'}, '{genres}');"
            sql_commands.append(sql)
    
    print("Обработка ratings.csv...")
    with open('dataset/ratings.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            sql = f"INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES ({row['userId']}, {row['movieId']}, {row['rating']}, {row['timestamp']});"
            sql_commands.append(sql)
    
    print("Обработка tags.csv...")
    with open('dataset/tags.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            tag = row['tag'].replace("'", "''")
            sql = f"INSERT INTO tags (user_id, movie_id, tag, timestamp) VALUES ({row['userId']}, {row['movieId']}, '{tag}', {row['timestamp']});"
            sql_commands.append(sql)
    
    
    print("Обработка users.txt...")
    with open('dataset/users.txt', 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line:
                continue
            
            parts = line.split('|')
            if len(parts) >= 5:
                user_id = parts[0]
                name = parts[1].replace("'", "''")
                email = parts[2].replace("'", "''")
                gender = parts[3]
                register_date = parts[4]
                occupation = parts[5]
                
                sql = f"INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES ({user_id}, '{name}', '{email}', '{gender}', '{register_date}', '{occupation}');"
                sql_commands.append(sql)

    
        print("Запись SQL в файл...")
    with open('db_init.sql', 'w', encoding='utf-8') as f:
        for i in range(0, len(sql_commands), 1000):
            chunk = sql_commands[i:i+1000]
            f.write('\n'.join(chunk) + '\n')
            print(f"Записано {i+1000}/{len(sql_commands)} команд")
        
    print("Генерация SQL завершена!")

if __name__ == "__main__":
    generate_sql()
