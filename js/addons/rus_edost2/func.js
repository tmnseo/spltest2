function edost_AddScript() {

	var E = document.getElementById('edost_office_css');
	if (E) return;

	var E2 = document.getElementById('edost_script_data');
	if (!E2) return;

	script = (window.JSON && window.JSON.parse ? JSON.parse(E2.value) : eval('(' + E2.value + ')'));

	var E = document.head;
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://');

	var E2 = document.createElement('LINK');
	E2.id = 'edost_office_css';
	E2.href = script.office_css;
	E2.type = 'text/css';
	E2.rel = 'stylesheet';
	E.appendChild(E2);

	var E2 = document.createElement('SCRIPT');
	E2.id = 'edost_office_script';
	E2.type = 'text/javascript';
	if (script.server) E2.charset = 'utf-8';
	E2.src = script.office_src;
	E.appendChild(E2);

	edost_RunScript();

}

function edost_RunScript() {

	if (window.edost_resize && window.edost_office) {
		edost_resize.start();

		var E = document.getElementById('edost_office_data');
		if (E) {
//			E.value = E.value.replace(/\*/g, '"');
			edost_office.window('parse');
		}
	}
	else window.setTimeout("edost_RunScript()", 500);

}

function edost_OpenOffice(id) {

	if (!window.edost_office || !window.edost_office.resize) return;

	edost_office.window(id);
	edost_office.resize();

}

function edost_SetOffice(profile, id, cod, mode) {

	if (window.edost_window && edost_window.cod) edost_window.submit();

	if (id == undefined) return;
	if (window.edost_window) edost_window.set('close');

	var E = document.getElementById('edost_office');
	if (E) E.value = 'edost' + ':' + profile + ':' + id + (cod != '' ? ':' + cod : '') + '|set';

	submitForm();

}

function submitForm() {

	var E = document.querySelector('input[name="DELIVERY_ID"]:checked');
	if (E) E.value += '|set';

	if (window.fn_calculate_total_shipping_cost) fn_calculate_total_shipping_cost();
	else {
		var E = $('button[name="dispatch[order_management.update_totals]"]');
		if (E[0]) E[0].click();
	}

}

$.ceEvent('on', 'ce.formpre_litecheckout_payments_form', function(form, a) {

	var E = $('div.cscart_edost_office_button_get');
	if (E[0]) {
		$.scrollToElm($('#cscart_edost_office_div'));
		var E = $('#cscart_edost_office_error');
		E.hide();
		E.show('slow');
		return false;
	}

});

$.ceEvent('on', 'ce.ajaxdone', function(event, a, b) {
	edost_RunScript();
});

edost_AddScript();