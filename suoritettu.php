<?php 


require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id_transaccion = isset($_GET['key']) ? $_GET['key'] : 0;

$error = '';
if ($id_transaccion == '0') {
     $error = 'Virheen käsittelypyynnön';
     } else {
        $sql = $con->prepare("SELECT count(id) FROM compra WHERE $id_transaccion=? AND status=?");
        $sql->execute([$id_transaccion, 'COMPLETED']);
        if ($sql->fetchColumn() > 0) {

            $sql = $con->prepare("SELECT id, päivämäärä, email, yhteensä  FROM compra WHERE $id_transaccion=? AND status=? LIMIT 1");
            $sql->execute([$id_transaccion, 'COMPLETED']);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $idCompra = $row['id'];
            $yhteensä = $row['yhteensä'];
            $päivämäärä = $row['päivämäärä'];

            $sqlDet = $con->prepare("SELECT nimi, hinta, määrä FROM osto_tiedot WHERE id_compra = ?");
            $sqlDet->execute([$idCompra]);
            } else {
                $error = 'Virhe ostoa tarkitettaessa';
            }


}


?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online - ALPACA</title>

    <!--Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>

    <!--Barra de navegacion-->
    <header>
  <div class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a href="#" class="navbar-brand">
        <strong>Alpaca Luettelo</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class= "collapse navbar-collapse" id= "navbarHeader"> 
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a href="#" class="nav-link active"></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Ota yhteyttä</a>
            </li>
        </ul>
        <a href="checkout.php" class="btn btn-primary"> Ostoskori <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!--Contenido-->
<main>
    <div class="container">
    <?php if(strlen($error) > 0){ ?>
        <div class="row">
            <div class="col">
                <h3><?php echo $error; ?></h3>
            </div>
        </div>
        <?php } else { ?>
            
            ?>
        <div class="row">
            <div class="col">
             <b>Ostaa folio: </b><?php echo $id_transaccion; ?><br>
             <b>Ostopäivä: </b><?php echo $päivämäärä; ?><br>
             <b>yhteensä: </b><?php echo VALUUTTA . number_format($yhteensä, 2, ','); ?><br>
            </div>
        </div>

        <div class="row">
            <div class="col">
              <table class="table">
                <thead>
                    <tr>
                        <th>Määrä</th>
                        <th>Tuote</th>
                        <th>Yhteensä</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) { 
                        $importe = $row_det['hinta'] * $row_det['määrä']; ?>
                        <tr>
                            <td><?php echo $row_det['määrä'] ?></td>
                            <td><?php echo $row_det['nimi'] ?></td>
                            <td><?php echo $importe ?></td>
                        </tr>
                        <?php } ?>
                </tbody>
              </table>
            </div>
        </div>
        <?php } ?>
        </div> 
 </main>

</body>
</html>