<?php 
require '../config/config.php';
require '../config/database.php';
$db = new Database();
$con = $db->conectar();

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

echo '<pre>';
print_r($datos);
echo '</pre>';

if(is_array($datos)) {
    $id_transaccion = $datos['detalles']['id'];
    $yhteensä = $datos['detalles']['purchase_units'][0]['amount']['value'];
    $status = $datos['detalles']['status'];
    $päivämäärä = $datos['detalles']['update_time'];
    $päivämäärä_uusi = date('Y-m-d H:i:s', strtotime($päivämäärä));
    $email = $datos['detalles']['payer']['email_address'];
    $id_asiakas = $datos['detalles']['payer']['payer_id'];

    $sql = $con->prepare("INSERT INTO compra (id_transaccion, päivämäärä, status, email, id_asiakas, yhteensä) VALUES (?,?,?,?,?,?)");
    $sql->execute([$id_transaccion, $päivämäärä_uusi, $status, $email, $id_asiakas, $yhteensä]);
    $id = $con->lastInsertId();

    if($id > 0) {

        $productos = isset($_SESSION['ostoskori']['productos']) ? $_SESSION['ostoskori']['productos'] : null;

        if($productos != null) {
            foreach($productos as $clave => $määrä) {
                
                $sql = $con->prepare("SELECT id, nimi, hinta, alennus, $määrä AS määrä FROM productos WHERE id=? AND omaisuus=1");
                $sql->execute([$clave]);
                $row_prod = $sql->fetch(PDO::FETCH_ASSOC);
                
                $hinta = $row_prod['hinta'];
                $alennus = $row_prod['alennus'];
                $hinta_ale = $hinta - (($hinta * $alennus) / 100);

                $sql_insert = $con->prepare("INSERT INTO osto_tiedot (id_compra, id_producto, nimi, hinta, määrä) VALUES (?,?,?,?,?)");
                $sql_insert->execute([$id, $clave, $row_prod['nimi'], $hinta_ale, $määrä]);

            }
            include 'lähettää_email.php';
        }
        unset($_SESSION['ostoskori']);
    }


}



?>