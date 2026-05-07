<?php
class Caja
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function cajaActiva()
    {
        $sql = "SELECT c.*, CONCAT(u.nombre,' ',u.apellido) AS responsable
                FROM caja c
                LEFT JOIN usuario u ON u.id_usuario = c.id_usuario
                WHERE c.estado = 'Abierta' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function abrir($saldo_inicial, $id_usuario)
    {
        // Verificar que no haya caja abierta
        $stmt = $this->conn->prepare("SELECT id_caja FROM caja WHERE estado = 'Abierta' LIMIT 1");
        $stmt->execute();
        if ($stmt->fetch()) throw new Exception('Ya existe una caja abierta.');

        $sql  = "INSERT INTO caja (saldo_inicial, total_ingresos, total_egresos, fecha_apertura, estado, id_usuario)
                 VALUES (:si, 0, 0, NOW(), 'Abierta', :iu)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':si', $saldo_inicial);
        $stmt->bindValue(':iu', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function cerrar($id_caja, $saldo_final, $justificacion)
    {
        $stmt = $this->conn->prepare("SELECT * FROM caja WHERE id_caja = :id LIMIT 1");
        $stmt->bindParam(':id', $id_caja, PDO::PARAM_INT);
        $stmt->execute();
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$caja) throw new Exception('Caja no encontrada.');

        $saldo_teorico = $caja['saldo_inicial'] + ($caja['total_ingresos'] ?? 0) - ($caja['total_egresos'] ?? 0);
        $diferencia    = $saldo_final - $saldo_teorico;

        $sql  = "UPDATE caja SET saldo_final=:sf, diferencia=:dif, justificacion=:just,
                 fecha_cierre=NOW(), estado='Cerrada' WHERE id_caja=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':sf',   $saldo_final);
        $stmt->bindValue(':dif',  $diferencia);
        $stmt->bindValue(':just', $justificacion);
        $stmt->bindValue(':id',   $id_caja, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function registrarMovimiento($id_caja, $tipo, $monto, $concepto)
    {
        $sql  = "INSERT INTO movimientos_caja (id_caja, tipo, monto, concepto, fecha)
                 VALUES (:ic, :tipo, :monto, :concepto, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ic',      $id_caja, PDO::PARAM_INT);
        $stmt->bindValue(':tipo',    $tipo);
        $stmt->bindValue(':monto',   $monto);
        $stmt->bindValue(':concepto', $concepto);
        $stmt->execute();

        // Actualizar totales en caja
        $col  = $tipo === 'Ingreso' ? 'total_ingresos' : 'total_egresos';
        $this->conn->prepare("UPDATE caja SET {$col} = COALESCE({$col},0) + :m WHERE id_caja = :ic")
            ->execute([':m' => $monto, ':ic' => $id_caja]);
        return true;
    }

    public function movimientos($id_caja)
    {
        $sql  = "SELECT * FROM movimientos_caja WHERE id_caja = :id ORDER BY fecha ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_caja, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historial()
    {
        $sql = "SELECT c.*, CONCAT(u.nombre,' ',u.apellido) AS responsable
                FROM caja c LEFT JOIN usuario u ON u.id_usuario = c.id_usuario
                ORDER BY c.fecha_apertura DESC LIMIT 50";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saldoTeorico($id_caja)
    {
        $stmt = $this->conn->prepare("SELECT saldo_inicial, total_ingresos, total_egresos FROM caja WHERE id_caja = :id");
        $stmt->bindParam(':id', $id_caja, PDO::PARAM_INT);
        $stmt->execute();
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        return $c ? ($c['saldo_inicial'] + ($c['total_ingresos'] ?? 0) - ($c['total_egresos'] ?? 0)) : 0;
    }
}
