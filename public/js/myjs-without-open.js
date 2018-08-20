//Geolocation
function getPosition(){
    showloader();
    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(function(position){
            var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+position.coords.latitude+','+position.coords.longitude;
            axios.get(url,{
                param:{
                    key:'AIzaSyBf81k4IAFil6omJv2VFTcvTssfdeiwcQQ'
                }
            }).then(function (response) {
                console.log(response);
                if (response.data.results[0]){
                    var comune = response.data.results[0].address_components[2].long_name;
                    var cap = response.data.results[0].address_components[7].long_name;
                    window.location="user/"+cap+"/"+comune;
                } else window.location="/user/91100/Residenza";


            });

        },showError);

    } else{

        alert("Sorry, your browser does not support HTML5 geolocation.");

    }
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            window.location="/user/91100/Residenza";
            break;
        case error.POSITION_UNAVAILABLE:
            window.location="/user/91100/Residenza";
            break;
        case error.TIMEOUT:
            window.location="/user/91100/Residenza";
            break;
        case error.UNKNOWN_ERROR:
            window.location="/user/91100/Residenza";
            break;
    }
}

// funzione che utilizziamo per bloccare pulsanti
function unlock(el1, el2) {
            document.getElementById(el2).value = '';
            if(el1.checked) {
                document.getElementById(el2).hidden = false;
                document.getElementById('alert').hidden = false;
            } else {
                document.getElementById(el2).hidden = 'true';
            }
        }

function showfile(el1,el2) {
         document.getElementById(el1).hidden = false;
         document.getElementById(el2).hidden = true;
}

function hiddenfile(el1,el2) {
    document.getElementById(el1).hidden = true;
    document.getElementById(el2).hidden = true;
}

function showId(el) {
    document.getElementById(el).hidden = false;
}

function NoShowId(el) {
    document.getElementById(el).hidden = true;
}

function NoHtml() {
    console.log('alert-ajx');
    document.getElementById('alert-ajax').innerHTML = " ";
}

function undisabled(el1, el2) {
    if(el1.checked) {
        document.getElementById(el2).disabled = false;
    } else {
        document.getElementById(el2).disabled = 'disabled';
    }
}

function hideloader() {
    document.getElementById('loading').style.display = 'none';
    
}

function deleterowTableContent(row) {
    document.getElementById('content').deleteRow(row);
    document.getElementById('ean').disabled = false;
    document.getElementById('add-item').disabled = false;
    var rows = document.getElementById('content').getElementsByTagName('tr').length;
    if (rows==1) addrow('content');
}

function addrow(content) {
    var table = document.getElementById(content);
    var rows = document.getElementById(content).getElementsByTagName('tr').length;
    var row = table.insertRow(rows);
    var cell1 = row.insertCell(0);
    cell1.innerHTML = '<th scope="row" class="text-center">' + (rows) + '</th>';
    var cell2 = row.insertCell(1);
    cell2.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><a  onclick="deleterowTableContent('+ (rows) +')" title="Cancella la riga"><i class="text-danger fa fa-minus-circle"></i></a></td>';
    var cell3 = row.insertCell(2);
    cell3.innerHTML = ' ';
    var cell4 = row.insertCell(3);
    cell4.innerHTML = '<td><input class="form-control alert-success" maxlenght="50" type="text" id="code" name="product_salesDeskCon" value=""></td>'
    var cell5 = row.insertCell(4);
    cell5.innerHTML = '<td><input disabled class="form-control" maxlenght="80" type="text" name="title_product" value=""></td>';
    var cell6 = row.insertCell(5);
    cell6.innerHTML = '<td><input disabled class="form-check-label" min="1" type="number" step="1.00" name="quantity_salesDeskCon" value=""></td>';
    var cell7 = row.insertCell(6);
    cell7.innerHTML =  '<td ><input disabled class="form-control" maxlenght="2" type="text" name="unit" value=""></td>';
    var cell8 = row.insertCell(7);
    cell8.innerHTML = '<td><input disabled class="form-control" type="text" name="price_product" ></td>';
    var cell9 = row.insertCell(8);
    cell9.innerHTML = '<td><input disabled class="form-check" min="0" type="number" step="0.01" name="discount_salesDeskCon"></td>';
    var cell10 = row.insertCell(9);
    cell10.innerHTML = '<td><input disabled class="form-control" type="text" name="price_product" ></td>';
    var cell11 = row.insertCell(10);
    cell11.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"></td>';
    document.getElementById('ean').value = "";
    document.getElementById('add-item').disabled = true;
}

function showNumberDate() {
    var tok = document.getElementById('tok').value;
    document.getElementById('numdat').hidden = false;
    document.getElementById('number').value = null;
    document.getElementById('date').value = null;
    var type = document.getElementById('type-document').value;
    if (type == 'invoice' || type == 'desk'){
        var url = 'https://www.nicolafavata.com/smartlogis/take-last-number/' + type;
        $.ajax({
           url: url,
           type: 'post',
            data: '_token='+tok,
            dataType: "html",
            success: function (resp) {
               if (resp.length>0){
                   document.getElementById('number').value = resp;
                   document.getElementById('date').value = document.getElementById('now').value;
               }
            }
        });
    }
    return;
}

function pushInformation(reference,comment,tot,iva) {
    document.getElementById(reference).value = document.getElementById('reference').value;
    document.getElementById(comment).value = document.getElementById('comment').value;
    document.getElementById(tot).value = document.getElementById('tot').value;
    document.getElementById(iva).value = document.getElementById('iva_tot').value;

}

function showloader() {
    document.getElementById('loading').style.display = 'inline-block';

}

function updateText(id,val) {
    document.getElementById(id).value=val;
    if (id=='lead_time_config') {
        if (document.getElementById('text-lead-time').value>365)  document.getElementById('text-lead-time').value=365;
    }
    if (id=='days_number_config') {
        if (document.getElementById('days_number_config').value>365)  document.getElementById('days_number_config').value=365;
    }

    if (id=='window_last_config') {
        if (document.getElementById('text-window_last_config').value>31)  document.getElementById('text-window_last_config').value=31;
    }
    if (id=='window_first_config') {
        if (document.getElementById('text-window_first_config').value>31)  document.getElementById('text-window_first_config').value=31;
    }
    if ((id=='window_last_config') || (id=='text-window_last_config')) {
        if ((parseFloat(document.getElementById('text-window_first_config').value)>parseFloat(document.getElementById(id).value)) || parseFloat((document.getElementById('window_first_config').value>document.getElementById(id).value))){
            document.getElementById('text-window_first_config').value=document.getElementById(id).value;
            document.getElementById('window_first_config').value=document.getElementById(id).value;
        }
    }
    if ((id=='window_first_config') || (id=='text-window_first_config')) {
        if ((parseFloat(document.getElementById('text-window_last_config').value)<parseFloat(document.getElementById(id).value)) || parseFloat((document.getElementById('window_last_config').value)<parseFloat(document.getElementById(id).value))){
            document.getElementById('text-window_last_config').value=document.getElementById(id).value;
            document.getElementById('window_last_config').value=document.getElementById(id).value;
        }
    }
}

function checkImportMax(id,val) {
    var imp = document.getElementById(id).value;
    if (parseFloat(val)>parseFloat(imp))  document.getElementById(id).value=val;
 }

function checkImportMin(id,val) {
    var imp = document.getElementById(id).value;
    if (parseFloat(val)<parseFloat(imp))  document.getElementById(id).value=val;
}


function showid(master,show,litteral) {
    var display = document.getElementById(master).checked;
    if (display==false) {
        document.getElementById(litteral).innerHTML = 'Rendi visibile la tua azienda ai cittadini e seleziona i comuni di visibilità';
        document.getElementById(show).style.display = 'none';
    }
    else {
        document.getElementById(litteral).innerHTML = 'La tua azienda è visibile ai cittadini';
        document.getElementById(show).style.display = 'inline-block';
    }
}

function showb2b() {
    var display = document.getElementById('visibile_b2b').checked;
    if(display==false)
        document.getElementById('b2b').innerHTML = 'Rendi visibile la tua azienda alle altre aziende';
    else
        document.getElementById('b2b').innerHTML = 'La tua azienda è visibile alle altre aziende';
}

/* Classi javascript*/
/*!
 * classie - class helper functions
 * from bonzo https://github.com/ded/bonzo
 *
 * classie.has( elem, 'my-class' ) -> true/false
 * classie.add( elem, 'my-new-class' )
 * classie.remove( elem, 'my-unwanted-class' )
 * classie.toggle( elem, 'my-class' )
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false */

( function( window ) {

    'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

    function classReg( className ) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
    var hasClass, addClass, removeClass;

    if ( 'classList' in document.documentElement ) {
        hasClass = function( elem, c ) {
            return elem.classList.contains( c );
        };
        addClass = function( elem, c ) {
            elem.classList.add( c );
        };
        removeClass = function( elem, c ) {
            elem.classList.remove( c );
        };
    }
    else {
        hasClass = function( elem, c ) {
            return classReg( c ).test( elem.className );
        };
        addClass = function( elem, c ) {
            if ( !hasClass( elem, c ) ) {
                elem.className = elem.className + ' ' + c;
            }
        };
        removeClass = function( elem, c ) {
            elem.className = elem.className.replace( classReg( c ), ' ' );
        };
    }

    function toggleClass( elem, c ) {
        var fn = hasClass( elem, c ) ? removeClass : addClass;
        fn( elem, c );
    }

    var classie = {
        // full names
        hasClass: hasClass,
        addClass: addClass,
        removeClass: removeClass,
        toggleClass: toggleClass,
        // short names
        has: hasClass,
        add: addClass,
        remove: removeClass,
        toggle: toggleClass
    };

// transport
    if ( typeof define === 'function' && define.amd ) {
        // AMD
        define( classie );
    } else {
        // browser global
        window.classie = classie;
    }

})( window );




function lock(el1, el2) {
    document.getElementById(el2).value = '12345678912';
    if(el1.checked) {
        document.getElementById(el2).hidden = true;
        document.getElementById('alert').hidden = true;
    } else {
        document.getElementById(el2).hidden = 'false';
    }
}




// funzioni che utilizziamo per controlli nelle fasi di login o di registrazione.
function validate() {
    var gdpr = document.getElementById("gdpr");
    var email = document.getElementById("r_email_ut");
    var password = document.getElementById("r_pass");
    var errorBox = document.getElementById("errorMessage");
    var nome = document.getElementById("nome");
    var cognome = document.getElementById("cognome");
    var alertDiv = "<div class='alert alert-danger alert-dismissible' roles='alert'>";
    var alertBtn = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";

    nome.style.border = "1px solid #ccc";
    cognome.style.border = "1px solid #ccc";
    email.style.border = "1px solid #ccc";
    password.style.border = "1px solid #ccc";

    if(gdpr.value != "1"){
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi leggere e accettare la Ns. Privacy Policy.' + "</div";
    }


    //check nome
    if (nome.value == ""){
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Hai dimenticato a inserire il tuo nome.' + "</div";
        nome.focus();
        nome.style.border = "3px solid #990033";
        return false;
    }

    //check cognome
    if (cognome.value == ""){
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Hai dimenticato a inserire il tuo cognome.' + "</div";
        cognome.focus();
        cognome.style.border = "3px solid #990033";
        return false;
    }

    //check email
    if (email.value == ""){
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Hai dimenticato a inserire l\'email.' + "</div";
        email.focus();
        email.style.border = "3px solid #990033";
        return false;
    }

    //check password
    if (password.value == "") {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Hai dimenticato a inserire la password.' + "</div";
        password.focus();
        password.style.border = "3px solid #990033";
        return false;
    }

    if(password.value.length < 8) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> La password deve avere almeno 8 caratteri' + "</div";
        password.focus();
        password.style.border = "3px solid #990033";
        return false;
    }

    return true;

}


//Funzione che utilizziamo per creare un oggetto XMLHttpRequest in base alla versione del browser
function creaXMLHTTPRequest() {
    var xmlHttp;

    if (window.ActiveXObject) {
        // IE
        try {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            xmlHttp = false;
        }
    } else {
        // Firefox, Chrome, etc..
        try {
            xmlHttp = new XMLHttpRequest();
        } catch(e) {
            xmlHttp = false;
        }
    }

    if (!xmlHttp) {
        alert("Impossibile creare l'oggetto XMLHttpRequest");
    } else {
        return xmlHttp;
    }
}

//Questa funzione la utilizziamo per cambiare un elemento della pagina con la tecnica AJAX
function ChangeById(master,change,path) {
    var xmlhttp;
    xmlhttp = new creaXMLHTTPRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById(change).innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET",path+document.getElementById(master).value, true);
    xmlhttp.send();
}






