var g_map;
var g_service;
var g_infowindow;

$(document).ready(() => {
	$(".map-list").parent().css("height", $(".map-svg").css("height"));

	$("#map svg > a").each((index, elem) => {
		$(elem).attr("xlink:title", $(elem).attr("data-map-title"));
	});

	var map = $(".map");
	var paths = $(".map .map-svg a");
	var links = $(".map .map-list a");

	var activeArea = function (id) {
		$(map).find(".is-active").each((index, elem) => {
			$(elem).toggleClass("is-active");
		})

		if (id !== undefined) {
			$(map).find(".map-svg a[data-map-code=" + id + "]").addClass("is-active");
			$(map).find(".map-list a[data-map-code=" + id + "]").addClass("is-active");
		}
	}

	paths.each((index, elem) => {
		$(elem).mouseenter(function () {
			activeArea($(this).attr("data-map-code"));
		});
	});

	links.each((index, elem) => {
		$(elem).mouseover(function () {
			activeArea($(this).attr("data-map-code"));
		});
	});

	$("svg").mouseleave(() => {
		activeArea();
	});

	var clickCallback = function () {
		$(".map-overlay").show();
		$(".map-overlay .map-sidebar-header h1").text($(this).attr("data-map-title"));
		$("#item-link").attr("href", $(this).attr("data-map-href"));
		$(".map-loader").show();

		var wikiName = $(this).attr("data-wikipedia-name");

		var sydney = new google.maps.LatLng(-33.867, 151.195);
		g_infowindow = new google.maps.InfoWindow();
		g_map = new google.maps.Map(document.getElementById('g-map'), { center: sydney, zoom: 15 });

		var request = {
			query: $(".map-overlay .map-sidebar-header h1").text().trim(),
			fields: ['name', 'geometry', 'place_id', 'formatted_address'],
		};

		g_service = new google.maps.places.PlacesService(g_map);

		setTimeout(() => {
			$(".map-sidebar-roadwork .map-loader").hide();
		}, 500);

		/*fetch("http://pjs3.guillaumechalons.fr/scrapper.php?href=" + $(this).attr("data-map-href"))
		.then(response => response.text())
		.then(text => {
			var scrapper = $("<div>" + text + "</div>");
			scrapper = ($(scrapper).find("#content_main")[$(scrapper).find("#content_main").length - 2]);
			$(scrapper).find("a").each(function () {
				$(this)
					.attr("href", "https://www.bayeux-intercom.fr" + $(this).attr("href"));
			});
			$(scrapper).find("img").each(function () {
				$(this)
					.attr("src", "https://www.bayeux-intercom.fr" + $(this).attr("src"));
			});
			$(scrapper).find(".frame").each(function(){
				$(".map-sidebar-scrapper").append("<h4>" + $(this).find("h3").text() + "</h4>");
				var txt = "<p>";
				$(this).find("p").each(function(){
					if($(this).text().trim() != "")
						txt += $(this).text().trim() + "<br>";
				});
				$(this).find("figure").each(function(){
					txt += $(this).html();
				});
				$(".map-sidebar-scrapper").append(txt);
				$(".map-sidebar-scrapper").append("</p><br>");
			});

			$(".map-sidebar-scrapper .map-loader").hide();
		});*/

		fetch("https://geo.api.gouv.fr/communes?nom=" + request.query + "&fields=code,nom,population,surface,departement")
			.then((response) => response.text())
			.then((text) => {
				var json = JSON.parse(text);
				$(".map-sidebar-info h5").html(json[0].departement.nom + " (" + json[0].departement.code + ") - " + json[0].population + " habitants - " + json[0].surface + " m²"),
				$(".map-sidebar-info .map-loader").hide();

				fetch("http://fr.wikipedia.org/w/api.php?action=parse&prop=text&page=" + ((wikiName != undefined) ? wikiName : json[0].nom) + "&format=json&origin=*")
					.then((response) => response.text())
					.then((text) => {
						var json2 = JSON.parse(text);

						var wikipage = $("<div>" + json2.parse.text["*"] + "</div>");
						$(wikipage).find(".mw-empty-elt").first().remove();
						$(wikipage).find(".infobox_v2").first().remove();
						$(wikipage).find(".bandeau-article").first().remove();
						$(wikipage).find("sup").remove();
						$(wikipage).find(".API").remove();
						$(wikipage).find("a").each(function () {
							$(this)
								.attr("href", "http://fr.wikipedia.org" + $(this).attr("href"))
								.attr("target", "wikipedia");
						});

						var txt1 = wikipage.find(".mw-parser-output p:first").html().replace("[]", "");
						$("#wikipage").append("<p>" + txt1 + "</p>");

						try {
							var txt2 = wikipage.find(".mw-parser-output p:nth-child(2)").html().replace("[]", "");
							if (txt1 == txt2) {
								txt2 = wikipage.find(".mw-parser-output p:nth-child(3)").html().replace("[]", "");
							}

							if (txt2 != undefined) {
								$("#wikipage").append("<p>" + txt2 + "</p>");
							}
						} catch (e) { }

						$("#wikipage").append("<small><a href='http://fr.wikipedia.org/wiki/" + json[0].nom + "' target='wikipedia'>Source: wikipedia</a></small>");
						$(".map-sidebar-desc .map-loader").hide();
					});
			});

		g_service.findPlaceFromQuery(request, function (results, status) {
			if (status === google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < results.length; i++) {
					createMarker(results[i]);
				}
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				$(".map-overlay .map-sidebar-header h4").text(results[0].formatted_address);
				g_map.setCenter(results[0].geometry.location);
				$(".map-sidebar-header .map-loader").hide();

				var request = {
					placeId: results[0].place_id,
					fields: ['name', 'rating', 'formatted_phone_number', 'geometry', 'photos']
				};

				g_service = new google.maps.places.PlacesService(g_map);
				g_service.getDetails(request, callback);

				function callback(place, status) {
					if (status == google.maps.places.PlacesServiceStatus.OK) {
						if (place.photos != undefined) {
							for (var i = 0; i < place.photos.length; i++) {
								$("#lightgallery").append("<a class=\"gallery-item\" href=\"" + place.photos[i].getUrl() + "\"><img class=\"img-responsive\" src=\"" + place.photos[i].getUrl() + "\" /></a>");
							}
						} else {
							$("#lightgallery").append("<p>Aucunes photos disponibles dans la gallerie</p>");
						}
					}
					$('#lightgallery').slick({
						dots: true,
						arrows: false,
					});
					$(".map-sidebar-gallery .map-loader").hide();
				}

				fetch("https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "8&lon=" + lng + "&APPID=adae04af27c47fd1e9f14ebb4fd21508&lang=fr")
					.then((response) => response.text())
					.then((text) => {
						var json = JSON.parse(text);

						$(".map-weather").parent().find("h4").append(" (" + json.weather[0].description + ")");

						$("#tempeture").text(Math.floor(json.main.temp - 273.15) + "°C");
						$("#humidity").text(json.main.humidity + "%");
						$("#pressure").text(Math.floor(json.main.pressure / 1000) + "bar");

						$(".map-weather i").addClass("owf-" + json.weather[0].id);
						$(".map-sidebar-weather .map-loader").hide();
						$(".map-sidebar-weather .col-md-10").show();
					})
			}
		});

		function createMarker(place) {
			var marker = new google.maps.Marker({
				map: g_map,
				position: place.geometry.location
			});

			google.maps.event.addListener(marker, 'click', function () {
				infowindow.setContent(place.name);
				infowindow.open(g_map, this);
			});
		}
	};

	$(paths).click(clickCallback);
	$(links).click(function (e) {
		e.preventDefault();
		$("svg a[data-map-code='" + $(this).attr("data-map-code") + "']").click();
	});

	$(".map-overlay .close-overlay").click((e) => {
		e.preventDefault();
		$(".map-overlay").hide();
		$("[data-dynamic='true']").each((index, elem) => {
			$(elem).html("");
			$(elem).text("");
		});
		$('#lightgallery').remove();
		$(".map-sidebar-gallery").append("<div id=\"lightgallery\"></div>");
		$(".map-sidebar-weather h4").text("Metéo actuelle");
	});
});