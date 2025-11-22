-- Crear base de datos (usar guion bajo en lugar de guion medio)
CREATE DATABASE foodtrack_db;
USE foodtrack_db;

-- 1. Tabla de Usuarios (Solo para autenticación)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tabla de actores institucionales (Contiene el rol)
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    national_id VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'inspector', 'vocero_parroquial', 'vocero_institucional', 'director', 'cocinero') NOT NULL DEFAULT 'cocinero',
    user_id INT NULL UNIQUE, -- Debe ser UNIQUE para 1:1 con users
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 3. Tabla de instituciones
CREATE TABLE institutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    campus_code VARCHAR(20) DEFAULT NULL,
    sica_code VARCHAR(20) DEFAULT NULL,
    municipality VARCHAR(50) DEFAULT NULL, -- Normalizar si es posible
    parish VARCHAR(50) DEFAULT NULL,       -- Normalizar si es posible
    phone VARCHAR(20) DEFAULT NULL,
    total_enrollment INT DEFAULT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Tabla de instituciones y sus actores relacionados (Muchos a Muchos)
CREATE TABLE actor_institution (
    actor_id INT NOT NULL,
    institution_id INT NOT NULL,
    PRIMARY KEY (actor_id, institution_id),
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- 5. Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    default_unit VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Tabla de recepciones de alimentos (QUITADO summary_quantity)
CREATE TABLE receptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    inspector_id INT NOT NULL,
    vocero_parroquial_id INT NOT NULL,
    notes TEXT,
    reception_type ENUM('CLAP', 'FRUVERT', 'PROTEINA', 'OTRO') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inspector_id) REFERENCES actors(id),
    FOREIGN KEY (vocero_parroquial_id) REFERENCES actors(id)
);
-- 1. Agregar la columna que faltaba en receptions
ALTER TABLE receptions ADD COLUMN summary_quantity INT DEFAULT 0 AFTER reception_type;

-- 7. Tabla de items detallados en cada recepción
CREATE TABLE reception_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reception_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,3) NOT NULL,
    unit VARCHAR(50) NOT NULL, -- Se mantiene 'unit' para flexibilidad
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reception_id) REFERENCES receptions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY uk_reception_product (reception_id, product_id) -- Asegura un solo item por producto en la recepción
);

-- 8. Tabla de entregas de alimentos (Se mantienen los resúmenes por desnormalización controlada)
CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    institution_id INT NOT NULL,
    reception_id INT DEFAULT NULL,
    receiver_id INT NOT NULL,
    qty_groceries INT DEFAULT 0,
    qty_proteins INT DEFAULT 0,
    qty_fruits INT DEFAULT 0,
    qty_vegetables INT DEFAULT 0,
    receiver_signature VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (reception_id) REFERENCES receptions(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES actors(id)
);

-- 9. Tabla de items detallados en cada entrega
CREATE TABLE delivery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,3) NOT NULL,
    unit VARCHAR(50) NOT NULL, -- Se mantiene 'unit' para flexibilidad
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY uk_delivery_product (delivery_id, product_id) -- Asegura un solo item por producto en la entrega
);

-- 10. Tabla de reportes diarios de consumo (Se mantienen los resúmenes por desnormalización controlada)
CREATE TABLE daily_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    institution_id INT NOT NULL,
    delivery_id INT NULL,
    spokesperson_id INT NOT NULL, -- vocero_institucional o cocinero, validar en la app
    menu TEXT,
    used_groceries INT DEFAULT 0,
    used_proteins INT DEFAULT 0,
    used_fruits INT DEFAULT 0,
    used_vegetables INT DEFAULT 0,
    students_served INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE SET NULL,
    FOREIGN KEY (spokesperson_id) REFERENCES actors(id),
    INDEX idx_reports_institution (institution_id),
    INDEX idx_reports_date (date),
    UNIQUE KEY uk_daily_report (date, institution_id) -- Un reporte por día por institución
);