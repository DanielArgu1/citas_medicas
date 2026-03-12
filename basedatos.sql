CREATE DATABASE IF NOT EXISTS citas_medicas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE citas_medicas;

DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS pacientes;
DROP TABLE IF EXISTS medicos;

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(15) NOT NULL UNIQUE,
    telefono VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    fecha_nacimiento DATE NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','medico','recepcion') NOT NULL,
    medico_id INT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_medico FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    motivo VARCHAR(250) NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_citas_medico FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_medico_fecha (medico_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento) VALUES
('María', 'González', '0801-1990-12345', '9876-5432', 'maria.gonzalez@email.com', '1990-05-15'),
('Carlos', 'Martínez', '0801-1985-54321', '9876-5433', 'carlos.martinez@email.com', '1985-08-20'),
('Ana', 'López', '0801-1992-11111', '9876-5434', 'ana.lopez@email.com', '1992-03-10'),
('José', 'Hernández', '0801-1988-22222', '9876-5435', 'jose.hernandez@email.com', '1988-11-25'),
('Laura', 'Rodríguez', '0801-1995-33333', '9876-5436', 'laura.rodriguez@email.com', '1995-07-05');

INSERT INTO medicos (nombre, apellido, especialidad, telefono, email) VALUES
('Roberto', 'Flores', 'Medicina General', '2234-5678', 'dr.flores@hospital.com'),
('Patricia', 'Sánchez', 'Pediatría', '2234-5679', 'dra.sanchez@hospital.com'),
('Miguel', 'Ramírez', 'Cardiología', '2234-5680', 'dr.ramirez@hospital.com');

INSERT INTO usuarios (nombre, email, password, rol, medico_id, estado) VALUES
('Administrador General', 'admin@admin.com', '$2y$12$BRqZHXxJT43DaW3Pn8wLy.tFjO.voeHj5SNZG.MvrNSu4N6mxVtrS', 'admin', NULL, 'activo'),
('Recepción Clínica', 'recepcion@clinica.com', '$2y$12$BRqZHXxJT43DaW3Pn8wLy.tFjO.voeHj5SNZG.MvrNSu4N6mxVtrS', 'recepcion', NULL, 'activo'),
('Roberto Flores', 'dr.flores@hospital.com', '$2y$12$BRqZHXxJT43DaW3Pn8wLy.tFjO.voeHj5SNZG.MvrNSu4N6mxVtrS', 'medico', 1, 'activo'),
('Patricia Sánchez', 'dra.sanchez@hospital.com', '$2y$12$BRqZHXxJT43DaW3Pn8wLy.tFjO.voeHj5SNZG.MvrNSu4N6mxVtrS', 'medico', 2, 'activo'),
('Miguel Ramírez', 'dr.ramirez@hospital.com', '$2y$12$BRqZHXxJT43DaW3Pn8wLy.tFjO.voeHj5SNZG.MvrNSu4N6mxVtrS', 'medico', 3, 'activo');

INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, motivo, estado) VALUES
(1, 1, CURDATE(), '09:00:00', '09:30:00', 'Control de rutina', 'pendiente'),
(2, 2, CURDATE(), '10:00:00', '10:30:00', 'Vacunación infantil', 'pendiente'),
(3, 3, CURDATE(), '11:00:00', '11:30:00', 'Dolor en el pecho', 'completada'),
(4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '14:30:00', 'Seguimiento de tratamiento', 'pendiente'),
(5, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:30:00', '16:00:00', 'Consulta pediátrica', 'pendiente');

CREATE TABLE historial_clinico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    cita_id INT NULL,
    motivo_consulta TEXT NOT NULL,
    sintomas TEXT NULL,
    diagnostico TEXT NOT NULL,
    tratamiento TEXT NULL,
    observaciones TEXT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_historial_medico FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    CONSTRAINT fk_historial_cita FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE SET NULL
);

INSERT INTO historial_clinico 
(paciente_id, medico_id, cita_id, motivo_consulta, sintomas, diagnostico, tratamiento, observaciones, fecha_registro)
VALUES
(1, 1, NULL, 'Dolor de cabeza frecuente', 'Cefalea, cansancio, sensibilidad a la luz', 'Migraña', 'Ibuprofeno 400 mg cada 8 horas por 3 días', 'Paciente debe evitar estrés y desvelos', NOW()),

(1, 2, NULL, 'Control general', 'Ningún síntoma importante', 'Paciente estable', 'Continuar controles cada 6 meses', 'Se recomienda mejorar alimentación', NOW()),

(2, 1, NULL, 'Dolor estomacal', 'Ardor, náuseas leves', 'Gastritis', 'Omeprazol 20 mg diario por 14 días', 'Evitar comidas irritantes', NOW()),

(2, 2, NULL, 'Revisión de seguimiento', 'Mejoría parcial', 'Gastritis en control', 'Continuar tratamiento por 7 días más', 'Paciente responde bien al medicamento', NOW()),

(3, 1, NULL, 'Tos y fiebre', 'Tos seca, fiebre de 38.5, dolor de garganta', 'Infección respiratoria', 'Amoxicilina 500 mg cada 8 horas por 7 días', 'Reposo e hidratación', NOW()),

(3, 2, NULL, 'Chequeo posterior', 'Persistencia de tos leve', 'Recuperación favorable', 'Jarabe para la tos por 5 días', 'No presenta complicaciones', NOW());



INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento) VALUES
('Daniel', 'Vásquez', '0801-1991-44444', '9876-5440', 'daniel.vasquez@email.com', '1991-04-18'),
('Sofía', 'Mejía', '0801-1993-55555', '9876-5441', 'sofia.mejia@email.com', '1993-09-22'),
('Fernando', 'Cruz', '0801-1987-66666', '9876-5442', 'fernando.cruz@email.com', '1987-01-30'),
('Valeria', 'Pineda', '0801-1998-77777', '9876-5443', 'valeria.pineda@email.com', '1998-12-11'),
('Ricardo', 'Morales', '0801-1984-88888', '9876-5444', 'ricardo.morales@email.com', '1984-06-07');

INSERT INTO historial_clinico 
(paciente_id, medico_id, cita_id, motivo_consulta, sintomas, diagnostico, tratamiento, observaciones, fecha_registro)
VALUES
(14, 1, NULL, 'Dolor de garganta', 'Ardor al tragar, fiebre leve, malestar general', 'Faringitis', 'Amoxicilina 500 mg cada 8 horas por 7 días', 'Paciente debe guardar reposo e hidratarse', NOW()),

(14, 3, NULL, 'Control posterior', 'Molestia leve en garganta, sin fiebre', 'Faringitis en mejoría', 'Completar antibiótico y continuar líquidos tibios', 'Evolución favorable', NOW()),

(15, 2, NULL, 'Fiebre y congestión', 'Congestión nasal, tos leve, fiebre de 38 grados', 'Resfriado común', 'Paracetamol 500 mg cada 8 horas por 3 días', 'No presenta signos de alarma', NOW()),

(15, 1, NULL, 'Chequeo general', 'Sin síntomas importantes', 'Paciente estable', 'No requiere tratamiento farmacológico', 'Se recomienda mejorar hábitos de sueño', NOW()),

(16, 3, NULL, 'Dolor en el pecho', 'Opresión leve en el pecho, cansancio al caminar', 'Angina leve', 'Aspirina 100 mg diaria y reposo relativo', 'Se recomienda evaluación cardiológica continua', NOW()),

(16, 1, NULL, 'Seguimiento cardiológico', 'Menor dolor torácico, mejor tolerancia al esfuerzo', 'Angina en control', 'Continuar tratamiento indicado', 'Paciente con respuesta favorable', NOW()),

(17, 2, NULL, 'Dolor abdominal', 'Cólicos, náuseas leves, falta de apetito', 'Gastroenteritis', 'Suero oral y dieta blanda por 3 días', 'Evitar comidas grasosas y lácteos', NOW()),

(17, 1, NULL, 'Revisión de seguimiento', 'Disminución de dolor abdominal, tolera alimentos', 'Gastroenteritis en recuperación', 'Continuar dieta blanda e hidratación', 'Buena evolución clínica', NOW()),

(18, 3, NULL, 'Mareo frecuente', 'Mareos al levantarse, visión borrosa ocasional', 'Hipotensión', 'Aumentar consumo de agua y control de presión', 'Se recomienda levantarse lentamente', NOW()),

(18, 1, NULL, 'Control general', 'Menos episodios de mareo', 'Hipotensión en observación', 'Continuar recomendaciones generales', 'Paciente estable actualmente', NOW());

INSERT INTO historial_clinico 
(paciente_id, medico_id, cita_id, motivo_consulta, sintomas, diagnostico, tratamiento, observaciones, fecha_registro)
VALUES
(1, 3, NULL, 'Dolor en el pecho al esfuerzo', 'Presión en el pecho, fatiga leve', 'Dolor torácico no específico', 'Reposo y evaluación cardiológica', 'Vigilar aparición de síntomas más fuertes', NOW()),

(2, 3, NULL, 'Palpitaciones', 'Latidos acelerados, ansiedad leve', 'Taquicardia pasajera', 'Control de signos vitales y reposo', 'Evitar cafeína y bebidas energéticas', NOW()),

(3, 2, NULL, 'Dolor de oído', 'Molestia en oído derecho, fiebre leve', 'Otitis media', 'Amoxicilina 500 mg cada 8 horas por 7 días', 'Mantener control si persiste dolor', NOW()),

(4, 1, NULL, 'Insomnio', 'Dificultad para dormir, cansancio diurno', 'Trastorno leve del sueño', 'Higiene del sueño y control en 2 semanas', 'Evitar uso de pantallas en la noche', NOW()),

(5, 3, NULL, 'Dolor de cabeza y mareo', 'Cefalea frontal, mareo leve', 'Cefalea tensional', 'Ibuprofeno 400 mg cada 8 horas por 3 días', 'Reducir estrés y descansar mejor', NOW());