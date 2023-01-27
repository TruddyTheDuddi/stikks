document.getElementById("submit").addEventListener("click", submit);
document.getElementById("url").addEventListener("focus", () => {
    // highlight the text in the input box
    document.getElementById("url").select();
});

function submit(){
    let url = document.getElementById("url").value.trim();
    let box = document.getElementById("formbox");
    let res = document.getElementById("respbox");
    let msgBox = document.getElementById("msg");
    let pfpRaw = document.getElementById("pfp").children[0];
    
    if(url == "")
        return;
    
    box.classList.add("disabled");
    msgBox.innerHTML = "<b>Loading /</b> Aggregating precious information...";
    msgBox.style.display = null;

    let data = {
        url: url
    };

    ajax("backend/fetch.php", "GET", encodeQueryData(data), null, (obj, resp) => {
        box.classList.remove("disabled");
        let json = JSON.parse(resp);
        if(!json.success){
            msgBox.innerHTML = json.msg;
        } else {
            msgBox.style.display = "none";
            res.style.display = null;
            document.getElementById("username").innerHTML = json.payload.username;
            document.getElementById("num").innerHTML = json.payload.num + json.payload.cnum;
            document.getElementById("cnum").innerHTML = json.payload.cnum;

            // Pfp stuff
            pfpRaw.style.display = "none";
            pfpRaw.src = json.payload.pfp;
            pfpRaw.onload = () => {
                pfpRaw.style.display = null;
            }

            // No stickers rip
            if(json.payload.num + json.payload.cnum == 0){
                document.getElementById("rip").style.display = null;
            } else {
                document.getElementById("rip").style.display = "none";
            }

            // Rich lad msg
            if(json.payload.cnum >= 12){
                document.getElementById("rich").style.display = null;
            } else {
                document.getElementById("rich").style.display = "none";
            }
        }
    });
}

/**
 * Object turned into a string for the GET URI 
 */
function encodeQueryData(data) {
    const ret = [];
    for (let d in data)
        ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
    return "?" + ret.join('&');
}