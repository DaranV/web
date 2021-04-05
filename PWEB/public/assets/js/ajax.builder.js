var ERROR_MSG = "Une erreur est survenue.";
var ERROR_MSG_FORBIDDEN = "Vous n'avez pas la permission d'effectuer cette action";

function ajaxRegister(){
    console.log("ajax form registered");
    $(".ajax-form").each(function(index, value){
        $(value).submit(function(event) {
            event.preventDefault();

            var datas = $(this).serialize();
            $(this).find("#result").css("display", "inline");
            $(this).find(":input").prop("disabled", true);
            $(this).find(".icon").attr("class", "fa fa-spinner fa-spin");

            $.ajax({
                url: $(value).attr("data-url"),
                type: $(value).attr("method"),
                data: datas,
                dataType: "json",
                success: function(json, statut){
                    try {
                      if((json.result != undefined && json.result.success) && json.alert != "error"){
                        $(value).find("#result").html("<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.success + "</div><br>");
                        if($(value).attr("data-reset")){
                          $(value).trigger("reset");
                      }
					  
                      if($(value).attr("data-redirect") && !json.result.redirect){
                          setTimeout(function(){
                              redirect($(value).attr("data-redirect"));
                          }, 1000);
                      }
					  
					  if(json.result.redirect){
                        setTimeout(function(){
							redirect(json.result.redirect);
                        }, 1000);
                      }

                      if($(value).attr("data-callback")){
                        window.eval.call(window,'(function (json, form) { ' + $(value).attr("data-callback") + '(json, form); })')(json, $(value));
                      }
                    }else{
                        $(value).find("#result").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + json.result.error + "</div><br>");
                        
                        if($(value).attr("data-callback-error")){
                          window.eval.call(window,'(function (json, error, form) { ' + $(value).attr("data-callback-error") + '(json, error, form); })')(json, null, $(value));
                        }
                    }
                    }catch(e){
                      if($(value).attr("data-callback-error")){
                        window.eval.call(window,'(function (json, error, form) { ' + $(value).attr("data-callback-error") + '(json, error, form); })')(json, null, $(value));
                      }
                    }

                    $(value).find(":input").each(function (index, el){
                      if(!$(el).hasClass("disabled")){
                        $(el).prop("disabled", false);
                      }
                    });
                    
                    $(value).find(".icon").attr("class", "fa fa-edit");
                    
                },
                error: function(json, statut, error){
					if(error == "Unauthorized"){
						$(value).find("#result").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + ERROR_MSG_FORBIDDEN  + "</div><br>");
						$(value).find(":input").each(function (i, el){
						  if(!$(el).hasClass("disabled")){
							$(el).prop("disabled", false);
						  }
						});
						$(value).find(".icon").attr("class", "fa fa-edit");
						if($(value).attr("data-callback-error")){
						  window.eval.call(window,'(function (json, error, form) { ' + $(value).attr("data-callback-error") + '(json, error, form); })')(json, error, $(value));
						}
					}else{
						$(value).find("#result").html("<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + ERROR_MSG  + "</div><br>");
						$(value).find(":input").each(function (i, el){
						  if(!$(el).hasClass("disabled")){
							$(el).prop("disabled", false);
						  }
						});
						$(value).find(".icon").attr("class", "fa fa-edit");
						if($(value).attr("data-callback-error")){
						  window.eval.call(window,'(function (json, error, form) { ' + $(value).attr("data-callback-error") + '(json, error, form); })')(json, error, $(value));
						}
					}
                }
            });
        });
    });
}

function redirect(url) {
  var ua = navigator.userAgent.toLowerCase(),
    isIE = ua.indexOf("msie") !== -1,
    version = parseInt(ua.substr(4, 2), 10);
  if (isIE && version < 9) {
    var link = document.createElement("a");
    link.href = url;
    document.body.appendChild(link);
    link.click();
  } else {
	window.location.replace(url);
  }
}

ajaxRegister();