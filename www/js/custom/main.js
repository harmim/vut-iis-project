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


$(function () {
	customMain.liveForm.init();
	customMain.netteAjax.init();
});
