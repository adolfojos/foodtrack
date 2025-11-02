-- Crear base de datos (usar guion bajo en lugar de guion medio)
CREATE DATABASE foodtrack_db;
USE foodtrack_db;

-- Tabla de users del sistema
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','inspector','parish_spokesperson','school_spokesperson','director') NOT NULL DEFAULT 'director',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de instituciones educativas
CREATE TABLE institutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parish VARCHAR(50),
    responsible VARCHAR(100),
    phone VARCHAR(20),
    active BOOLEAN DEFAULT TRUE
);

-- Tabla de actores (inspectores, voceros, directores)
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin','inspector','parish_spokesperson','school_spokesperson','director') NOT NULL,
    institution_id INT NULL,
    user_id INT NULL,
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Recepciones mensuales desde MERCAL
CREATE TABLE receptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    inspector_id INT NOT NULL,
    parish_spokesperson_id INT NOT NULL,
    notes TEXT,
    total_bags INT NOT NULL,
    FOREIGN KEY (inspector_id) REFERENCES actors(id),
    FOREIGN KEY (parish_spokesperson_id) REFERENCES actors(id)
);

-- Entregas a instituciones
CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    institution_id INT NOT NULL,
    reception_id INT NULL,
    receiver_id INT NOT NULL,
    qty_groceries INT DEFAULT 0,
    qty_proteins INT DEFAULT 0,
    qty_fruits INT DEFAULT 0,
    qty_vegetables INT DEFAULT 0,
    receiver_signature VARCHAR(100),
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (reception_id) REFERENCES receptions(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES actors(id),
    INDEX idx_deliveries_institution (institution_id),
    INDEX idx_deliveries_date (date)
);

-- Reportes diarios de consumo
CREATE TABLE daily_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    institution_id INT NOT NULL,
    delivery_id INT NULL,
    spokesperson_id INT NOT NULL,
    menu TEXT,
    used_groceries INT DEFAULT 0,
    used_proteins INT DEFAULT 0,
    used_fruits INT DEFAULT 0,
    used_vegetables INT DEFAULT 0,
    students_served INT DEFAULT 0,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE SET NULL,
    FOREIGN KEY (spokesperson_id) REFERENCES actors(id),
    INDEX idx_reports_institution (institution_id),
    INDEX idx_reports_date (date)
);
