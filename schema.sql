-- Crear base de datos (usar guion bajo en lugar de guion medio)
CREATE DATABASE foodtrack_db;
USE foodtrack_db;

-- Crear tablas principales del sistema de seguimiento de alimentos 

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','inspector','vocero_parroquial','vocero_institucional','director','cocinero') NOT NULL DEFAULT 'cocinero',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Tabla de instituciones
CREATE TABLE institutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    campus_code VARCHAR(20),
    sica_code VARCHAR(20),
    municipality VARCHAR(50),
    parish VARCHAR(50),
    director VARCHAR(100),
    phone VARCHAR(20),
    total_enrollment INT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Tabla de actores institucionales
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Personal information
    full_name VARCHAR(100) NOT NULL,
    national_id VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100),
    phone VARCHAR(20),

    -- Role and associations
    role ENUM('admin','inspector','vocero_parroquial','vocero_institucional','director','cocinero') NOT NULL DEFAULT 'cocinero',
    user_id INT NULL,
    institution_id INT NULL,

    -- Status and timestamps
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE SET NULL
);


CREATE TABLE receptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    inspector_id INT NOT NULL,
    vocero_parroquial_id INT NOT NULL,
    notes TEXT,
    reception_type ENUM('CLAP', 'FRUVERT', 'PROTEINA', 'OTRO') NOT NULL, /* ¡NUEVO! */
    summary_quantity INT NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inspector_id) REFERENCES actors(id),
    FOREIGN KEY (vocero_parroquial_id) REFERENCES actors(id)
);

CREATE TABLE reception_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
  
  /* Llave foránea para saber a qué recepción pertenece este item */
  reception_id INT NOT NULL,
  
  /* Descripción del producto: 'Bolsa CLAP', 'Cambur', 'Pollo', 'Plátano' */
  product_name VARCHAR(255) NOT NULL,
  
  /* La cantidad (puede ser peso o conteo). 
     Usamos DECIMAL para soportar 1.5 toneladas */
  quantity DECIMAL(10, 3) NOT NULL,
  
  /* La unidad de medida: 'BOLSAS', 'TONELADAS', 'KILOS', 'UNIDADES' */
  unit VARCHAR(50) NOT NULL,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (reception_id) REFERENCES receptions(id)
    ON DELETE CASCADE /* Si se borra la recepción, se borran sus items */
);

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (reception_id) REFERENCES receptions(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES actors(id),
    INDEX idx_deliveries_institution (institution_id),
    INDEX idx_deliveries_date (date)
);

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE SET NULL,
    FOREIGN KEY (spokesperson_id) REFERENCES actors(id),
    INDEX idx_reports_institution (institution_id),
    INDEX idx_reports_date (date)
);
