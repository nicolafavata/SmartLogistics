<?php
   $accessData=parse_ini_file('../config.ini');
   $conn = @mysqli_connect ($accessData['host'],$accessData['user'],$accessData['pass']);
   mysqli_set_charset($conn,"utf8");
   if ($conn) {
       $provincia=$_GET['provincia'];
       $provincia=mysqli_real_escape_string($conn,$provincia);
       $comandoSQL = "SELECT comune from comuni where provincia='$provincia'";
       @mysqli_select_db($conn, $accessData['name']);
       $ricerca = mysqli_query($conn, $comandoSQL);
       if ($ricerca) {
           echo '<div class="input-group mb-3">';
          echo '<div class="input-group-prepend">';
          echo '<label class="h6 fucsia input-group-text" for="inputGroupSelect01">Comune(*)&nbsp</label>';
          echo '</div>';
           echo '<select class="custom-select" name="comune">';
                                        while ($tuple = mysqli_fetch_assoc($ricerca))
                                        {
                                            $comune=$tuple['comune'];
                                            echo "<option value='$comune'>$comune</option>";
                                        }
                                    ?>
           </select></div>
                <div class="table-responsive-md">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <label class="h6 fucsia shadow">Indirizzo(*)</label><br />
                                </td>
                                <td>
                                    <label class="h6 fucsia shadow">Numero civico(*)</label><br />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input required type="text" name="indirizzo" id="indirizzo" maxlength="50" placeholder="Via/Piazza">
                                </td>
                                <td>
                                    <input required type="text" name="civico" id="civico" maxlength="6" placeholder="Numero civico">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<?php
                }
                mysqli_close($conn);
            }
?>
          