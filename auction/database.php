<?php
  $connection = mysqli_connect('localhost', 'root', 'root', 'Auction')
  or die('Error connectingto MySQL server.' . mysqli_connect_error());

  echo "Connected successfully...<br><br>";

  $query = "INSERT INTO Users (firstName, lastName, email, username, password)".
  "VALUES ('Amanda', 'Lee', 'amanda.lee.22@ucl.ac.uk', 'amwLEE', '123'),
  ('Dylan', 'Conceicao', 'dylan.conceicao.22@ucl.ac.uk', 'RubiksCubey', '456'),
  ('Melody', 'Leom', 'melody.leom.22@ucl.ac.uk', 'ml3319', '789'),
  ('Valerie', 'Song', 'valerie.song.22@ucl.ac.uk', 'valeriewqsong', '000')";

  $result = mysqli_query($connection, $query)
  or die('Error making saveToDatabase query.' . mysqli_error($connection));


?>