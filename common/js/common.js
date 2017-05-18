function htmlEncode(str) {
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
function translate(){
	i18n.init({fallbackLng: "zh-CN",lng: Cookies.get("i18next"),resGetPath: 'resources/localization/__lng__/__ns__.json'}, function(err, t) {$("*").i18n();$("*").i18n();});
}
$(document).ready(function() {
	translate();
	if (url('?lang')) {
		Cookies.set("i18next", url('?lang'));
	    translate();
	}
});