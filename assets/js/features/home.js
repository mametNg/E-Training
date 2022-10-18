'use strict';

const filterSelect = (method1, method2) => {
	let input = $(method1);
	let msg = $(method2);

	if (input.val() && input.val().length !== 0) {
		input.removeClass('is-invalid');
		input.addClass('is-valid');
		msg.text("");
		return true;
	} else {
		input.removeClass('is-valid');
		input.addClass('is-invalid');
		msg.text("Cannot be empty!");
		return false;
	}
}

const filterChecked = (method1, method2) => {
	let input = $(method1);
	let msg = $(method2);

	if (input.prop('checked')) {
		input.removeClass('is-invalid');
		input.addClass('is-valid');
		msg.text("");
		return true;
	} else {
		input.removeClass('is-valid');
		input.addClass('is-invalid');
		msg.text("Please checked!");
		return false;
	}
}

const autoSave = (data, path, input, cat) => {
	const params = {
		'id': data,
		'input': input,
		'cat': cat,
	};

	const executePost = {
		'data' : JEncrypt(JSON.stringify(params)),
	}

	const url = baseUrl("/auth/api/v2/"+path);

	const execute = postField(url, 'POST', executePost, false);

	// execute.done(function(result) {
	// 	let obj = JSON.parse(JSON.stringify(result));

	// 	if (obj.code == 200) {
	// 		$(".air-badge").html(airBadge(obj.msg , 'success'));
	// 		setTimeout(function() {
	// 			window.location = window.location.href;
	// 		}, 5000);
	// 	} else {
	// 		$(".air-badge").html(airBadge(obj.msg , 'danger'));
	// 	}
	// });

	// execute.fail(function() {
	// 	$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
	// });
}

const welcome = () => {

	$(document).ready(function() {
		$("#wrapper").addClass("vh-100");
	});

	$("#input-cat-bu").change(function() {
		const area = $("#input-cat-area");
		let sc = `<option disabled selected>Choose a Area</option>`;
		const params = {
			'bu': this.value,
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v1/catBu");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				for (let i = 0; i < obj.result.length; i++) {
					sc += `<option value="${obj.result[i].area}">${obj.result[i].area}</option>`;
				}

				area.html(sc);
				area.attr("disabled", false);
			} else {
				area.html(sc);
				area.attr("disabled", true);
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			area.html(sc);
			area.attr("disabled", true);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-start").click(function() {
		const bu = $("#input-cat-bu");
		const area = $("#input-cat-area");
		const ready = $("#input-cat-ready");
		const btn = $("#btn-start");
		let allow = true;

		if (!filterSelect("#"+bu.attr("id"), "#msg-"+bu.attr("id"))) allow = false;
		if (!filterSelect("#"+area.attr("id"), "#msg-"+area.attr("id"))) allow = false;
		if (!filterChecked("#"+ready.attr("id"), "#msg-"+ready.attr("id"))) allow = false;

		if (!allow) return false;

		bu.attr("disabled", true);
		area.attr("disabled", true);
		ready.attr("disabled", true);
		btn.attr("disabled", true);

		const params = {
			'bu': bu.val().trim(),
			'area': area.val().trim(),
			'checked': ready.prop("checked"),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v1/start");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				// for (let i = 0; i < obj.result.length; i++) {
				// 	sc += `<option value="${obj.result[i].area}">${obj.result[i].area}</option>`;
				// }

				window.location = window.location.href;

				// area.html(sc);
				area.attr("disabled", false);
			} else {
				bu.attr("disabled", false);
				area.attr("disabled", false);
				ready.attr("disabled", false);
				btn.attr("disabled", false);
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			bu.attr("disabled", false);
			area.attr("disabled", false);
			ready.attr("disabled", false);
			btn.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});
}

const quest = () => {
		let answer = null;
		let resetCountdown = false;

		$(document).ready(function() {
			const timer = $(".countdown-timer");
			countDown(timer.attr("data"), ".countdown", "Lost Session");
		});

		$('[id^="scroll-page"]').click(function() {
			const myData = $(this);
			$('html, body').stop().animate({
				scrollTop: ($("#"+myData.attr('target')).offset().top)
			}, 1000, 'easeInOutExpo');
		});

		$(".wm").click(function() {
			const myData = $(this);
			$("#image-modal").attr("src", myData.attr("src"));
		});


		$('.input-pg').click(function() {
			const myData = $(this);
			const allInput = $(".input-pg");
			const btnQuest = $('[id^="scroll-page"]');
			btnQuest[myData.attr('target').replace("quest-", "")].classList.remove("btn-secondary");
			btnQuest[myData.attr('target').replace("quest-", "")].classList.add("btn-primary");

			let s = $(".quest-start");
			let f = $(".quest-finish");

			let ss = parseInt(btnQuest.length);
			let ff = 0;

			for (let i = 0; i < btnQuest.length; i++) {
				if (btnQuest[i].getAttribute("class").includes("btn-primary")) {
					$(".question-"+i).removeClass("text-danger");
					ff++;
					ss--;
				}
			}

			s.text(ss);
			f.text(ff);
			
			autoSave(myData.attr("data"), "saving", myData.val(), myData.attr("cat"));
		});
		
		$(document).on('scroll', function() {
			let scrollDistance = $(this).scrollTop();
			if (scrollDistance > 100) {
				$('#number-quest').css("height", "100%");
				$('#number-quest').css("top", (scrollDistance-70)+"px");
			} else {
				$('#number-quest').removeAttr("style");
			}
		});

		$("#save-quest").click(function () {
			const quest = $(".scroll-page");
			let allow = true;

			for (let i = 0; i < quest.length; i++) {
				let input = $(".pg-"+i);
				let checked = false;

				for (let x = 0; x < input.length; x++) if (input[x].checked) checked = true;

				if (!checked) allow = false;
				if (!checked) quest[i].querySelector(".question-"+i).classList.add("text-danger");
				if (!checked) quest[i].querySelector(".question-"+i).classList.remove("text-dark");
				if (checked) quest[i].querySelector(".question-"+i).classList.remove("text-danger");
				if (checked) quest[i].querySelector(".question-"+i).classList.add("text-dark");

				// quest[i].querySelector(".question-"+i).focus();
			}


			if (!allow) $(".air-badge").html(airBadge("Make sure all questions are filled in!", 'danger'));
			if (!allow) return false;


			$("#modal-collect").modal("show");

			let params = {};

			for (let i = 0; i < quest.length; i++) {
				let input = $(".pg-"+i);
				params[i] = {};
				params[i]['id'] = input.attr("Data");

				for (let x = 0; x < input.length; x++) if (input[x].checked) params[i]['ans'] = input[x].value;
			}

			answer = {"answer": params};
		});

		$("#btn-collect").click(function() {
			const modal = $("#modal-collect");
			const btn = $("#btn-collect");

			modal.modal('hide');
			btn.attr("disabled", true);

			const url = baseUrl("/auth/api/v2/collect");
			const execute = postField(url, 'POST', answer, false);

			execute.done(function(result) {
				let obj = JSON.parse(JSON.stringify(result));

				if (obj.code == 200) {

					$(".air-badge").html(airBadge(obj.msg , 'success'));
					setTimeout(function() {
						window.location = obj.result.url;
					}, 5000);
				} else {
					btn.attr("disabled", false);
					$(".air-badge").html(airBadge(obj.msg , 'danger'));
				}
			});

			execute.fail(function() {
				btn.attr("disabled", false);
				$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			});
		});

		$("#modal-collect").on('hidden.bs.modal', function () {
			answer = null;
		});

		$(document).on("mouseover" ,function() {
			const countDown = $(".countdown");
			const timer = $(".countdown-timer");

			if (countDown.text() !== "Lost Session" || resetCountdown) return false;

			resetCountdown = true;
				
			const executePost = {
				'data' : JEncrypt(JSON.stringify("reset")),
			};

			const url = baseUrl("/auth/api/v2/timeout");
			const execute = postField(url, 'POST', executePost, false);

			execute.done(function(result) {
				let obj = JSON.parse(JSON.stringify(result));

				if (obj.code == 200) {
					window.location = obj.result.url;
				} else {
					$(".air-badge").html(airBadge(obj.msg , 'danger'));
				}
			});

			execute.fail(function() {
				$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			});
		});
}

const preview = () => {

	$(document).ready(function() {
		$("#wrapper").addClass("vh-100");
	});

	$("#btn-home").click(function() {
		$("#modal-back-home").modal('show');
	});

	$("#btn-back-home").click(function () {
		const executePost = {
			'data' : JEncrypt(JSON.stringify("end")),
		};

		const url = baseUrl("/auth/api/v2/finish");
		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				window.location = obj.result.url;
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-try").click(function() {
		$("#modal-try-quest").modal('show');
	});

	$("#btn-try-quest").click(function() {
		const modal = $("#modal-try-quest");
		modal.modal('hide');

		const executePost = {
			'data' : JEncrypt(JSON.stringify("try")),
		};

		const url = baseUrl("/auth/api/v2/try");
		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				window.location = obj.result.url;
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});

	});
}

const main = (function() {
	const isOn = $(".main-js").attr("my-js") || false;

	if (isOn == "welcome") welcome();
	if (isOn == "quest") quest();
	if (isOn == "preview") preview();
})();