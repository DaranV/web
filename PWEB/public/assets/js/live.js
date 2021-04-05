var state = "notest";
var r = false;
var testId = -1;

setInterval(() => {
	fetch(AJAX_LIVE_URL)
	.then((reponse) => reponse.text())
	.then((text) => {
		var json = JSON.parse(text);
		
		$("#state-" + state).hide();
		$("#state-" + json.state).show();
			
		if(json.state == "notest"){
			if(r) {
				$("#state-notest").hide();
				$("#state-ended").show();
				$("#link_bilan").attr("href", $("#link_bilan").attr("href").replace("-1", testId));
			}
		}else if(json.state == "waitingprof" || json.state == "alreadyreplied"){
			r = true;
			testId = json.data.test.id_test;
		}else if(json.state == "answering"){
			testId = json.data.test.id_test;
			if(state != "answering") {
				r = true;
				$("#result").html("");
				$("input[name='test_id']").val(json.data.test.id_test);
				$("input[name='question_id']").val(json.data.question.id_quest);
				
				Object.keys(json.data.question).forEach((elem, index) => {
					if($("[data-field='" +  elem + "']").length != 0){
						$("[data-field='" +  elem + "']").html(json.data.question[elem]);
					}else if($("[data-if='" +  elem + "']").length != 0){
						if(json.data.question[elem] == 1) {
							$("[data-if='" +  elem + "']").show();
						}else{
							$("[data-if='" +  elem + "']").hide();
						}
					}
				});
			
				$("#answers").html("");
				json.data.question.reponses.forEach((elem, index) => {
					if(json.data.question.bmultiple == 0){
						$("#answers").append(`
						<div class="form-group q-form">
							<input type="radio" id="ck_rep_${elem.id_rep}" value="${elem.id_rep}" name="rep" />
							<label for="ck_rep_${elem.id_rep}"><span></span>${elem.texte_rep}</label>
						</div>
						`);
					}else{
						$("#answers").append(`
						<div class="form-group q-form">
							<input type="checkbox" id="ck_rep_${elem.id_rep}" name="rep[]" value="${elem.id_rep}" />
							<label for="ck_rep_${elem.id_rep}">${elem.texte_rep}</label>
						</div>
						`);
					}
				});
			}
			
			if(json.data.question.bAutorise == 1 && json.data.question.bBloque == 0){
				$("[data-field='state']").html("Vous pouvez répondre à cette question.");
				$("button[type='submit']").attr("disabled", false);
			}else if(json.data.question.bAutorise == 1 && json.data.question.bBloque == 1){
				$("[data-field='state']").html("Vous ne poouvez plus répondre à cette question.");
				$("button[type='submit']").attr("disabled", true);
			}else if(json.data.question.bAnnule == 1){
				$("[data-field='state']").html("La question a été annulée.");
				$("button[type='submit']").attr("disabled", true);
			}
		}
		
		state = json.state;
	})
}, 500);