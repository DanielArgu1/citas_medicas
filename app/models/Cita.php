<?php

require_once __DIR__ . '/../core/Model.php';

class Cita extends Model {

    public function obtenerTodas(){

        $sql = "SELECT 
                c.id,
                CONCAT(p.nombre,' ',p.apellido) AS paciente,
                CONCAT(m.nombre,' ',m.apellido) AS medico,
                c.fecha,
                c.hora_inicio,
                c.hora_fin,
                c.estado
                FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                JOIN medicos m ON c.medico_id = m.id
                ORDER BY c.fecha DESC, c.hora_inicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function verificarDisponibilidad($medico_id,$fecha,$hora_inicio,$hora_fin){

        $sql = "SELECT COUNT(*) AS total
                FROM citas
                WHERE medico_id = :medico_id
                AND fecha = :fecha
                AND estado != 'cancelada'
                AND (
                    hora_inicio < :hora_fin 
                    AND hora_fin > :hora_inicio
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':medico_id'=>$medico_id,
            ':fecha'=>$fecha,
            ':hora_inicio'=>$hora_inicio,
            ':hora_fin'=>$hora_fin
        ]);

        $row = $stmt->fetch();

        return $row['total'] == 0;
    }

    public function crear($data){

        if(!$this->verificarDisponibilidad(
            $data['medico_id'],
            $data['fecha'],
            $data['hora_inicio'],
            $data['hora_fin']
        )){
            return "ocupado";
        }

        $sql = "INSERT INTO citas
        (paciente_id, medico_id, fecha, hora_inicio, hora_fin, motivo)
        VALUES
        (:paciente_id, :medico_id, :fecha, :hora_inicio, :hora_fin, :motivo)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':paciente_id'=>$data['paciente_id'],
            ':medico_id'=>$data['medico_id'],
            ':fecha'=>$data['fecha'],
            ':hora_inicio'=>$data['hora_inicio'],
            ':hora_fin'=>$data['hora_fin'],
            ':motivo'=>$data['motivo']
        ]);

        return "ok";
    }

}