/**
 * Full AJAX request with specified caught xhttp changes
 * NEW: Fuctions are all embedded when sent
 */
function ajax(URL, method, dataToSend, objectToChange, onSuccess=null, onError=null){
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){ 
        if (this.readyState == 4 && this.status == 200) {
            if(onSuccess !== null) {
                onSuccess(objectToChange, xhttp.responseText);
            } else {
                objectToChange.innerHTML = xhttp.responseText;
            }
        } else {
            if(this.readyState == 4 && this.status != 200){
                console.error("Error while executing ajax command [Code: "+this.status+"]");
                if(onError !== null)
                    onError(objectToChange, this.status);
            }
        }
    }

    // Setting fuctions for each case (optional)
    if(onError !== null)
        xhttp.addEventListener("error", onError);
        xhttp.addEventListener("timeout", onError);
    
    switch(method){
        case "POST":
            xhttp.open("POST", URL);
            xhttp.send(dataToSend);
            break;
        
        case "GET":
            xhttp.open("POST", URL+dataToSend);
            xhttp.send();
            break;
    }
}