<?php 
require '../config/config.php';
require '../config/database.php';

if(isset($_POST['action'])) {
   
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    if($action == 'lisätä') { 
        $määrä = isset($_POST['määrä']) ? $_POST['määrä'] : 0;
        $vastaus = lisätä($id, $määrä);
        if ($vastaus > 0) {
            $datos['ok'] = true;
            } else {
                $datos['ok'] = false;
        }
        $datos['väli'] = VALUUTTA . number_format($vastaus, 2, ','); 
    } else if ($action == 'poistaa') {
                $datos['ok'] = poistaa($id);
    } else { 
        $datos['ok'] = false;
    }
 } else { 
    $datos['ok'] = false;
}

echo json_encode($datos);

function lisätä($id, $määrä) {

    $vast = 0;
    if($id > 0 && $määrä > 0 && is_numeric(($määrä))) {
        if(isset($_SESSION['ostoskori']['productos'][$id])) {
            $_SESSION['ostoskori']['productos'][$id] = $määrä;
 
                $db = new Database();
                $con = $db->conectar();
                $sql = $con->prepare("SELECT hinta, alennus FROM productos WHERE id=? AND omaisuus=1 LIMIT 1");
                $sql->execute([$id]);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $hinta = $row['hinta'];
                $alennus = $row['alennus'];
                $hinta_ale = $hinta - (($hinta * $alennus) / 100);
                $vast = $määrä * $hinta_ale;

                return $vast;
        }
        } else {
            return $vast;
    }
}

function poistaa($id) {
    if($id > 0) {
        if(isset($_SESSION['ostoskori']['productos'][$id])) {
            unset($_SESSION['ostoskori']['productos'][$id]);
            return true;
        }
    } else {
        return false;
    }
}

?>