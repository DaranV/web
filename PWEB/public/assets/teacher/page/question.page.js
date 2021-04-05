CKEDITOR.replace('desc');

var i = $("#reps > div").length;

function addRep(){
	$("#reps").append(`
	<div class="row" id="rep_${i}">
		<div class="col-md-10 col-sm-9">
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="selected[${i}]" style="display: inline !important;" />
				</span>
				<input type="text" name="rep[${i}]" class="form-control"  />
			</div>
		</div>
		<div class="col-md-2 col-sm-3 text-center">
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