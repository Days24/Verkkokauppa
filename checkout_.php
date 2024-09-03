<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <script
      src= "https://www.paypal.com/sdk/js?client-id=AfTLgtQCux3m_LAnFvvlYy3t6LgFUU7cZ4r7px07yoD866ifQI18gFrUUaeCEccWv1v08-U6OWUOnzzk&currency=EUR">
    </script>
</head>
<body>

<div id="paypal-button-container"></div>

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
            value: 100
          }
        }]
      });
    },

    onApprove: function(data, actions){
      actions.order.capture().then(function(detalles){
        window.location.href="suoritettu.html"
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