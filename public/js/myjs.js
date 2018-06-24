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






