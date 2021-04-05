var tableClasses = $("#questions-table").DataTable({
    "sPaginationType": "full_numbers",
    aoColumnDefs: [
        {
            bSortable: false,
            aTargets: [-1]
        }
    ]
});

var tableClasses2 = $("#qcm-table").DataTable({
    "sPaginationType": "full_numbers",
    aoColumnDefs: [
        {
            bSortable: false,
            aTargets: [-1]
        }
    ]
});

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

function listen(){
	$("#preview3 input[type='checkbox']").change(function() {
		var theme = $(this).attr("data-theme-id");
		var question = $(this).attr("data-question-id");
		var $this = this;
		
		$("#result_question").css("display", "inline");
		$("#" + theme + "_loader").show();
		$("#result_question").html("<div class=\"alert alert-warning\">Sauvegarde en cours ...</div><br>");
		
		if(this.checked) {
			$.ajax({
				url: AJAX_URL_ADD_QUESTION,
				type: "post",
				data: "test_id=" + TEST_ID + "&question_id=" + question,
				dataType: "json",
				success: function(json, statut){
					if(!json.result.error){
						$("#result_question").html("<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.success + "</div><br>");
						$("#" + theme + "_selected_nb").text(parseInt($("#" + theme + "_selected_nb").text()) + 1);
						addRow(json.result.data);
						updatePreview()
					}else{
						$("#result_question").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.error + "</div><br>");
						$($this).prop("checked", false);
					}
					$("#" + theme + "_loader").hide();
				},
				error: function(json, statut, error){
					$("#result_question").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Une erreur est survenue, veuillez réessayer ultérieurement.</div><br>");
					$($this).prop("checked", false);
					$("#" + theme + "_loader").hide();
				}
			});
		} else{
			$.ajax({
				url: AJAX_URL_REMOVE_QUESTION,
				type: "post",
				data: "test_id=" + TEST_ID + "&question_id=" + question,
				dataType: "json",
				success: function(json, statut){
					if(!json.result.error){
						$("#result_question").html("<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.success + "</div><br>");
						$("#" + theme + "_selected_nb").text(parseInt($("#" + theme + "_selected_nb").text()) - 1);
						tableClasses
							.row($("#question_" + question).parent())
							.remove()
							.draw();
						updatePreview();
					}else{
						$("#result_question").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.error + "</div><br>");
						$($this).prop("checked", true);
					}
					$("#" + theme + "_loader").hide();
				},
				error: function(json, statut, error){
					$("#result_question").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Une erreur est survenue, veuillez réessayer ultérieurement.</div><br>");
					$($this).prop("checked", true);
					$("#" + theme + "_loader").hide();
				}
			});
		}
	});
}

listen();

function addRow(datas){
	console.log("add row");
	tableClasses.row.add([
		"<span id=\"question_" + datas.id_quest + "\">" + (tableClasses.rows().count() + 1) + "</span>",
		datas.titre,
		datas.texte,
		"<a href=\"" + datas.url + "\" class=\"btn btn-info\">Voir</a>"
	]).draw(false);
}

function updatePreview(){
	fetch(AJAX_URL_UPDATE_PREVIEW_0)
	.then((response) => response.text())
	.then((text) => {
		$("#preview1").html(text);
	})
	
	fetch(AJAX_URL_UPDATE_PREVIEW_1)
	.then((response) => response.text())
	.then((text) => {
		$("#preview2").html(text);
	})
	
	fetch(AJAX_URL_UPDATE_PREVIEW_3)
	.then((response) => response.text())
	.then((text) => {
		$("#preview3").html(text);
		listen();
	})
}

function afterCreateQuestion(json, form){
	var id = json.result.id;
	var titre = $(form).find("input[name='name']").val();
	
	$("#addQuestionForm").hide();
	$("#editQuestionForm").show();
	$("#answerForm").show();
	$("#editQuestionForm").find("input[name='id']").val(id);
	$("#answerForm").find("input[name='id']").val(id);
	$("#editQuestionForm").find("input[name='titre']").val(titre);
	
	updatePreview();
	
	$(form).find("input[name='name']").val("");
}

CKEDITOR.replace('desc');

var i = $("#reps > div").length;

function addRep(){
	$("#reps").append(`
	<div class="row" id="rep_${i}">
		<div class="col-md-9 col-sm-9">
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="selected[${i}]" style="display: inline !important;" />
				</span>
				<input type="text" name="rep[${i}]" class="form-control"  />
			</div>
		</div>
		<div class="col-md-3 col-sm-3 text-center">
			<button class="btn btn-danger" type="button" onclick="removeRep(${i})">
				<i class="fa fa-trash-o" style="margin-right: 0px;"></i> Supprimer
			</button>
		</div>
	</div>
	`);
	
	i++;
}

function removeRep(i){
	$("#rep_" + i).remove();
	
	if($("input[name='ids[" + i + "]']") != undefined){
		$("#reps").parent().append(`<input type="hidden" name="del[]" value="` + $("input[name='ids[" + i + "]']").val() + `" />`);
	}
}

function afterEditQuestion(json, form){
	updatePreview();
	
}

function afterEditAnswers(json, form){
	updatePreview();
	setTimeout(() => {
		$("#addQuestionForm").show();
		$("#editQuestionForm").hide();
		$("#answerForm").hide();
		$("#editQuestionForm").find("input[name='titre']").val("");
		$("#editQuestionForm").find("input[name='desc']").val("");
	}, 1000);
}

function del(url){
    bootbox.confirm({
        message: "Voulez-vous vraiment supprimer cette question ? <br> Cela supprimera toutes les réponses etc... des étudiants liés à cet question<br> Cette action est irréversible.",
        buttons: {
            confirm: {
                label: 'Oui',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Non',
                className: 'btn-info'
            }
        },
        callback: function (result) {
            if(result){
                remove(url);
            }
        }
    });
}

function remove(url){
    $.ajax({
        url: url,
        type: "get",
        dataType: "json",
        success: function(json, statut){
            if(!json.result.error){
                bootbox.alert("<center><i class=\"fa fa-check-circle fa-5\" style=\"color: green; font-size: 5em;margin-left: 10px;margin-bottom: 10px;\" aria-hidden=\"true\"></i> <p style=\"font-size: 2em;\">" + json.result.success + "</p></center>");
				updatePreview();
            }else{
                bootbox.alert("<font color=\"red\">" + json.result.error + "</font>");
            }
        },
        error: function(json, statut, error){
            bootbox.alert("<font color=\"red\">Une erreur est survenue, veuillez réessayer ultérieurement.</font>");
        }
    });
}

function edit(id){
	$("#addQuestionForm").hide();
	$("#editQuestionForm").show();
	$("#answerForm").show();
	
	$("#editQuestionForm").find("input[name='id']").val(id);
	$("#answerForm").find("input[name='id']").val(id);
	
	fetch(AJAX_URL_QUESTION_DETAIL.replace('-1', id))
	.then((response) => response.text())
	.then((text) => {
		var json = JSON.parse(text);
		$("#editQuestionForm").find("input[name='titre']").val(json.datas.titre);
		$("#editQuestionForm").find("input[name='desc']").val(json.datas.texte);
		
		var i = 0;
		$("#reps").html("");
		json.datas.reponses.forEach(function(element) {
			var c = (element.bvalide == 1) ? "checked" : "";
			$("#reps").append(`
			<div class="row" id="rep_${i}">
				<div class="col-md-9 col-sm-9">
				<div class="input-group">
					<span class="input-group-addon">
						<input type="checkbox" name="selected[${i}]" ${c} style="display: inline !important;" />
					</span>
					<input type="text" name="rep[${i}]" class="form-control" value="${element.texte_rep}" />
				</div>
				</div>
				<div class="col-md-3 col-sm-3 text-center">
				<button class="btn btn-danger" type="button" onclick="removeRep(${i})">
					<i class="fa fa-trash-o" style="margin-right: 0px;"></i> Supprimer
				</button>
				</div>
			</div>
			<input type="hidden" name="ids[${i}]" value="${element.id_rep}" />
			`);
			i++;
		});			
	});
}