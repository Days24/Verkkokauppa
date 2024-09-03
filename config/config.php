<?php 

define("CLIENT_ID", "AfTLgtQCux3m_LAnFvvlYy3t6LgFUU7cZ4r7px07yoD866ifQI18gFrUUaeCEccWv1v08-U6OWUOnzzk");
define("CURRENCY", "EUR");
define("KEY_TOKEN", "BTY.qwe-878*");
define("VALUUTTA", "€");
//valuutta = moneda//

session_start();
$num_cart = 0;
if(isset($_SESSION['ostoskori']['productos'])){
    $num_cart = count($_SESSION['ostoskori']['productos']);

}

?>