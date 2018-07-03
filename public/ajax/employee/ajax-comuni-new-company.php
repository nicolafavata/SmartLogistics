<?php
   $accessData=parse_ini_file('../../config.ini');
   $conn = @mysqli_connect ($accessData['host'],$accessData['user'],$accessData['pass']);
   mysqli_set_charset($conn,"utf8");
   if ($conn) {
       $provincia=$_GET['provincia'];
       $provincia=mysqli_real_escape_string($conn,$provincia);
       $comandoSQL = "SELECT comune,id_comune from comuni where provincia='$provincia'";
       @mysqli_select_db($conn, $accessData['name']);
       $ricerca = mysqli_query($conn, $comandoSQL);
       if ($ricerca) {
           echo '<div class="input-group mb-3">';
          echo '<label class="grigio" for="inputGroupSelect01">Comune&nbsp&nbsp</label>';
           echo '<select class="custom-select" name="cap_company">';
                                        while ($tuple = mysqli_fetch_assoc($ricerca))
                                        {
                                            $comune=$tuple['comune'];
                                            $id=$tuple['id_comune'];
                                            echo "<option value='$id'>$comune</option>";
                                        }
                                    ?>
           </select></div>

<?php
                }
                mysqli_close($conn);
            }
?>
          