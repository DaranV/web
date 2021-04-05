/*------ INITIALISATION DES BAILS ----------*/
const express = require('express');
const cors = require('cors');
const fetch = require('node-fetch');
var polyline = require('google-polyline'); // ça va décoder les points -- polyline.encode() et polyline.decode()
const volleyball = require("volleyball");
const app = express(); // création de l'application
app.use(express.json());
app.use(cors());
app.use(volleyball);

const NodeCache = require("node-cache");
const myCache = new NodeCache();


const API_ADRESS_URL = "https://api-adresse.data.gouv.fr/search/";




function verif_adress(adress, callback) {



  const adresse_verif_url = API_ADRESS_URL + `?q=${adress}`;

  fetch(adresse_verif_url)
    .then((content) => content.json())
    .then(data => {

      // console.log(data.features);

      let address_data = {}
      //Si on a une confidence inférieur à 0.7 on considère l'adresse comme invalide
      if (data.features[0].properties.score < 0.7) {
        address_data = {
          status: 404
        }
      } else {
        address_data = {
          point: data.features[0].geometry.coordinate,
          label: data.features[0].properties.label,
          status: 200
        }
      }
      //Callback(data) fait l'effet d'un return ici
      callback(address_data);
    })







}




/*------ http://localhost:3000/itineraire?departure=247%20rue+de+crimee&arrival=127%20avenue%20de%20Versailles----------*/
app.get('/itineraire', (request, response) => {
  var departure = request.query.departure; // on reçoit l'adresse de départ
  var arrival = request.query.arrival; // on reçoit l'adresse d'arrivée

  console.log(departure);
  console.log(arrival);

  /* En vrai je crois que ça sert à rien Google doit sûrement le faire soi-même*/
  var depart = departure.replace(" ", "+");
  var arrive = arrival.replace(" ", "+");

  /* ----- Utilisation de l'API Google Direction : belek la clé d'API de Daran ne pas trop l'utiliser*/



  //On vérifie que les adresses existent

  verif_adress(departure, (result_departure) => {
    // console.log(result_departure);
    //Si l'addresse n'est pas valide on renvoie une erreur
    if (result_departure.status == 404) {

      response.status(422);
      response.json({
        error: "departure"
      });
      return;

    }

    verif_adress(arrival, (result_arrival) => {
      // console.log(result_arrival);

      if (result_arrival.status == 404) {

        response.status(422);
        response.json({
          error: "departure"
        });
        return;
      }


      depart = result_departure.label;
      arrive = result_arrival.label;
      var url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' + depart + '&destination=' + arrive + '&key=AIzaSyCI9-tYg6bp8pB7iQ_wIEB750lCQYONlZI&language=fr';

      url = encodeURI(url);


      //Si la valeur existe pas besoin de refaire la requête à google

      const value = myCache.get(depart.trim() + "---" + arrive.trim());
      if (value) {
        console.log("Result from cache");
        response.json(value);
        return;
      }

      console.log(url);
      /*récupérer les données qu'on a demandé*/
      fetch(url)
        .then((resp) => resp.text())
        .then((text) => JSON.parse(text))
        .then((data) => {
          var donnee = data;
          // console.log(donnee);

          var tabRoute = donnee.routes[0].legs[0].steps; // tableau qui va afficher tous les steps

          var tabPoint = []; // Il va contenir tous les points
          var tabInstruction = []; //tableau avec les directives de routes

          for (var i = 0; i < tabRoute.length; i++) {
            tabPoint.push(tabRoute[i].polyline.points); // On ajoute les points dans le tableau
            tabInstruction.push(tabRoute[i].html_instructions);
          }

          console.log(tabInstruction);

          // console.log("-------- Tableau avec les points encodés -------------");
          // console.log(tabPoint);

          // console.log("-------- Tableau avec les points décodés -------------");

          var tabDecode = [];
          for (var i = 0; i < tabPoint.length; i++) {
            tabDecode.push(polyline.decode(tabPoint[i]));
            //    console.log(tabPoint[i]);
          }

          // console.log(tabDecode);

          //On place le résultat dans notre mémoire cache pour ne pas à faire la requête la prochaine fois 
          //On sauvegarde dans les 2 sens
        

          // console.log("Key ==>" + depart.trim() + "---" + arrival.trim());

          var tabDecodeAndInstruction = [];
          tabDecodeAndInstruction.push(tabDecode);
          tabDecodeAndInstruction.push(tabInstruction);


          myCache.set(depart.trim() + "---" + arrive.trim(), tabDecodeAndInstruction, 100000);
          myCache.set(arrive.trim() + "---" + depart.trim(), tabDecodeAndInstruction, 100000);


          response.json(tabDecodeAndInstruction);


          //response.send(tabDecode);


        });





    })
  })







});

app.listen(3000, () => {
  console.log("le serveur tourne ");
})
