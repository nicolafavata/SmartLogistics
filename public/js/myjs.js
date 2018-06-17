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

function showloader() {
    document.getElementById('loading').style.display = 'inline-block';

}

function lock(el1, el2) {
    document.getElementById(el2).value = '12345678912';
    if(el1.checked) {
        document.getElementById(el2).hidden = true;
        document.getElementById('alert').hidden = true;
    } else {
        document.getElementById(el2).hidden = 'false';
    }
}

//funzione spinner


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

function valideprofile() {
    var nome = document.getElementById("nome");
    var cognome = document.getElementById("cognome");
    var nascita = document.getElementById("nascita");
    var sesso = document.getElementById("sesso");
    var errorBox = document.getElementById("errorMessage");
    var alertDiv = "<div class='alert alert-danger alert-dismissible' roles='aler'>";
    var alertBtn = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";

    nome.style.border = "1px solid #ccc";
    cognome.style.border = "1px solid #ccc";
    nascita.style.border = "1px solid #ccc";

    //check nome
    if ((nome.value == "") || (nome.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi inserire il tuo nome.' + "</div";
        nome.focus();
        nome.style.border = "3px solid #990033";
        return false;
    }

    //check cognome
    if ((cognome.value == "") || (cognome.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi inserire il tuo cognome.' + "</div";
        cognome.focus();
        cognome.style.border = "3px solid #990033";
        return false;
    }

    //check nascita
    if ((nascita.value == "") || (nascita.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi inserire la tua data di nascita.' + "</div";
        nascita.focus();
        nascita.style.border = "3px solid #990033";
        return false;
    }
     
    return true;

}

function valideoffer() {
    var rb_scelto = false;
    var errorBox = document.getElementById("errorMessage");
    var alertDiv = "<div class='alert alert-danger alert-dismissible' roles='aler'>";
    var alertBtn = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
                
    for (counter = 0; counter < document.newtravel.idmezzo.length; ++counter) {
        if (document.newtravel.idmezzo[counter].checked) 
                        rb_scelto = true;
                }
                
    if (rb_scelto == false) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Per prima cosa devi selezionare il mezzo con il quale farai il viaggio.' + "</div";
        return (false);
    }

    return (true);

}

function validemezzo() {
    var marca = document.getElementById("marca");
    var disponibili = document.getElementById("disponibili");
    var modello = document.getElementById("modelli");
    var numbagagli = document.getElementById("numbagagli");
    var errorBox = document.getElementById("errorMessage");
    var alertDiv = "<div class='alert alert-danger alert-dismissible' roles='aler'>";
    var alertBtn = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
    

    marca.style.border = "1px solid #ccc";
    disponibili.style.border = "1px solid #ccc";
    modelli.style.border = "1px solid #ccc";
    numbagagli.style.border = "1px solid #ccc";

    //check marca
    if ((marca.value == "") || (marca.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi selezionare la marca del tuo mezzo.' + "</div";
        marca.focus();
        marca.style.border = "3px solid #990033";
        return false;
    }

    //check disponibili
    if ((disponibili.value == "") || (disponibili.value == "undefined") || (disponibili.value == 0)) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi digitare il numero di posti disponibili se vuoi utilizzare il mezzo per offrire un passaggio.' + "</div";
        disponibili.focus();
        disponibili.style.border = "3px solid #990033";
        return false;
    }

    //check modello
    if ((modelli.value == "") || (modelli.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi selezionare il modello del tuo mezzo.' + "</div";
        modelli.focus();
        modelli.style.border = "3px solid #990033";
        return false;
    }

    //check disponibili
    if ((numbagagli.value == "") || (numbagagli.value == "undefined")) {
        errorBox.innerHTML = alertDiv + alertBtn + '<strong>Attento!</strong> Devi digitare il numero di bagagli che può contenere il tuo mezzo.' + "</div";
        numbagagli.focus();
        numbagagli.style.border = "3px solid #990033";
        return false;
    }
     
    return true;

}

//Funzione per il calcolo degli anni dell'utente in base alla data di nascita
function calcolo_anni(oggi, strData){
    aData = strData.split('/');
    aData[0] = parseInt(aData[0],10);
    aData[1] = parseInt(aData[1],10)-1;
    aData[2] = parseInt(aData[2],10);
    data = new Date(aData[2],aData[1],aData[0]);
    if(data.getDate()==aData[0] && data.getMonth()==aData[1] && data.getFullYear()==aData[2]){
        tmpData = new Date(oggi.getFullYear(),aData[1],aData[0]);
        dif = oggi.getTime()-tmpData.getTime();
        divisore = (1000*60*60*24);
        giorni = parseInt(dif/divisore);
        anni = oggi.getFullYear()-data.getFullYear();
        if (giorni < 0) {
            anni--;
        }
        return(anni);
    } else{
        return('Data non valida');
    }
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

//Geolocation
function showPosition(){
    showloader();
    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(function(position){
            var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+position.coords.latitude+','+position.coords.longitude;
            axios.get(url,{
                param:{
                    key:'AIzaSyDhLYVDNomqt4XPBd7Mb3YrjrXEqmK79pQ'
                }
            }).then(function (response) {
                console.log(response);
                console.log(response.data.results[0].address_components[2].long_name);
                var comune = response.data.results[0].address_components[2].long_name;
                var cap = response.data.results[0].address_components[7].long_name;
                window.location="user/"+cap+"/"+comune;
            });

        });

    } else{

        alert("Sorry, your browser does not support HTML5 geolocation.");

    }
}

