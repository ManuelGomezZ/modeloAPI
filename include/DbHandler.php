<?php

class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . './DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
 
    public function createAuto($patente,$vehiculo, $estado, $valorPermiso, $intereses, $multa)
    {

       // print_r($array);die;
        $queryVehiculo = "INSERT INTO vehiculos (patente, vehiculo, estado ,fecha_creacion, fecha_modificacion) VALUES('$patente', '$vehiculo', $estado,  now(), now())";

        $stmt = $this->conn->prepare($queryVehiculo);

        $stmt->execute();

        $vehiculoLastId = "SELECT MAX(id) as last FROM vehiculos;";
        $stmt = $this->conn->prepare($vehiculoLastId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $LastID= $result[0]['last'];
        // print_r($result);die;

        $queryPermiso = "INSERT INTO permisos (vehiculo_patente, valor_permiso, fecha_creacion, fecha_modificacion, estado, vehiculo_id) VALUES ('$patente', $valorPermiso, NOW(), NOW(),1,$LastID )";
        $stmt = $this->conn->prepare($queryPermiso);
        $stmt->execute();

        $queryMulta = "INSERT INTO multas (vehiculo_patente, multa_impaga, intereses_reajustes, fecha_creacion, fecha_modificacion, vehiculo_id) VALUES ('$patente', $multa, $intereses, NOW(), NOW(),$LastID )";
        $stmt = $this->conn->prepare($queryMulta);
        $stmt->execute();
        //return $stmt;
    }
    public function updateDatos($patente,$vehiculo, $valorPermiso, $vehiculoId, $intereses, $multa)
    {

        $sql= "UPDATE vehiculos SET vehiculos.patente = '$patente', vehiculos.vehiculo = '$vehiculo' WHERE vehiculos.id = $vehiculoId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $sql= "UPDATE permisos SET vehiculo_patente = '$patente', valor_permiso = $valorPermiso, fecha_modificacion = now() WHERE vehiculo_id = $vehiculoId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $sql= "UPDATE multas SET vehiculo_patente = '$patente', multa_impaga = $multa, intereses_reajustes = $intereses, fecha_modificacion = now() WHERE vehiculo_id = $vehiculoId";
        //print_r($sql);die;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }

 
}
 
?>