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
                                <label class="h6 fucsia input-group-text" for="inputGroupSelect01">Provincia(*)</label>
                            </div>
                            <select class="custom-select" name="provincia" id="provincia"
                                    onChange="ChangeById('provincia','comuni','ajax/ajax-comuni-business.php?provincia=')">
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
                                <label class="h6 fucsia input-group-text" for="inputGroupSelect01">Comune(*)&nbsp</label>
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
                <div class="table-responsive-md">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>
                                <label class="h6 fucsia shadow">CAP(*)</label><br />
                            </td>
                            <td>
                                <label class="h6 fucsia shadow">City(*)</label><br />
                            </td>
                            <td>
                                <label class="h6 fucsia shadow">State(*)</label><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input required type="text" name="cap_extra" id="cap_extra" maxlength="30" placeholder="CAP">
                            </td>
                            <td>
                                <input required type="text" name="city" id="city" maxlength="30" placeholder="City">
                            </td>
                            <td>
                                <input required type="text" name="state" id="state" maxlength="6" placeholder="State">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="h6 fucsia shadow">Adress(*)</label><br />
                            </td>
                            <td colspan="2">
                                <label class="h6 fucsia shadow">Number(*)</label><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input required type="text" name="indirizzo" id="indirizzo" maxlength="50" placeholder="Your adress">
                            </td>
                            <td colspan="2">
                                <input required type="text" name="civico" id="civico" maxlength="6" placeholder="Street number">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
<?php }?>

          