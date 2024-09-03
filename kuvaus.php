<?php 
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == '') {
    echo 'Error processing request';
    exit;
}else {
    
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if ($token == $token_tmp) {

                $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND omaisuus=1 LIMIT 1");
                $sql->execute([$id]);
                if ($sql->fetchColumn() > 0) {

                    $sql = $con->prepare("SELECT nimi, kuvaus, hinta, alennus FROM productos WHERE id=? AND omaisuus=1");
                    $sql->execute([$id]);
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    $nimi = $row['nimi'];
                    $kuvaus = $row['kuvaus'];
                    $hinta = $row['hinta'];
                    $alennus = $row['alennus'];
                    $hinta_ale = $hinta - (($hinta * $alennus) / 100);
                    $dir_images = 'images/productos/' . $id . '/';

                    $rutaImg = $dir_images . 'principal.jpg';

                    if (!file_exists($rutaImg)) {
                        $rutaImg = 'images/no-photo.jpg';
                    }
                    $imagenes = array();
                    if(file_exists($dir_images)) {  
                    $dir = dir($dir_images);

                    while (($archivo = $dir->read()) != false) {
                        if($archivo != 'principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))){
                            $imagenes[] = $dir_images . $archivo;   
                        }
                       }
                       $dir->close();
                    }                
    }else {
         echo  'Error processing request';
         exit;
    }
}else{
    echo  'Error processing request';
    exit;
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
        //conteo de productos en el carrito, para que aparezca en la pagina principal, href=checkout.php esta llamando a la url para que se conecte la pagina, copia en el index//
        <a href="checkout.php" class="btn btn-primary"> Ostoskori <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!--Contenido-->
<main>
    <div class="container">
        <div class="row">
            <div class="col-md-6 order-md-1">
                <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img src="<?php echo $rutaImg; ?>" class="d-block w-100">
                        </div>

                        <?php foreach($imagenes as $img){ ?>
                        <div class="carousel-item">
                        <img src="<?php echo $img; ?>" class="d-block w-100">
                        </div>
                        <?php } ?>

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>
            <div class="col-md-6 order-md-2">
            <h2><?php echo $nimi; ?></h2>

                            <?php if($alennus > 0) {?>
                            <p><del><?php echo VALUUTTA . number_format($hinta, 2, ','); ?></del></p>
                            <h2>
                                <?php echo VALUUTTA . number_format($hinta_ale, 2, ','); ?>
                                <small class= "text-success"> <?php echo $alennus; ?>% alennus</small>
                            </h2>
                            <?php } else { ?>

                <h2><?php echo VALUUTTA . number_format($hinta, 2, ','); ?></h2>
                <?php } ?>
                <p class="lead">
                <?php echo $kuvaus; ?>
                </p>
                <div class="d-grid gap-3 col-10 mx-auto">
                    <button class= "btn btn-primary" type="button">Osta nyt</button>
                    <button class= "btn btn-outline-primary" type="button" onclick="addProducto(<?php echo $id; ?>, '<?php echo $token_tmp; ?>')">Lisää ostoskoriin</button>

                </div>
            </div>
        </div>
    
    </div>
</main>

<!-- Option 1: Bootstrap Bundle with Popper/ link de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<script>
    function addProducto(id, token){
        let url = 'clases/Ostoskori.php'
        let formData = new FormData()
        formData.append('id', id)
        formData.append('token', token)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
         .then(data => {
            if(data.ok){
                let elemento = document.getElementById("num_cart")
                elemento.innerHTML = data.numero
            }
        })
    }

</script>



<!--
    Betty AT
    2024-->

</body>
</html>
