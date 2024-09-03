<?php 
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['ostoskori']['productos']) ? $_SESSION['ostoskori']['productos'] : null; 

//print_r($_SESSION);

$lista_ostoskori = array();
if($productos != null) {
    foreach($productos as $clave => $määrä) {
        
        $sql = $con->prepare("SELECT id, nimi, hinta, alennus, $määrä AS määrä FROM productos WHERE id=? AND omaisuus=1");
        $sql->execute([$clave]);
        $lista_ostoskori[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
    } else {
        header("Location: index.php");
        exit;
}

//session_destroy();


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
          <a href="ostoskori.php" class="btn btn-primary"> Ostoskori <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
          </a>
      </div>
    </div>
  </div>
</header>

<!--Contenido-->
<main>
    <div class="container">

<div class="row" >
    <div class="col-6">
        <h4>Maksutiedot</h4>
        <div id="paypal-button-container"></div>

    </div>
    <div class="col-6">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tuote</th>
                        <th>Välisumma</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($lista_ostoskori == null) { echo '<tr><td colspan="5" class="text-center"><b>Tyhjä lista</b></td></tr>'; 
                    } else {
                        $yhteensä = 0;
                        foreach($lista_ostoskori as $producto) {
                            $_id = $producto['id'];
                            $nimi = $producto['nimi'];
                            $hinta = $producto['hinta'];
                            $määrä = $producto['määrä'];
                            $alennus = $producto['alennus'];
                            $hinta_ale = $hinta - (($hinta * $alennus) / 100);
                            $välisumma = $määrä * $hinta_ale;
                            $yhteensä += $välisumma;
                        ?>
                    <tr>
                        <td><?php echo $nimi; ?></td>
                        <td>
                            <div id="välisumma_<?php echo $_id; ?>" name="välisumma[]"><?php echo VALUUTTA . number_format($välisumma, 2, ','); ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2">
                        <p class="h3 text-end" id="yhteensä"><?php echo VALUUTTA . number_format($yhteensä, 2, ','); ?></p>
                        </td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>
    </div>
    </div>
    </div>

</main>



 <!-- Option 1: Bootstrap Bundle with Popper/ link de Bootstrap -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

 <script
      src= "https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>"></script>


<script>
  paypal.Buttons({
    style:{ 
      color: 'blue',
      shape: 'pill',
      label: 'pay'
    },
    createOrder: function(data, actions){
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: <?php echo $yhteensä; ?>
          }
        }]
      });
    },

    onApprove: function(data, actions){
        let URL = 'clases/kaapata.php'
      actions.order.capture().then(function(detalles){
        console.log(detalles)
        let url = 'clases/kaapata.php'

        return fetch(url, {
            method: 'post',
            headers: {
                'content-type': 'application/json'
            },
            body: JSON.stringify({
                detalles: detalles
            })
        }).then(function(response){
            window.location.href = "suoritettu.php?key=" + detalles['id'];
        })

      });
    },

    onCancel: function(data){
      alert("Maksu peruutettu");
      console.log(data);
    }
  }).render('#paypal-button-container');
</script>


</body>
</html>
