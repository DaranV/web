var last = [];

setInterval(() => {
	fetch(AJAX_TEST_LIVE_STATS)
	.then((response) => response.text())
	.then((text) => {
		var json = JSON.parse(text);
		
		if(!(JSON.stringify(json.students) == JSON.stringify(last.students))){
			console.log("update onlines");
			$("#onlines").html("");
			json.students.forEach((elem, index) => {
				$("#onlines").append(`<li class="${((elem.online) ? "online" : "notonline")}">${elem.nom} ${elem.prenom} : ${elem.state}</li>`);
			});
		}
		
		if(!(JSON.stringify(json.questions) == JSON.stringify(last.questions))){
			console.log("update questions");
			$("#listlqcmlive").html("");
			$("#history-qcm").html("");
			
			json.questions.forEach((elem, index) => {
				if(elem.state == "nothing"){
					$("#listlqcmlive").append(`
						<div class="checkbox">
							<label><input ${((json.selected != null) ? "disabled" : "")} type="checkbox" data-question-id="${elem.id}" style="display: inline;">${elem.titre}</label>
						</div>
					`);
				}else if(elem.state == "closed"){
					$("#history-qcm").append(`
						<li id="history_${elem.id}">${elem.titre} (terminé)</li>
					`);
				}else if(elem.state == "annulated"){
					$("#history-qcm").append(`
						<li id="history_${elem.id}">${elem.titre} (annulé)</li>
					`);
				}else if(elem.state == "selected"){
					$("#history-qcm").append(`
						<li id="history_${elem.id}">${elem.titre} (en cours)</li>
					`);
				}
			});
		}
		
		if(!(JSON.stringify(json.selected) == JSON.stringify(last.selected))){
			if(json.selected == null){
				$("#selectedQuestion").html("Aucune question selectionnée");
			}else{
				$("#selectedQuestion").html(`
				<h4><span>${json.selected.titre}</span> <small>( <span>${json.selected.nbBonneRepAttendu}</span> points)</small></h4>
				<h5>${json.selected.texte}</h5>
				<div id="answers"></div>
				<div id="bmultiple" style="display: none;">
					<br>
					<small>* Plusieures réponses possibles</small>
				</div>
				<br>
				`);
				
				$("#selectedQuestion").append(`
					<div class="pie-chart">
						<div class="pie-canvas" id="pie-chart"></div>
							<ul class="chart-key">
							</ul>
						</div>
					</div>
				`);
				
				if(json.selected.bmultiple == 1) $("#bmultiple").show();
				else $("#bmultiple").hide();
				
				$("#answers").html("");
				json.selected.reponses.forEach((elem, index) => {
					$("#answers").append(`
					<div class="form-group q-form">
						<input disabled type="radio" id="ck_rep_${elem.id_rep}" value="${elem.id_rep}" name="rep" />
						<label for="ck_rep_${elem.id_rep}"><span></span>${elem.texte_rep} (x${elem.nb})</label>
					</div>
					`);
					
					$(".chart-key").append(`<li><span style="background: ${json.selected.chart.colors[index]};"></span>${elem.texte_rep} (x${elem.nb})</li>`);
				});
				
				$(".chart-key").append(`  <span class="pie-generator" data-target="pie-chart" data-colors='${JSON.stringify(json.selected.chart.colors)}' data-values='${JSON.stringify(json.selected.chart.values)}'></span>`);
			
				$(".pie-generator").each(function(index, value){
					var target = $("#" + $(value).attr("data-target"));
					var colors = JSON.parse($(value).attr("data-colors"));
					var values = JSON.parse($(value).attr("data-values"));
					
					$(target).sparkline(values, {
						type: "pie",
						height: "220",
						width: "220",
						offset: "+90",
						sliceColors: colors
					});
				});
				
				$("#selectedQuestion").append(`<div id="result_selected"></div>`);
				$("#selectedQuestion").append(`<br><button onclick="stopQuestion('closed')" class="btn btn-success">Arreter la question</button> <button onclick="stopQuestion('annulated')" class="btn btn-danger">Annuler la question</button>`);
			}
		}
		
		last = json;
	})
	
	listen2();
}, 500);

function stopQuestion(type){
	$.ajax({
		url: AJAX_TEST_STOP_QUESTION,
		type: "post",
		data: "test_id=" + TEST_ID + "&question_id=" + last.selected.id_quest + "&type=" + type,
		dataType: "json",
		success: function(json, statut){
			if(!json.result.success) {
				$("#result_selected").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.error + "</div><br>");
			}
		},
		error: function(json, statut, error){
			$("#result_selected").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Une erreur est survenue, veuillez réessayer ultérieurement.</div><br>");
		}
	});
}

function listen2(){
	$("#list-qcm-live input[type='checkbox']").change(function() {
		var question = $(this).attr("data-question-id");
		var $this = this;
		
		$("#list-qcm-live").find(":input").prop("disabled", true);
		$("#result_qcm").html("");
		
		$.ajax({
			url: AJAX_TEST_START_QUESTION,
			type: "post",
			data: "test_id=" + TEST_ID + "&question_id=" + question,
			dataType: "json",
			success: function(json, statut){
				if(json.result.success) {
					$($this).parent().remove();
				}else{
					$("#result_qcm").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.error + "</div><br>");
					$($this).prop("checked", false);
					$("#list-qcm-live").find(":input").prop("disabled", false);
				}
			},
			error: function(json, statut, error){
				$("#result_qcm").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Une erreur est survenue, veuillez réessayer ultérieurement.</div><br>");
				$($this).prop("checked", false);
				$("#list-qcm-live").find(":input").prop("disabled", false);
			}
		});
	});
}