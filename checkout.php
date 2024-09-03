<?php 
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

//validar si existe la variable SESSION, si existe la recibe '?' y si no existe 'null'// 
$productos = isset($_SESSION['ostoskori']['productos']) ? $_SESSION['ostoskori']['productos'] : null; 

//print_r($_SESSION);

$lista_ostoskori = array();
//cuando productos es diferente a null, es decir tiene informacion entonces pasa lo siguiente://
if($productos != null) {
    //la clave sera el id del producto y cantidad sera la cantidad que vamos a tener//
    foreach($productos as $clave => $määrä) {
        
        $sql = $con->prepare("SELECT id, nimi, hinta, alennus, $määrä AS määrä FROM productos WHERE id=? AND omaisuus=1");
        $sql->execute([$clave]);
        $lista_ostoskori[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
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
        //conteo de productos en el carrito, para que aparezca en la pagina principal, copia en kuvaus//
          <a href="ostoskori.php" class="btn btn-primary"> Ostoskori <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
          </a>
      </div>
    </div>
  </div>
</header>

<!--Contenido-->
<main>
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tuote</th>
                        <th>Hinta</th>
                        <th>Määrä</th>
                        <th>Välisumma</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($lista_ostoskori == null) { 
                        echo '<tr><td colspan="5" class="text-center"><b>Tyhjä lista</b></td></tr>'; 
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
                        <td><?php echo VALUUTTA . number_format($hinta_ale, 2, ','); ?></td>

                        <td>
                            <input type="number" min="1" max="10" step="1" value="<?php echo $määrä ?>"
                            size="5" id="määrä_<?php echo $_id; ?>" onchange="päivitysMäärä(this.value, <?php echo $_id; ?>)">
                        </td>
                        
                        <td>
                            <div id="välisumma_<?php echo $_id; ?>" name="välisumma[]"><?php echo VALUUTTA . number_format($välisumma, 2, ','); ?></div>
                        </td>

                        <td>
                        <a href="#" id="poistaa" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toggle="modal" data-bs-target="#poistaModal">Poistaa</a></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2">
                        <p class="h3" id="yhteensä"><?php echo VALUUTTA . number_format($yhteensä, 2, ','); ?></p>
                        </td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>

        <?php if($lista_ostoskori != null) { ?>

        <div class="row">
            <div class="col-md-5 offset-md-7 d-grid gap-2">
                <a href="maksu.php" class="btn btn-primary btn-lg">Maksaa</a>
            </div>
        </div>
        <?php } ?>
    </div>
</main>


<!-- Modal -->
<div class="modal fade" id="poistaModal" tabindex="-1" aria-labelledby="poistaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="poistaModalLabel">Huomio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Haluatko poistaa tuotteen?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulje</button>
        <button id="btn-poista" type="button" class="btn btn-danger" onclick="poistaa()">Poistaa</button>
      </div>
    </div>
  </div>
</div>


 <!-- Option 1: Bootstrap Bundle with Popper/ link de Bootstrap -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<script>

    let poistaModal = document.getElementById('poistaModal')
    poistaModal.addEventListener('show.bs.modal', function(event){
        let button = event.relatedTarget 
        let id = button.getAttribute('data-bs-id')
        let buttonPoista = poistaModal.querySelector('.modal-footer #btn-poista')
        buttonPoista.value = id
    })


    function päivitysMäärä(määrä, id) {
        let url = 'clases/päivitys_ostoskori.php'
        let formData = new FormData()
        formData.append('action', 'lisätä')
        formData.append('id', id)
        formData.append('määrä', määrä)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
         .then(data => {
            if(data.ok){
                
                let divvälisumma = document.getElementById('välisumma_' + id)
                divvälisumma.innerHTML = data.väli

                let yhteensä = 0.00
                let list = document.getElementsByName('välisumma[]')

                for(let i = 0; i < list.length; i++) {
                    yhteensä += parseFloat(list[i].innerHTML.replace(/[€.]/g, ''))
                }

                yhteensä = new Intl.NumberFormat('fi-FI', {
                    minimumFractionDigits: 2
                }).format(yhteensä)
                document.getElementById('yhteensä').innerHTML = '<?php echo VALUUTTA; ?>' + yhteensä
            }
        })
    }




    function poistaa() {

        let botonPoista = document.getElementById('btn-poista')
        let id = botonPoista.value


        let url = 'clases/päivitys_ostoskori.php'
        let formData = new FormData()
        formData.append('action', 'poistaa')
        formData.append('id', id)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
         .then(data => {
            if(data.ok){
                location.reload()
             
            }
        })
    }

</script>

</body>
</html>