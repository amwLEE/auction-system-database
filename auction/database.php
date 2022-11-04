<?php
  $connection = mysqli_connect('localhost', 'user1', 'uDhx3kBgKLLO*Bbo', 'Auction')
  or die('Error connectingto MySQL server.' . mysqli_connect_error());

  $query = "INSERT INTO Users (firstName, lastName, email, username, password)".
  "VALUES ('Dylan', 'Conceicao', 'dylan.conceicao.22@ucl.ac.uk', 'RubiksCubey', '999')";

  $result = mysqli_query($connection, $query)
  or die('Error making saveToDatabase query.' . mysqli_error($connection));

  mysqli_close($connection)
?>