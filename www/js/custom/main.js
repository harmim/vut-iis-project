"use strict";


let customMain;
if (typeof customMain === "undefined") {
	customMain = {};
}


customMain.liveForm = {
	init: function () {
		LiveForm.setOptions({
			showValid: true,
			messageErrorPrefix: ''
		});
	}
};


customMain.netteAjax = {
	init: function () {
		$.nette.init();
	}
};


customMain.fileInputFileName = {
	init: function () {
		$("input.custom-file-input").change(function () {
			$(this).next("label.custom-file-label").text($(this).val().split('\\').pop());
		});
	}
};


$(function () {
	customMain.liveForm.init();
	customMain.netteAjax.init();
	customMain.fileInputFileName.init();
});
