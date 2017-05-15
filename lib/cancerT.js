//当前页面导航变色
$(document).ready(function(){
	var title = $('title').text();
	$("#nav-ui a").removeClass("nav-color");
	$("#nav-ui a").each(function(){		
		if($(this).text()===title){
			$(this).addClass("nav-color").css("color", "white");
		}		
	});
});
