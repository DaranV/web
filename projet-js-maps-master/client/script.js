
//On configure la sidebar
$(document).ready(function () {
    $('.drawer').drawer({
        showOverlay: false
    });
});



var map = L.map('map',
    {
        center: [48.8422, 2.6785],
        zoom: 18
    });
var base = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);

function clearMap(m) {
    for(i in m._layers) {
        if(m._layers[i]._path != undefined) {
            try {
                m.removeLayer(m._layers[i]);
            }
            catch(e) {
                console.log("problem with " + e + m._layers[i]);
            }
        }
    }
}



//On crée l'évènement pour notre formulaire on va traiter la requête nous même

$("#searchForm").submit(function (e) {
    e.preventDefault();

    console.log("Evénement intercepté");

    //Ici on continue on récupère les champs que le gars à saisie et on fait une requête à notre api 
    //recupere l'adresse de dep et d'arrivée 

    var depart = $("#debut").val();
    var arrivee = $("#fin").val();
    console.log(depart);
    var latlngs = [];
    $.ajax(encodeURI('http://localhost:3000/itineraire?departure=' + depart + '&arrival=' + arrivee))
        .done(function (data) {
            //Done est appelé qu'on on recoit les données de la requête et que tout s'est bien passé
            //Dessin de la route , douoble boucle sur le tableau de coordonnées
            console.log(data);


            for (var i = 0; i < data[0].length; i++) {
                for (var j = 0; j < data[0][i].length; j++) {
                    lat1 = data[0][i][j][0];
                    long1 = data[0][i][j][1];

                    latlngs.push(new L.LatLng(lat1, long1));
                }
            }
            
            $("#instructions").html("");
			data[1].forEach((instruction)=>{
				$("#instructions").append('<li><div class="drawer-menu-item">'+instruction+'</div></li>');
			})
			
            //On nettoie tout 
          
            clearMap(map);
            L.polyline(latlngs, { color: 'red' }).addTo(map);
            map.setView(latlngs[0],11);
        })
        .fail(function (error) {
            //Fail est appelé si une erreur survient
            console.log(error);
            alert("error");
        })
})


//Dessin de la map



