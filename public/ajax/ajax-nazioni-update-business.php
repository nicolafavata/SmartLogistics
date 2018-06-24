<?php
       $nazione=$_GET['nazione'];
       if ($nazione=='Italia'){
           $comandoSQL = "SELECT distinct (provincia) from comuni order by provincia";
           $accessData=parse_ini_file('../config.ini');
           $conn = @mysqli_connect ($accessData['host'],$accessData['user'],$accessData['pass']);
           mysqli_set_charset($conn,"utf8");
           if ($conn) {
               @mysqli_select_db($conn, $accessData['name']);
                $ricerca = mysqli_query($conn, $comandoSQL);
                if ($ricerca) {
                    if ($tuple = mysqli_fetch_assoc($ricerca)) {
                    ?>
                    <div id="ajax-comune">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="h6 input-group-text" for="inputGroupSelect01">Provincia</label>
                            </div>
                            <select class="custom-select" name="provincia" id="provincia"
                                    onChange="ChangeById('provincia','comuni','../ajax/ajax-comuni-update-business.php?provincia=')">
                                <option selected>Seleziona la tua provincia</option>
                                <?php
                                while ($tuple = mysqli_fetch_assoc($ricerca)) {
                                    $provincia = $tuple['provincia'];
                                    echo "<option value='$provincia'>$provincia</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div id="comuni" class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="h6 input-group-text" for="inputGroupSelect01">Comune&nbsp</label>
                            </div>
                            <select class="custom-select" name="comune">
                                <option>Seleziona prima la provincia</option>
                            </select>
                        </div>
                    </div>
                    <?php
                }}
           mysqli_close($conn);
           } }
           else  {?>
            <div id="ajax-comune">
                <label for="">Cap extra</label>
                <input required type="text" name="cap_extra" id="cap_extra" class="font-weight-bold text-uppercase form-group" value="" >
                <label for="">City</label>
                <input required type="text" name="city" id="city" class="font-weight-bold text-uppercase form-group" value="" >
                <label for="">State</label>
                <input required type="text" name="state" id="state" class="font-weight-bold text-uppercase form-group" value="" >
            </div>
<?php }?>

          