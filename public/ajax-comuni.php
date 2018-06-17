<?php
   $provincia=$_GET['provincia'];
   $accessData=parse_ini_file('../config.ini');
   $conn = @mysqli_connect ('127.0.0.1','root','v2t3z3v3');
   mysqli_set_charset($conn,"utf8");
   if ($conn) {
      $comandoSQL = "SELECT comune from comuni where provincia='".$provincia."'";
      @mysqli_select_db($conn,'smartlogis');
      $ricerca = mysqli_query($conn, $comandoSQL);
        if ($ricerca) {
          echo '<div class="input-group-prepend">';
          echo '<label class="input-group-text" for="inputGroupSelect01">Comune</label>';
          echo '</div>';
          echo '<select class="custom-select" name="comune">';
                    while ($tuple = mysqli_fetch_assoc($ricerca))
                    {
                      $comune=$tuple['comune'];
                      echo "<option value='$comune'>$comune</option>";
                    }
                    echo '</select>';
                }
            }
?>
          