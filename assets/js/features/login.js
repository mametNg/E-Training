'use strict';

const mailControl = (method1, method2) => {
	const input = $(method1);
	const msg = $(method2);
	const validation = filterMail(method1);

	if (!validation) {
		input.removeClass("is-valid");
		input.addClass("is-invalid");
		msg.text("This email isn't valid!");
		return false;
	}

	if (validation) {
		input.addClass("is-valid");
		input.removeClass("is-invalid");
		msg.text("");
		return true;
	}
}

const passControl = (method1, method2) => {
	const input = $(method1);
	const msg = $(method2);

	let filter = filterLength(method1, 5);
	if (filter !== true) {
		input.removeClass("is-valid");
		input.addClass("is-invalid");
		msg.text(filter);
		return false;
	} else {
		input.addClass("is-valid");
		input.removeClass("is-invalid");
		msg.text("");
		return true;
	}
}

const login = (api) => {

	$("#input-uname").keyup(function() {
		const input = $("#input-uname");
		passControl("#"+input.attr("id"), "#msg-"+input.attr("id"));
	});

	$("#turn-passwd").click(function() {
		showPasswd("input-password", this.id);
	});

	$("#btn-login").click(function () {
		const btn = $("#btn-login");
		const uname = $("#input-uname");
		const passwd = $("#input-password");
		let allow = true;

		if (!passControl("#"+uname.attr("id"), "#msg-"+uname.attr("id"))) allow = false
		if (!passControl("#"+passwd.attr("id"), "#msg-"+passwd.attr("id"))) allow = false

		if (!allow) return false

		$(".air-badge").html(loadingBackdrop());
		btn.attr("disabled", true);
		uname.attr("disabled", true);
		passwd.attr("disabled", true);

		const params = {
			'uname': uname.val().trim(),
			'password': passwd.val().trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/"+api+"/login");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				$(".air-badge").html(airBadge(obj.msg , 'success'));
				setTimeout(function() {
					window.location = window.location.href;
				}, 5000);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				btn.attr("disabled", false);
				uname.attr("disabled", false);
				passwd.attr("disabled", false);
			}
		});

		execute.fail(function() {
			btn.attr("disabled", false);
			uname.attr("disabled", false);
			passwd.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});
}

let main = (function() {
	let isOn = $(".main-js").attr("my-js") || false;

	if (isOn == "member") login('v1');
	if (isOn == "admin") login('v6');
})();