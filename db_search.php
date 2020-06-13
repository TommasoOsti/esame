<?php   
  $HOST= "localhost";
  $USER= "osti";  
  $PWD = "esame2020";
  $DBNAME = "esame2020";
  
  $conn= mysqli_connect($HOST,$USER,$PWD,$DBNAME) or die ("Database inesistente"); 
?>

<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="esame2020.css">

<?php
  $view_available = isset($_POST['filter_check']) ? 1 : 0;
  $price_from = $_POST['price_from'];    
  $price_to = $_POST['price_to'];    
  $localita = $_POST['localita'];

  $query = "
    SELECT *
    FROM proposta_viaggio
      INNER JOIN hotel ON proposta_viaggio.alloggio=hotel.id_hotel
      INNER JOIN viaggi ON proposta_viaggio.trasporto=viaggi.id_viaggio
      INNER JOIN mete ON hotel.posizione=mete.id_meta 
        WHERE ";
      if($price_to == 0)
        $query = $query . "proposta_viaggio.prezzo >=" . $price_from;
      else
        $query = $query . "proposta_viaggio.prezzo BETWEEN ". $price_from ." AND ". $price_to;
        
      if($localita != "none")
      {
        $query = $query . " AND viaggi.destinazione IN (
        SELECT id_meta FROM mete WHERE tipo_meta='";
        $query = $query . $localita . "')";
      }

  $result = mysqli_query ($conn, $query) or die ("Invalid query");
?>


<body>
  <nav class="e2020-bar e2020-black">
    <p><h1 align="center">Agenzia viaggi</h1></p>
  </nav>

  <div class="e2020-panel e2020-green">
    <p align="center">Offerte di viaggio</p>
  </div> 

  <div class="e2020-result-container">
    <h2>Risultati della ricerca</h2>

    <?php
      $nresults = 0;
      if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
          if($view_available == 0 && $row["posti_disponibili"] == 0)
            continue;

          $nresults++;
          echo "<div class='e2020-result-panel'>
            <p align=right>". $row["tipo_meta"] ."</p>
            <p>Località: " . $row["localita"] . ", " . $row["nazione"] . "</p>
            <p>Dal ". $row["data_partenza"] ." al ". $row["data_ritorno"] ."</p>
            <p>Trasporto: via ". $row["mezzo_trasporto"] ." con ". $row["compagnia"] ."</p>
            <p>Sistemazione presso hotel ". $row["nome_hotel"] ." ". $row["valutazione"] ." stelle, trattamento ". $row["trattamento"] ." e sistemazione in ". $row["sistemazione"] .".</p>
            <p align=right>Posti disponibili: ". $row["posti_disponibili"] ."</p>
            <p align=right style='color: crimson; font-size:xx-large;'>". $row["prezzo"] ."€</p>
          </div>";
        }
      } 
      if($nresults == 0) {
        echo "0 risultati";
      }

    ?>

  </div>

</body>
</html>