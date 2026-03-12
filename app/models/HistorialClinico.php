<?php

require_once __DIR__ . '/../core/Model.php';

class HistorialClinico extends Model {

    public function obtenerPorPaciente($pacienteId){
        $sql = "SELECT h.*,
                       CONCAT(m.nombre, ' ', m.apellido) AS medico_nombre
                FROM historial_clinico h
                INNER JOIN medicos m ON h.medico_id = m.id
                WHERE h.paciente_id = :paciente_id
                ORDER BY h.fecha_registro DESC, h.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':paciente_id' => $pacienteId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data){
        $sql = "INSERT INTO historial_clinico
                (paciente_id, medico_id, cita_id, motivo_consulta, sintomas, diagnostico, tratamiento, observaciones, fecha_registro)
                VALUES
                (:paciente_id, :medico_id, :cita_id, :motivo_consulta, :sintomas, :diagnostico, :tratamiento, :observaciones, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':paciente_id' => $data['paciente_id'],
            ':medico_id' => $data['medico_id'],
            ':cita_id' => !empty($data['cita_id']) ? $data['cita_id'] : null,
            ':motivo_consulta' => $data['motivo_consulta'],
            ':sintomas' => $data['sintomas'],
            ':diagnostico' => $data['diagnostico'],
            ':tratamiento' => $data['tratamiento'],
            ':observaciones' => $data['observaciones']
        ]);
    }
}