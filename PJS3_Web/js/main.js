if(Cookies.get('is-dark') == "true"){
	document.querySelector("body").classList.remove("default-theme");
	document.querySelector("body").classList.add("dark-theme");
}
  
$(".switch-theme").click(function (e){
	e.preventDefault();
	$("body").toggleClass("dark-theme");
	$("body").toggleClass("default-theme");
	Cookies.set('is-dark', $("body").hasClass("dark-theme"));
});

$(document).ready(() => {
  $('.slick').slick({
	arrows: true,
	dots: false,
  });
});

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
	this.classList.toggle("active");
	var panel = this.nextElementSibling;
	if (panel.style.maxHeight){
	  panel.style.maxHeight = null;
	} else {
	  panel.style.maxHeight = panel.scrollHeight + "px";
	}
  });
}