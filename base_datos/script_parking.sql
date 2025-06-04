CREATE DATABASE IF NOT EXISTS parking CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE parking;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS plazas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    bloqueada BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    plaza_id INT NOT NULL,
    fecha DATE NOT NULL,
    franja ENUM('mañana', 'tarde', 'día completo') NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (plaza_id) REFERENCES plazas(id) ON DELETE CASCADE
);

INSERT INTO plazas (nombre, bloqueada) VALUES
('P01', 1),
('P02', 0),
('P03', 1),  
('P04', 0),
('P05', 0),
('P06', 1),  
('P07', 0),
('P08', 0),
('P09', 0),
('P10', 0),
('P11', 0),
('P12', 0),
('P13', 0),
('P14', 0),
('P15', 0),
('P16', 0),
('P17', 0),
('P18', 0),
('P19', 0),
('P20', 1),
('P21', 0),
('P22', 0),
('P23', 0),
('P24', 0),
('P25', 1);
