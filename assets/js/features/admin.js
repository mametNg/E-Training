'use strict';

const filterImage = (method1, method2, fname) => {
	let input = $(method1);
	let msg = $(method2);
	let _fname = $(fname);

	if (input && input.prop('files').length == 1) {
		msg.text('');
		input.removeClass('is-invalid');
		input.addClass('is-valid');
		return true;
	}

	if (!method1 || !input.prop('files').length == 1) {
		msg.text("Please choose a image!");
		_fname.text("Choose a image");
		input.removeClass('is-valid');
		input.addClass('is-invalid');
		return false;
	}
}

const filterInput = (method1, method2) => {
	let input = $(method1);
	let msg = $(method2);

	let filter = filterLength(method1, 1);

	if (filter !== true) {
		input.removeClass('is-valid');
		input.addClass('is-invalid');
		msg.text(filter);
		return false;
	} else {
		input.removeClass('is-invalid');
		input.addClass('is-valid');
		msg.text("");
		return true;
	}
}

const filterCKEDITOR = (method1, method2, method3) => {
	let input = $(method1);
	let msg = $(method2);

	let filter = filterLengthCKEDITOR(method3, 6); 

	if (filter !== true) {
		input.removeClass('is-valid');
		input.addClass('is-invalid');
		msg.text(filter);
		return false;
	} else {
		input.removeClass('is-invalid');
		input.addClass('is-valid');
		msg.text("");
		return true;
	}
}

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

const filterName = (method1, method2) => {
	let inputBox = $(method1);
	let msgBox = $(method2);

	let filter = filterChar(inputBox, [" "], 3);

	if (filter.status) {
		inputBox.removeClass("is-invalid");
		inputBox.addClass("is-valid");
		msgBox.text('');
	}

	if (!filter.status) {
		inputBox.removeClass("is-valid");
		inputBox.addClass("is-invalid");
		msgBox.text(filter.msg);
	}

	return (filter.status == true) ? true:false;
}

const filterCode = (method1, method2, status=false, min=false, max=false, must=false) => {
	let inputBox = $(method1);
	let msgBox = $(method2);

	let filter = filterNumb(inputBox, status, min, max, must);

	if (!filter.status) {
		inputBox.removeClass("is-valid");
		inputBox.addClass("is-invalid");
		msgBox.text(filter.msg);
		return false;
	} else {
		inputBox.removeClass("is-invalid");
		inputBox.addClass("is-valid");
		msgBox.text("");
		return true;
	}
}

const __disableEnableExam = (data, path, status="N") => {
	const params = {
		'id': data,
		'status': status,
	};

	const executePost = {
		'data' : JEncrypt(JSON.stringify(params)),
	}

	const url = baseUrl("/auth/api/v6/"+path);

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
		}
	});

	execute.fail(function() {
		$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
	});
}

const manageExam = () => {

	const listViewExam = (arr) => {
		const myData = arr;
		const modal = $("#modal-view-exam");

		const params = {
			'id': myData.attr('data').trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printExam");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				modal.modal('show');

				let ul = '';
				for (let i = 65; i <= 68; i++) ul += '<li>'+obj.result['quest_'+String.fromCharCode(i).toLowerCase()]+'</li>';

				if (obj.result.image.length !== 0) {
					$("#exam-box-img").removeClass('d-none');
					$("#exam-img").attr('src', obj.result.image);
				}

				$("#exam-quest").html(obj.result.quest);
				$("#exam-answers").html(ul);
				$("#exam-answer").text(obj.result.answer_key.toUpperCase());

			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	}

	const listEditExam = (arr) => {
		const myData = $(arr);
		const modal = $("#modal-edit-exam");

		const params = {
			'id': myData.attr('data').trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printExam");
		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				modal.attr('target', myData.attr('data').trim());
				modal.modal('show');

				if (CKEDITOR.instances['input-edit-pertanyaan']) CKEDITOR.instances['input-edit-pertanyaan'].destroy();

				const bu = $("#input-edit-bu");
				const area = $("#input-edit-area");
				const category = $("#input-edit-category");
				const pertanyaan = $("#input-edit-pertanyaan");
				const turnImage = $("#turn-edit-image");
				const image = $("#input-edit-new-image");
				const imageThumb = $("#edit-img-thumbnail");
				const answerA = $("#input-edit-answer-a");
				const answerB = $("#input-edit-answer-b");
				const answerC = $("#input-edit-answer-c");
				const answerD = $("#input-edit-answer-d");
				const answerKey = $("#input-edit-answer-key");

				const slectdef = $(".select-def");
				const slecta = $(".select-a");
				const slectb = $(".select-b");
				const slectc = $(".select-c");
				const slectd = $(".select-d");

				bu.val(obj.result.bu);
				area.val(obj.result.area);
				category.val(obj.result.cat);
				pertanyaan.val(obj.result.quest);
				answerA.val(obj.result.quest_a);
				answerB.val(obj.result.quest_b);
				answerC.val(obj.result.quest_c);
				answerD.val(obj.result.quest_d);

				if (obj.result.image.trim().length !== 0) imageThumb.attr("src", obj.result.image);
				if (obj.result.image.trim().length == 0) imageThumb.attr("src", baseUrl("/assets/img/account/default.jpg"));

				if (obj.result.answer_key.toUpperCase().trim() == 'A') {
					slectdef.removeAttr("selected");
					slecta.attr("selected", true);
				}
				if (obj.result.answer_key.toUpperCase().trim() == 'B') {
					slectdef.removeAttr("selected");
					slectb.attr("selected", true);
				}
				if (obj.result.answer_key.toUpperCase().trim() == 'C') {
					slectdef.removeAttr("selected");
					slectc.attr("selected", true);
				}
				if (obj.result.answer_key.toUpperCase().trim() == 'D') {
					slectdef.removeAttr("selected");
					slectd.attr("selected", true);
				}

				CKEDITOR.replace('input-edit-pertanyaan');
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	}

	const listDeleteExam = (arr) => {
		const myData = $(arr);
		const txt = $(".info-delete-exam").text(myData.attr('data').trim());
		const btn = $("#save-delete-exam").attr("data-info", myData.attr('data').trim());
	}

	const listDisableExam = (arr) => {
		const myData = $(arr);
		const txt = $(".info-disable-exam").text(myData.attr('data').trim());
		const btn = $("#save-disable-exam").attr("data-info", myData.attr('data').trim());
	}

	const listEnableExam = (arr) => {
		const myData = $(arr);
		const txt = $(".info-enable-exam").text(myData.attr('data').trim());
		const btn = $("#save-enable-exam").attr("data-info", myData.attr('data').trim());
	}


	const desc = CKEDITOR.replace('input-pertanyaan');
	// const descEdit = CKEDITOR.replace('input-edit-pertanyaan');
	let fileName = null;
	let changeImageFile = null;
	let TypeAction = null;
	let fileInput = null;
	let myFile = null;

	$("#input-cat-bu").change(function() {
		const area = $("#input-cat-area");
		const cat = $("#input-cat-cat");
		let sc = `<option disabled selected>Choose a Area</option>`;
		let scCat = `<option disabled selected>Choose a Category</option>`;
		const params = {
			'bu': this.value,
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/manegeExamBu");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				for (let i = 0; i < obj.result.length; i++) {
					sc += `<option value="${obj.result[i].area}">${obj.result[i].area}</option>`;
				}

				area.html(sc);
				area.attr("disabled", false);
				cat.html(scCat);
				cat.attr("disabled", true);
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

	$("#input-cat-area").change(function() {
		const bu = $("#input-cat-bu");
		const area = $("#input-cat-area");
		let dataTable = $('#dataTable').DataTable();
		let sc = ``;
		let scCat = `<option disabled selected>Choose a Category</option>`;

		const params = {
			'bu': bu.val(),
			'area': area.val(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/manegeExamArea");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.quests.length; i++) {
					sc += `
					<tr>
						<td class="text-wrap align-middle text-center">
							<div class="form-check">
		                      <input class="form-check-input" type="checkbox" value="" id="list-exam" data="${obj.result.quests[i].id}">
		                      <label class="form-check-label" for="list-exam"></label>
		                    </div>
						</td>
						<td class="text-wrap align-middle text-center">${(i+1)}</td>
						<td>${obj.result.quests[i].bu}</td>
						<td>${obj.result.quests[i].area}</td>
						<td>${obj.result.quests[i].cat}</td>
						<td>${obj.result.quests[i].quest}</td>
						<td class="text-wrap align-middle text-center">${(obj.result.quests[i].status == 1 ? "Active":"Non-Active")}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-between">
							<a href="#" id="list-edit-exam" data-toggle="modal" data="${obj.result.quests[i].id}" class="m-1 badge badge-success"><span class="mr-2">Edit</span><i class="fas fa-fw fa-edit"></i></a>
							<a href="#" id="list-delete-exam" data-toggle="modal" data-target="#modal-delete-exam" data="${obj.result.quests[i].id}" class="m-1 badge badge-danger"><span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i></a>
							<a href="#" id="list-view-exam" data-toggle="modal" data="${obj.result.quests[i].id}" class="m-1 badge badge-info"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
							${(obj.result.quests[i].status == 1 ? `<a href="#" id="list-disable-exam" data-toggle="modal" data-target="#modal-disable-exam" data="${obj.result.quests[i].id}" class="m-1 badge badge-dark"><span class="mr-2">Disable</span><i class="fas fa-fw fa-eye"></i></a>`:`<a href="#" id="list-enable-exam" data-toggle="modal" data-target="#modal-enable-exam" data="${obj.result.quests[i].id}" class="m-1 badge badge-secondary"><span class="mr-2">Enable</span><i class="fas fa-fw fa-eye"></i></a>`)}
							</div>
						</td>
					</tr>
					`;
				}

				for (let i = 0; i < obj.result.cat.length; i++) {
					scCat += `<option value="${obj.result.cat[i]}">${obj.result.cat[i]}</option>`;
				}

				dataTable.destroy();
				$("#cos-quest").html(sc);
				$('#dataTable').DataTable()
				$("#input-cat-cat").attr("disabled", false);
				$("#input-cat-cat").html(scCat);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			area.attr("disabled", true);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#input-cat-cat").change(function() {
		const bu = $("#input-cat-bu");
		const area = $("#input-cat-area");
		const cat = $("#input-cat-cat");
		const tableBody = $("#cos-quest");
		let dataTable = $('#dataTable').DataTable();
		let sc = ``;

		const params = {
			'bu': bu.val(),
			'area': area.val(),
			'cat': cat.val(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/manegeExamCat");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				for (let i = 0; i < obj.result.length; i++) {
					sc += `
					<tr>
						<td class="text-wrap align-middle text-center">
							<div class="form-check">
		                      <input class="form-check-input" type="checkbox" value="" id="list-exam" data="${obj.result[i].id}">
		                      <label class="form-check-label" for="list-exam"></label>
		                    </div>
						</td>
						<td class="text-wrap align-middle text-center">${(i+1)}</td>
						<td>${obj.result[i].bu}</td>
						<td>${obj.result[i].area}</td>
						<td>${obj.result[i].cat}</td>
						<td>${obj.result[i].quest}</td>
						<td class="text-wrap align-middle text-center">${(obj.result[i].status == 1 ? "Active":"Non-Active")}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-between">
							<a href="#" id="list-edit-exam" data-toggle="modal" data="${obj.result[i].id}" class="m-1 badge badge-success"><span class="mr-2">Edit</span><i class="fas fa-fw fa-edit"></i></a>
							<a href="#" id="list-delete-exam" data-toggle="modal" data-target="#modal-delete-exam" data="${obj.result[i].id}" class="m-1 badge badge-danger"><span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i></a>
							<a href="#" id="list-view-exam" data-toggle="modal" data="${obj.result[i].id}" class="m-1 badge badge-info"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
							${(obj.result[i].status == 1 ? `<a href="#" id="list-disable-exam" data-toggle="modal" data-target="#modal-disable-exam" data="${obj.result[i].id}" class="m-1 badge badge-dark"><span class="mr-2">Disable</span><i class="fas fa-fw fa-eye"></i></a>`:`<a href="#" id="list-enable-exam" data-toggle="modal" data-target="#modal-enable-exam" data="${obj.result[i].id}" class="m-1 badge badge-secondary"><span class="mr-2">Enable</span><i class="fas fa-fw fa-eye"></i></a>`)}
							</div>
						</td>
					</tr>
					`;
				}

				dataTable.destroy();
				tableBody.html(sc);
				$('#dataTable').DataTable()
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-show-all-quest").click(function() {
		let dataTable = $('#dataTable').DataTable();
		let sc = ``;

		const params = {
			'quest': "all",
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/manegeExamShowAll");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.length; i++) {
					sc += `
					<tr>
						<td class="text-wrap align-middle text-center">
							<div class="form-check">
		                      <input class="form-check-input" type="checkbox" value="" id="list-exam" data="${obj.result[i].id}">
		                      <label class="form-check-label" for="list-exam"></label>
		                    </div>
						</td>
						<td class="text-wrap align-middle text-center">${(i+1)}</td>
						<td>${obj.result[i].bu}</td>
						<td>${obj.result[i].area}</td>
						<td>${obj.result[i].cat}</td>
						<td>${obj.result[i].quest}</td>
						<td class="text-wrap align-middle text-center">${(obj.result[i].status == 1 ? "Active":"Non-Active")}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-between">
							<a href="#" id="list-edit-exam" data-toggle="modal" data="${obj.result[i].id}" class="m-1 badge badge-success"><span class="mr-2">Edit</span><i class="fas fa-fw fa-edit"></i></a>
							<a href="#" id="list-delete-exam" data-toggle="modal" data-target="#modal-delete-exam" data="${obj.result[i].id}" class="m-1 badge badge-danger"><span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i></a>
							<a href="#" id="list-view-exam" data-toggle="modal" data="${obj.result[i].id}" class="m-1 badge badge-info"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
							${(obj.result[i].status == 1 ? `<a href="#" id="list-disable-exam" data-toggle="modal" data-target="#modal-disable-exam" data="${obj.result[i].id}" class="m-1 badge badge-dark"><span class="mr-2">Disable</span><i class="fas fa-fw fa-eye"></i></a>`:`<a href="#" id="list-enable-exam" data-toggle="modal" data-target="#modal-enable-exam" data="${obj.result[i].id}" class="m-1 badge badge-secondary"><span class="mr-2">Enable</span><i class="fas fa-fw fa-eye"></i></a>`)}
							</div>
						</td>
					</tr>
					`;
				}
				$("#input-cat-area").attr("disabled", true);
				$("#input-cat-area").html(`<option disabled selected>Choose a Area</option>`);
				$("#input-cat-cat").attr("disabled", true);
				$("#input-cat-cat").html(`<option disabled selected>Choose a Category</option>`);
				dataTable.destroy();
				$("#cos-quest").html(sc);
				$('#dataTable').DataTable()
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#modal-view-exam").on('hidden.bs.modal', function () {
		$("#exam-quest").html('');
		$("#exam-answers").html('');
		$("#exam-answer").text('');
		$("#exam-box-img").addClass('d-none');
		$("#exam-img").attr('src', '');
	});

	$("#list-delete-exam").on('hidden.bs.modal', function () {
		$(".info-delete-exam").text('');
		$("#save-delete-exam").attr("data-info", '');
	});

	$("#list-disable-exam").on('hidden.bs.modal', function () {
		$(".info-disable-exam").text('');
		$("#save-disable-exam").attr("data-info", '');
	});

	$("#list-enable-exam").on('hidden.bs.modal', function () {
		$(".info-enable-exam").text('');
		$("#save-enable-exam").attr("data-info", '');
	});

	$(document).on('click', '[id^="list-view-exam"]', function () {
		const myData = $(this);
		listViewExam(myData);
	});

	$(document).on('click', '[id^="list-edit-exam"]', function () {
		const myData = $(this);
		listEditExam(myData);
	});

	$(document).on('click', '[id^="list-delete-exam"]', function () {
		const myData = $(this);
		listDeleteExam(myData);
	});

	$(document).on('click', '[id^="list-disable-exam"]', function () {
		const myData = $(this);
		listDisableExam(myData);
	});

	$(document).on('click', '[id^="list-enable-exam"]', function () {
		const myData = $(this);
		listEnableExam(myData);
	});

	$("#save-enable-exam").click(function() {
		const btn = $("#save-enable-exam");
		const txt = btn.attr("data-info");

		$("#modal-enable-exam").modal('hide');
		$(".air-badge").html(loadingBackdrop());
		__disableEnableExam(txt, 'setExamp', "Y");

		btn.attr("data-info", "");
		$(".info-enable-exam").text('');
	});

	$("#save-disable-exam").click(function() {
		const btn = $("#save-disable-exam");
		const txt = btn.attr("data-info");

		$("#modal-disable-exam").modal('hide');
		$(".air-badge").html(loadingBackdrop());
		__disableEnableExam(txt, 'setExamp');

		btn.attr("data-info", "");
		$(".info-disable-exam").text('');
	});

	$("#turn-new-image").click(function() {
		let turn = $("#turn-new-image");
		let label = $("#label-turn-new-image");
		let change = $("#input-new-image");
		let msg = $("#msg-input-new-image");

		if (turn.prop('checked')) {
			// label.text("Disabled change image");
			change.addClass("custom-input-file--2");
			change.attr("disabled", false);
			if (fileName) fileName.text("Choose a image");
			$("#new-img-thumbnail").attr("src", baseUrl("/assets/img/account/default.jpg"));
		} else {
			// label.text("Enable change image");
			change.removeClass("is-invalid");
			change.removeClass("custom-input-file--2");
			msg.text("");
			change.attr("disabled", true);
			change.val("");
		}
	});

	$("#input-new-image").click(function() {
		const choose = $(this);
		TypeAction = choose.attr("data-choose");
		fileInput = choose;

		// if (TypeAction == "new") fileName = $(".new-file-name");
		// if (TypeAction == "change") fileName = $(".change-file-name");
		fileName = (TypeAction == "new" ? $(".new-file-name") : (TypeAction == "change" ? $(".change-file-name") : null));

		fileName.text("Choose a image");
		fileInput.val("");
	});

	$("#input-new-image").change(function() {
		myFile = fileInput.prop('files')[0];
		let modal = $("#modal-new-exam");

		fileName.text("Choose a image");

		if (imgExtension(myFile) == false ) {
			modal.modal('hide');
			$(".air-badge").html(airBadge("The file must be an image!" , 'danger'));
			return false;
		}

		const reader = new FileReader();
		reader.onload = function() {

			const img = new Image;
			img.onload = function() {
				changeImageFile = fileInput;
				fileName.text(myFile.name);
				$("#new-img-thumbnail").attr("src", reader.result);
			};

			img.onerror = function() {
				modal.modal('hide');
				fileInput.val("");
				$(".air-badge").html(airBadge("Malicious files detected!" , 'danger'));
			};
			img.src = reader.result;
		}
		reader.readAsDataURL(myFile);
	});

	$("#add-new-exam").click(function() {
		const modal = $("#modal-new-exam");
		const bu = $("#input-bu");
		const area = $("#input-area");
		const category = $("#input-category");
		const pertanyaan = $("#input-pertanyaan");
		const turnImage = $("#turn-new-image");
		const image = $("#input-new-image");
		const imageName = $(".change-file-name");
		const answerA = $("#input-answer-a");
		const answerB = $("#input-answer-b");
		const answerC = $("#input-answer-c");
		const answerD = $("#input-answer-d");
		const answerKey = $("#input-answer-key");
		const btn = $("#add-new-exam");
		let allow = true;
		let onFile = null;
		let formData = null;

		if (!filterInput("#"+bu.attr("id"), "#msg-"+bu.attr("id"))) allow = false;
		if (!filterInput("#"+area.attr("id"), "#msg-"+area.attr("id"))) allow = false;
		if (!filterInput("#"+category.attr("id"), "#msg-"+category.attr("id"))) allow = false;
		if (!filterCKEDITOR("#"+pertanyaan.attr("id"), "#msg-"+pertanyaan.attr("id"), CKEDITOR.instances['input-pertanyaan'].getData())) allow = false;
		if (turnImage.prop("checked")) {
			if (!filterImage("#"+image.attr("id"), "#msg-"+image.attr("id"), "."+imageName.attr("class"))) allow = false;
			if (!changeImageFile || !changeImageFile.prop('files').length == 1) {
				$("#msg-"+image.attr("id")).text("Please choose a image!");
				imageName.text("Choose a image");
				image.removeClass('is-valid');
				image.addClass('is-invalid');
				allow = false;
			}
		}
		if (!filterInput("#"+answerA.attr("id"), "#msg-"+answerA.attr("id"))) allow = false;
		if (!filterInput("#"+answerB.attr("id"), "#msg-"+answerB.attr("id"))) allow = false;
		if (!filterInput("#"+answerC.attr("id"), "#msg-"+answerC.attr("id"))) allow = false;
		if (!filterInput("#"+answerD.attr("id"), "#msg-"+answerD.attr("id"))) allow = false;
		if (!filterSelect("#"+answerKey.attr("id"), "#msg-"+answerKey.attr("id"))) allow = false;

		if (!allow) return false;
		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());
		
		bu.attr("disabled", true);
		area.attr("disabled", true);
		category.attr("disabled", true);
		pertanyaan.attr("disabled", true);
		turnImage.attr("disabled", true);
		image.attr("disabled", true);
		answerA.attr("disabled", true);
		answerB.attr("disabled", true);
		answerC.attr("disabled", true);
		answerD.attr("disabled", true);
		answerKey.attr("disabled", true);
		btn.attr("disabled", true);

		let params = {
			'bu' : bu.val().trim(),
			'area' : area.val().trim(),
			'category' : category.val().trim(),
			'answerA' : answerA.val().trim(),
			'answerB' : answerB.val().trim(),
			'answerC' : answerC.val().trim(),
			'answerD' : answerD.val().trim(),
			'answerKey' : answerKey.val().trim(),
		};

		if (turnImage.prop("checked")) {
			params['on-image'] = turnImage.prop("checked");

			formData = new FormData();
			formData.append('data', JEncrypt(JSON.stringify(params)));
			formData.append('image', fileInput.prop('files')[0]);
			formData.append('exam', btoa(unescape(CKEDITOR.instances['input-pertanyaan'].getData())));
			onFile = true;
		} else {
			formData = {
				'data' : JEncrypt(JSON.stringify(params)),
				'exam' : btoa(unescape(CKEDITOR.instances['input-pertanyaan'].getData())),
			}
			onFile = false;
		}

		const url = baseUrl("/auth/api/v6/newExam");
		const execute = postField(url, 'POST', formData, false, onFile);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				$(".air-badge").html(airBadge(obj.msg , 'success'));
				setTimeout(function() {
					window.location = window.location.href;
				}, 5000);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				bu.attr("disabled", false);
				area.attr("disabled", false);
				category.attr("disabled", false);
				pertanyaan.attr("disabled", false);
				if (turnImage.prop("checked")) {
					turnImage.attr("disabled", false);
					image.attr("disabled", false);
				}
				answerA.attr("disabled", false);
				answerB.attr("disabled", false);
				answerC.attr("disabled", false);
				answerD.attr("disabled", false);
				answerKey.attr("disabled", false);
				btn.attr("disabled", false);
			}
		});

		execute.fail(function() {
			bu.attr("disabled", false);
			area.attr("disabled", false);
			category.attr("disabled", false);
			pertanyaan.attr("disabled", false);
			if (turnImage.prop("checked")) {
				turnImage.attr("disabled", false);
				image.attr("disabled", false);
			}
			answerA.attr("disabled", false);
			answerB.attr("disabled", false);
			answerC.attr("disabled", false);
			answerD.attr("disabled", false);
			answerKey.attr("disabled", false);
			btn.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#turn-edit-image").click(function() {
		let turn = $("#turn-edit-image");
		let label = $("#label-turn-edit-image");
		let change = $("#input-edit-image");
		let msg = $("#msg-input-edit-image");

		if (turn.prop('checked')) {
			// label.text("Disabled change image");
			change.addClass("custom-input-file--2");
			change.attr("disabled", false);
			if (fileName) fileName.text("Choose a image");
			$("#edit-img-thumbnail").attr("src", baseUrl("/assets/img/account/default.jpg"));
		} else {
			// label.text("Enable change image");
			change.removeClass("is-invalid");
			change.removeClass("custom-input-file--2");
			msg.text("");
			change.attr("disabled", true);
			change.val("");
		}
	});

	$("#input-edit-image").click(function() {
		const choose = $(this);
		TypeAction = choose.attr("data-choose");
		fileInput = choose;

		// if (TypeAction == "edit") fileName = $(".edit-file-name");
		// if (TypeAction == "change") fileName = $(".change-file-name");
		fileName = (TypeAction == "edit" ? $(".edit-file-name") : (TypeAction == "change" ? $(".change-file-name") : null));

		fileName.text("Choose a image");
		fileInput.val("");
	});

	$("#input-edit-image").change(function() {
		myFile = fileInput.prop('files')[0];
		let modal = $("#modal-edit-exam");

		fileName.text("Choose a image");

		if (imgExtension(myFile) == false ) {
			modal.modal('hide');
			$(".air-badge").html(airBadge("The file must be an image!" , 'danger'));
			return false;
		}

		const reader = new FileReader();
		reader.onload = function() {

			const img = new Image;
			img.onload = function() {
				changeImageFile = fileInput;
				fileName.text(myFile.name);
				$("#edit-img-thumbnail").attr("src", reader.result);
			};

			img.onerror = function() {
				modal.modal('hide');
				fileInput.val("");
				$(".air-badge").html(airBadge("Malicious files detected!" , 'danger'));
			};
			img.src = reader.result;
		}
		reader.readAsDataURL(myFile);
	});

	$("#add-edit-exam").click(function() {
		const modal = $("#modal-edit-exam");
		const bu = $("#input-edit-bu");
		const area = $("#input-edit-area");
		const category = $("#input-edit-category");
		const pertanyaan = $("#input-edit-pertanyaan");
		const turnImage = $("#turn-edit-image");
		const image = $("#input-edit-image");
		const imageName = $(".change-file-name");
		const answerA = $("#input-edit-answer-a");
		const answerB = $("#input-edit-answer-b");
		const answerC = $("#input-edit-answer-c");
		const answerD = $("#input-edit-answer-d");
		const answerKey = $("#input-edit-answer-key");
		const btn = $("#add-edit-exam");
		let allow = true;
		let onFile = null;
		let formData = null;

		if (!filterInput("#"+bu.attr("id"), "#msg-"+bu.attr("id"))) allow = false;
		if (!filterInput("#"+area.attr("id"), "#msg-"+area.attr("id"))) allow = false;
		if (!filterInput("#"+category.attr("id"), "#msg-"+category.attr("id"))) allow = false;
		if (!filterCKEDITOR("#"+pertanyaan.attr("id"), "#msg-"+pertanyaan.attr("id"), CKEDITOR.instances['input-edit-pertanyaan'].getData())) allow = false;
		if (turnImage.prop("checked")) {
			if (!filterImage("#"+image.attr("id"), "#msg-"+image.attr("id"), "."+imageName.attr("class"))) allow = false;
			if (!changeImageFile || !changeImageFile.prop('files').length == 1) {
				$("#msg-"+image.attr("id")).text("Please choose a image!");
				imageName.text("Choose a image");
				image.removeClass('is-valid');
				image.addClass('is-invalid');
				allow = false;
			}
		}
		if (!filterInput("#"+answerA.attr("id"), "#msg-"+answerA.attr("id"))) allow = false;
		if (!filterInput("#"+answerB.attr("id"), "#msg-"+answerB.attr("id"))) allow = false;
		if (!filterInput("#"+answerC.attr("id"), "#msg-"+answerC.attr("id"))) allow = false;
		if (!filterInput("#"+answerD.attr("id"), "#msg-"+answerD.attr("id"))) allow = false;
		if (!filterSelect("#"+answerKey.attr("id"), "#msg-"+answerKey.attr("id"))) allow = false;

		if (!allow) return false;
		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());
		
		bu.attr("disabled", true);
		area.attr("disabled", true);
		category.attr("disabled", true);
		pertanyaan.attr("disabled", true);
		turnImage.attr("disabled", true);
		image.attr("disabled", true);
		answerA.attr("disabled", true);
		answerB.attr("disabled", true);
		answerC.attr("disabled", true);
		answerD.attr("disabled", true);
		answerKey.attr("disabled", true);
		btn.attr("disabled", true);

		let params = {
			'id' : modal.attr("target").trim(),
			'bu' : bu.val().trim(),
			'area' : area.val().trim(),
			'category' : category.val().trim(),
			'answerA' : answerA.val().trim(),
			'answerB' : answerB.val().trim(),
			'answerC' : answerC.val().trim(),
			'answerD' : answerD.val().trim(),
			'answerKey' : answerKey.val().trim(),
		};

		if (turnImage.prop("checked")) {
			params['on-image'] = turnImage.prop("checked");

			formData = new FormData();
			formData.append('data', JEncrypt(JSON.stringify(params)));
			formData.append('image', fileInput.prop('files')[0]);
			formData.append('exam', btoa(unescape(CKEDITOR.instances['input-edit-pertanyaan'].getData())));
			onFile = true;
		} else {
			formData = {
				'data' : JEncrypt(JSON.stringify(params)),
				'exam' : btoa(unescape(CKEDITOR.instances['input-edit-pertanyaan'].getData())),
			}
			onFile = false;
		}

		const url = baseUrl("/auth/api/v6/editExam");
		const execute = postField(url, 'POST', formData, false, onFile);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				$(".air-badge").html(airBadge(obj.msg , 'success'));
				setTimeout(function() {
					window.location = window.location.href;
				}, 5000);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				bu.attr("disabled", false);
				area.attr("disabled", false);
				category.attr("disabled", false);
				pertanyaan.attr("disabled", false);
				if (turnImage.prop("checked")) {
					turnImage.attr("disabled", false);
					image.attr("disabled", false);
				}
				answerA.attr("disabled", false);
				answerB.attr("disabled", false);
				answerC.attr("disabled", false);
				answerD.attr("disabled", false);
				answerKey.attr("disabled", false);
				btn.attr("disabled", false);
			}
		});

		execute.fail(function() {
			bu.attr("disabled", false);
			area.attr("disabled", false);
			category.attr("disabled", false);
			pertanyaan.attr("disabled", false);
			if (turnImage.prop("checked")) {
				turnImage.attr("disabled", false);
				image.attr("disabled", false);
			}
			answerA.attr("disabled", false);
			answerB.attr("disabled", false);
			answerC.attr("disabled", false);
			answerD.attr("disabled", false);
			answerKey.attr("disabled", false);
			btn.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});
	
	$("#save-delete-exam").click(function() {
		const btn = $("#save-delete-exam");
		const txt = btn.attr("data-info");

		$("#modal-delete-exam").modal('hide');
		$(".air-badge").html(loadingBackdrop());
		
		const params = {
			'id': txt,
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		btn.attr("data-info", "");
		$(".info-delete-exam").text('');

		const url = baseUrl("/auth/api/v6/deltExam");

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
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});


		
	});
}

const resultExam = () => {
	let myChart = null
	let chartOpt = chartConfig();

	const listViewDetailExamResult = (arr) => {
		const myData = arr;
		const bu = $('[id^="detail-list-bu"]');
		const area = $('[id^="detail-list-area"]');
		const display1 = $("#modal-response-1");
		const display2 = $("#modal-response-2");
		const btnh1 = $("#btn-display-h1");
		const btnh2 = $("#btn-display-h2");
		const body = $("#modal-body-2");
		let sc = ``;

		const params = {
			'id': myData.attr('data'),
			'sid': myData.attr('sid'),
			'bu': bu.eq(myData.attr('find')).text(),
			'area': area.eq(myData.attr('find')).text(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printDetailExamResult");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.length; i++) {
					sc +=`
					<tr>
						<td>${(i+1)}</td>
						<td>${obj.result[i].name}</td>
						<td>${obj.result[i].score}</td>
						<td>${obj.result[i].bu}</td>
						<td>${obj.result[i].area}</td>
						<td>${obj.result[i].cat}</td>
						<td>${obj.result[i].desc}</td>
						<td>${obj.result[i].created}</td>
					</tr>
					`;
				}

				$('#dataTable-3').DataTable().destroy();
				body.html(sc);
				$('#dataTable-3').DataTable();
				display1.addClass("d-none");
				btnh1.addClass("d-none");
				display2.removeClass('d-none');
				btnh2.removeClass('d-none');
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	}

	const listViewExamStatic = (arr) => {
		const myData = arr;
		const bu = $('[id^="list-bu"]');
		const area = $('[id^="list-area"]');
		const modal = $("#modal-static-view");
		// const modal = $("#modal-exam-result");
		// const body = $("#modal-body");
		// let sc = ``;

		const params = {
			'id': myData.attr('data'),
			'bu': bu.eq(myData.attr('find')).text(),
			'area': area.eq(myData.attr('find')).text(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printExamStatic");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				let datasets = [];
				let date = [];
				for (let i = 0; i < obj.result.data.length; i++) {
					let score = [];
					let dates = [];
					for (let x = 0; x < obj.result.data[i].data.length; x++) {
						score[x] = obj.result.data[i].data[x].score;
						dates[x] = obj.result.data[i].data[x].created;
					}
					date = dates;
					datasets[i] = chartValue(obj.result.data[i].label, score);
				}

				chartOpt.options.plugins.title.text = obj.result.user.name+" - "+obj.result.user.id;
				chartOpt.data.datasets = datasets;
				chartOpt.data.labels = date;

				modal.attr("uid", myData.attr('data'));
				modal.attr("bu", bu.eq(myData.attr('find')).text());
				modal.attr("area", area.eq(myData.attr('find')).text());

				const ctx = $('#report-static')[0];
				ctx.height = 200;
				if (myChart === null) myChart = new Chart(ctx.getContext('2d'), chartOpt);
				if (myChart !== null) myChart.update('none');
				modal.modal('show');
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				// modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			// modal.modal('hide');
		});
	}

	const listViewExamResult = (arr) => {
		const myData = arr;
		const bu = $('[id^="list-bu"]');
		const area = $('[id^="list-area"]');
		const modal = $("#modal-exam-result");
		const body = $("#modal-body");
		let sc = ``;

		const params = {
			'id': myData.attr('data'),
			'bu': bu.eq(myData.attr('find')).text(),
			'area': area.eq(myData.attr('find')).text(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printExamResult");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.length; i++) {
					sc +=`
					<tr>
						<td>${(i+1)}</td>
						<td>${obj.result[i].name}</td>
						<td id="detail-list-bu" data="${obj.result[i].uid}">${obj.result[i].bu}</td>
						<td id="detail-list-area" data="${obj.result[i].uid}">${obj.result[i].area}</td>
						<td>${obj.result[i].created}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-center">
								<a href="#" id="list-view-detail-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" sid="${obj.result[i].sid}" class="m-1 badge badge-primary"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
								<a href="#" id="list-print-detail-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" sid="${obj.result[i].sid}" class="m-1 badge badge-dark"><span class="mr-2">Print</span><i class="fas fa-fw fa-print"></i></a>
								<a href="#" id="list-print-detail-exam-delete" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" sid="${obj.result[i].sid}" class="m-1 badge badge-danger"><span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i></a>
								<a href="#" id="list-view-detail-exam-detail" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" sid="${obj.result[i].sid}" class="m-1 badge badge-info"><span class="mr-2">Detail</span><i class="fas fa-fw fa-align-justify"></i></a>
							</div>
						</td>
					</tr>
					`;
				}

				$('#dataTable-modal').DataTable().destroy();
				body.html(sc);
				$('#dataTable-modal').DataTable();
				modal.modal('show');
				modal.attr("target", `${btoa(myData.attr('data'))}/${btoa(bu.eq(myData.attr('find')).text())}/${btoa(area.eq(myData.attr('find')).text())}/print`)
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	}

	const listResetExamResult = (arr) => {
		const myData = arr;
		const bu = $('[id^="list-bu"]');
		const area = $('[id^="list-area"]');
		const modal = $("#modal-exam-reset");
		const name = $("#target-reset");
		const btnModal = $("#btn-exam-reset");

		btnModal.attr("uid", myData.attr('data'));
		btnModal.attr("bu", bu.eq(myData.attr('find')).text());
		btnModal.attr("area", area.eq(myData.attr('find')).text());
		name.text(myData.attr('data'));
		modal.modal('show');
	}

	const listPrintDetailExamResult = (arr) => {
		const myData = arr;
		const bu = $('[id^="detail-list-bu"]');
		const area = $('[id^="detail-list-area"]');
		let path = btoa(myData.attr('data'))+"/"+btoa(myData.attr('sid'))+"/"+btoa(bu.eq(myData.attr('find')).text())+"/"+btoa(area.eq(myData.attr('find')).text())+"/1";
		
		window.open(baseUrl("/otj/print/"+path), '_blank');
	}

	const listPrintDetailExamDelete = (arr) => {
		const myData = arr;
		const modal = $("#modal-exam-delete");
		const last_modal = $("#modal-exam-result");
		const btnModal = $("#btn-exam-delete");

		$(".target-delete").text(myData.attr('data'));
		btnModal.attr('uid', myData.attr('data'));
		btnModal.attr('sid', myData.attr('sid'));
		last_modal.modal('hide');
		modal.modal('show');
	}

	const listViewDetailExamDetail = (arr) => {
		const myData = arr;
		const bu = $('[id^="detail-list-bu"]');
		const area = $('[id^="detail-list-area"]');
		let path = btoa(myData.attr('data'))+"/"+btoa(myData.attr('sid'))+"/"+btoa(bu.eq(myData.attr('find')).text())+"/"+btoa(area.eq(myData.attr('find')).text())+"/1";
		
		window.open(baseUrl("/start/review/"+path), '_blank');
	}

	$("#find-static").click(function () {
		const startDate = $("#start-date"); 	
		const endDate = $("#end-date");
		const modal = $("#modal-static-view");

		const params = {
			'uid': modal.attr('uid'),
			'bu': modal.attr('bu'),
			'area': modal.attr('area'),
			'start-date': startDate.val().trim(),
			'end-date': endDate.val().trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/findPrintExamStatic");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				let datasets = [];
				let date = [];
				for (let i = 0; i < obj.result.data.length; i++) {
					let score = [];
					let dates = [];
					for (let x = 0; x < obj.result.data[i].data.length; x++) {
						score[x] = obj.result.data[i].data[x].score;
						dates[x] = obj.result.data[i].data[x].created;
					}
					date = dates;
					datasets[i] = chartValue(obj.result.data[i].label, score);
				}

				chartOpt.options.plugins.title.text = obj.result.user.name+" - "+obj.result.user.id;
				chartOpt.data.datasets = datasets;
				chartOpt.data.labels = date;

				const ctx = $('#report-static')[0];
				ctx.height = 200;
				if (myChart === null) myChart = new Chart(ctx.getContext('2d'), chartOpt);
				if (myChart !== null) myChart.update('none');
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	});

	$("#btn-display-back").click(function() {
		const display1 = $("#modal-response-1");
		const display2 = $("#modal-response-2");
		const btnh1 = $("#btn-display-h1");
		const btnh2 = $("#btn-display-h2");

		display2.addClass('d-none');
		btnh2.addClass('d-none');
		display1.removeClass("d-none");
		btnh1.removeClass("d-none");
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

		const url = baseUrl("/auth/api/v6/manegeExamBu");

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

	$("#input-cat-area").change(function() {
		const bu = $("#input-cat-bu");
		const area = $("#input-cat-area");
		let dataTable = $('#dataTable').DataTable();
		let sc = ``;

		const params = {
			'bu': bu.val(),
			'area': area.val(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/sortExamResult");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.length; i++) {
					sc += `
					<tr>
						<td class="text-wrap align-middle text-center">${(i+1)}</td>
						<td>${obj.result[i].name}</td>
						<td id="list-bu" data="${obj.result[i].uid}">${obj.result[i].bu}</td>
						<td id="list-area" data="${obj.result[i].uid}">${obj.result[i].area}</td>
						<td class="text-wrap align-middle text-center">${obj.result[i].created}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-center">
							${(obj.result[i].acc == 1 ? `<a href="#" id="list-reset-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-danger"><span class="mr-2">Reset</span><i class="fas fa-fw fa-sync"></i></a>` : ``)}
							<a href="#" id="list-view-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-info"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
							<a href="#" id="list-view-exam-static" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-primary"><span class="mr-2">Static</span><i class="fas fa-fw fa-chart-bar"></i></a>
							</div>
						</td>
					</tr>
					`;
				}

				dataTable.destroy();
				$("#cos-quest").html(sc);
				$('#dataTable').DataTable()
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			area.attr("disabled", true);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-show-all-result").click(function() {
		let dataTable = $('#dataTable').DataTable();
		let sc = ``;

		const params = {
			'quest': "all",
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/sortAllExamResult");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {

				for (let i = 0; i < obj.result.length; i++) {
					sc += `
					<tr>
						<td class="text-wrap align-middle text-center">${(i+1)}</td>
						<td>${obj.result[i].name}</td>
						<td id="list-bu" data="${obj.result[i].uid}">${obj.result[i].bu}</td>
						<td id="list-area" data="${obj.result[i].uid}">${obj.result[i].area}</td>
						<td class="text-wrap align-middle text-center">${obj.result[i].created}</td>
						<td class="text-wrap align-middle text-center w-25">
							<div class="d-flex justify-content-center">
							${(obj.result[i].acc == 1 ? `<a href="#" id="list-reset-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-danger"><span class="mr-2">Reset</span><i class="fas fa-fw fa-sync"></i></a>` : ``)}
							<a href="#" id="list-view-exam-result" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-info"><span class="mr-2">Details</span><i class="fas fa-fw fa-eye"></i></a>
							<a href="#" id="list-view-exam-static" data-toggle="modal" find="${i}" data="${obj.result[i].uid}" class="m-1 badge badge-primary"><span class="mr-2">Static</span><i class="fas fa-fw fa-chart-bar"></i></a>
							</div>
						</td>
					</tr>
					`;
				}

				dataTable.destroy();
				$("#cos-quest").html(sc);
				$('#dataTable').DataTable()
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			area.attr("disabled", true);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-print-result").click(function() {
		const modal = $("#modal-exam-result");
		window.open(baseUrl("/otj/"+modal.attr("target")), '_blank');
	});

	$("#btn-pdf-result").click(function() {
		const modal = $("#modal-exam-result");
		window.open(baseUrl("/otj/pdf/"+modal.attr("target")), '_blank');
	});

	$("#btn-exam-delete").click(function () {
		const btn = $("#btn-exam-delete");
		const modal = $("#modal-exam-delete");

		modal.modal('hide');
	
		const params = {
			'id': btn.attr('uid'),
			'sid': btn.attr('sid')
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/deleteResultExam");

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
			}
			modal.modal('hide');
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
		
	});

	$("#btn-exam-reset").click(function() {
		const btn = $("#btn-exam-reset");
		const modal = $("#modal-exam-reset");

		modal.modal('hide');
	
		const params = {
			'id': btn.attr('uid'),
			'bu': btn.attr('bu'),
			'area': btn.attr('area'),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/resetResultExam");

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
			}
			modal.modal('hide');
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	});

	$(document).on('click', '[id^="list-view-exam-static"]', function () {
		const myData = $(this);
		listViewExamStatic(myData);
	});

	$(document).on('click', '[id^="list-view-exam-result"]', function () {
		const myData = $(this);
		listViewExamResult(myData);
	});

	$(document).on('click', '[id^="list-reset-exam-result"]', function () {
		const myData = $(this);
		listResetExamResult(myData);
	});

	$(document).on('click', '[id^="list-view-detail-exam-result"]', function () {
		const myData = $(this);
		listViewDetailExamResult(myData);
	});

	$(document).on('click', '[id^="list-print-detail-exam-result"]', function () {
		const myData = $(this);
		listPrintDetailExamResult(myData);
	});

	$(document).on('click', '[id^="list-print-detail-exam-delete"]', function () {
		const myData = $(this);
		listPrintDetailExamDelete(myData);
	});

	$(document).on('click', '[id^="list-view-detail-exam-detail"]', function () {
		const myData = $(this);
		listViewDetailExamDetail(myData);
	});

}

const examSettings = () => {
	CKEDITOR.replace('input-regulation')
	
	$("#input-processing-time, #input-minimum-value").keyup(function() {
		filterCode("#"+this.id, "#msg-"+this.id, true , 1, 3);
	});

	$("#input-processing-time, #input-minimum-value").keypress(function() {
		return allowNumberic(this);
	});

	$("#btn-save").click(function() {
		const title = $("#input-name-ujian");
		const maxTime = $("#input-processing-time");
		const minVal = $("#input-minimum-value");
		const regulation = $("#input-regulation");

		let allow = true;

		if (!filterInput("#"+title.attr("id"), "#msg-"+title.attr("id"))) allow = false;
		if (!filterCode("#"+maxTime.attr("id"), "#msg-"+maxTime.attr("id"))) allow = false;
		if (!filterCode("#"+minVal.attr("id"), "#msg-"+minVal.attr("id"))) allow = false;
		if (!filterCKEDITOR("#"+regulation.attr("id"), "#msg-"+regulation.attr("id"), CKEDITOR.instances['input-regulation'].getData())) allow = false;

		if (!allow) return false;
		$(".air-badge").html(loadingBackdrop());

		let params = {
			"title" : title.val().trim(),
			"time" : maxTime.val().trim(),
			"val" : minVal.val().trim(),
		}

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
			"regulation" : btoa(unescape(CKEDITOR.instances['input-regulation'].getData())),
		}
		
		const url = baseUrl("/auth/api/v6/examSettings");

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
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});
}

const userList = () => {

	$("#input-new-username").keyup(function() {
		filterCode("#"+this.id, "#msg-"+this.id, true ,8, 8);
	});

	$("#input-new-username").keypress(function() {
		return allowNumberic(this);
	});

	$("#input-new-name").keyup(function() {
		filterName("#"+this.id, "#msg-"+this.id, true ,8, 8);
	});

	$("#btn-add-new-user").click(function() {
		const username = $("#input-new-username"); 
		const name = $("#input-new-name"); 
		const password = $("#input-new-password"); 
		const departement = $("#input-new-departement"); 
		const phone = $("#input-new-phone"); 
		const address = $("#input-new-address"); 
		const btn = $("#btn-add-new-user"); 
		const modal = $("#modal-new-user");
		let allow = true;

		if (!filterCode("#"+username.attr("id"), "#msg-"+username.attr("id"))) allow = false;
		if (!filterName("#"+name.attr("id"), "#msg-"+name.attr("id"))) allow = false;
		if (!filterInput("#"+password.attr("id"), "#msg-"+password.attr("id"))) allow = false;
		if (!filterInput("#"+departement.attr("id"), "#msg-"+departement.attr("id"))) allow = false;
		if (!filterInput("#"+phone.attr("id"), "#msg-"+phone.attr("id"))) allow = false;
		if (!filterInput("#"+address.attr("id"), "#msg-"+address.attr("id"))) allow = false;

		if (!allow) return false;
		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());

		const params = {
			'username': username.val().trim(),
			'name': name.val().trim(),
			'password': password.val().trim(),
			'departement': departement.val().trim(),
			'phone': phone.val().trim(),
			'address': address.val().trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/addNewUser");

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
				button.attr("disabled", false);
			}
		});

		execute.fail(function() {
			button.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("#btn-save-edit-user").click(function() {
		const username = $("#input-edit-username"); 
		const name = $("#input-edit-name"); 
		const password = $("#input-edit-password"); 
		const departement = $("#input-edit-departement"); 
		const phone = $("#input-edit-phone"); 
		const address = $("#input-edit-address"); 
		const btn = $("#btn-save-edit-user"); 
		const modal = $("#modal-edit-user");
		let allow = true;

		if (!filterCode("#"+username.attr("id"), "#msg-"+username.attr("id"))) allow = false;
		if (!filterName("#"+name.attr("id"), "#msg-"+name.attr("id"))) allow = false;
		if (!filterInput("#"+password.attr("id"), "#msg-"+password.attr("id"))) allow = false;
		if (!filterInput("#"+departement.attr("id"), "#msg-"+departement.attr("id"))) allow = false;
		if (!filterInput("#"+phone.attr("id"), "#msg-"+phone.attr("id"))) allow = false;
		if (!filterInput("#"+address.attr("id"), "#msg-"+address.attr("id"))) allow = false;

		if (!allow) return false;
		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());

		const params = {
			'username': username.val().trim(),
			'name': name.val().trim(),
			'password': password.val().trim(),
			'departement': departement.val().trim(),
			'phone': phone.val().trim(),
			'address': address.val().trim(),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/editUser");

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
			}
		});

		execute.fail(function() {
			btn.attr("disabled", false);
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});

	$("[id^='btn-modal-edit-user").click(function() {
		const username = $("#input-edit-username"); 
		const name = $("#input-edit-name"); 
		const password = $("#input-edit-password"); 
		const departement = $("#input-edit-departement"); 
		const phone = $("#input-edit-phone"); 
		const address = $("#input-edit-address"); 
		const btn = $(this);
		const modal = $("#modal-edit-user");

		const params = {
			'id': btn.attr("target"),
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/printUser");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				modal.attr("target", btn.attr("target"));
				modal.modal('show');

				username.val(obj.result.id);
				name.val(obj.result.name);
				departement.val(obj.result.dept);
				phone.val(obj.result.phone);
				address.val(obj.result.address);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
				modal.modal('hide');
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
			modal.modal('hide');
		});
	});

	$("[id^='btn-modal-disable-user']").click(function() {
		const data = $(this);
		const modal = $("#modal-disable-user");
		const txt = $(".info-disable-user");
		const btnModal = $("#save-disable-user");

		btnModal.attr("data-info", data.attr("target"));
		txt.text(data.attr("target"));
		modal.modal('show');
	});

	$("[id^='btn-modal-enable-user']").click(function() {
		const data = $(this);
		const modal = $("#modal-enable-user");
		const txt = $(".info-enable-user");
		const btnModal = $("#save-enable-user");

		btnModal.attr("data-info", data.attr("target"));
		txt.text(data.attr("target"));
		modal.modal('show');
	});

	$("[id^='btn-modal-delete-user']").click(function() {
		const data = $(this);
		const modal = $("#modal-delete-user");
		const txt = $(".info-delete-user");
		const btnModal = $("#save-delete-user");

		btnModal.attr("data-info", data.attr("target"));
		txt.text(data.attr("target"));
		modal.modal('show');
	});

	$("#save-disable-user").click(function() {
		const modal = $("#modal-disable-user");
		const btn = $("#save-disable-user");
		const txt = btn.attr("data-info");

		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());
		__disableEnableExam(txt, 'setUser');

		btn.attr("data-info", "");
		$(".info-disable-user").text('');
	});

	$("#save-enable-user").click(function() {
		const modal = $("#modal-enable-user");
		const btn = $("#save-enable-user");
		const txt = btn.attr("data-info");

		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());
		__disableEnableExam(txt, 'setUser', "Y");

		btn.attr("data-info", "");
		$(".info-enable-user").text('');
	});

	$("#save-delete-user").click(function() {
		const modal = $("#modal-delete-user");
		const btn = $("#save-delete-user");
		const txt = btn.attr("data-info");

		modal.modal('hide');
		$(".air-badge").html(loadingBackdrop());
		__disableEnableExam(txt, 'deltUser', "Y");

		btn.attr("data-info", "");
		$(".info-delete-user").text('');
	});
}

const printResult = () => {
	$("#print-otj").click(function() {
		$(".train-type").text($("#train-type").val());
		$(".trainer").text($("#trainer").val());
		window.print();
	});
}

const graphic = () => {

	const filterInput = (method1, method2) => {
		let input = $(method1);
		let msg = $(method2);

		let filter = filterLength(method1, 1);

		if (filter !== true) {
			input.removeClass('is-valid');
			input.addClass('is-invalid');
			msg.text(filter);
			return false;
		} else {
			input.removeClass('is-invalid');
			input.addClass('is-valid');
			msg.text("");
			return true;
		}
	}
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

	$("#btn-load-users").click(function() {
		const modal = $("#modal-load-users");
		modal.modal("show");
	});

	$(document).on('click', '[id^="set-user"]', function () {
		const myData = $(this);
		const user = $("#u-"+myData.attr("target"));
		const modal = $("#modal-load-users");
		const input = $("#input-name");

		input.val(user.attr("is-val"));
		input.attr("data", user.attr("data"));
		modal.modal("hide");
	});

	$("#input-cat-bu").change(function() {
		const area = $("#input-cat-area");
		const cat = $("#input-cat-cat");
		let sc = `<option disabled selected>Choose a Area</option>`;
		let scCat = `<option disabled selected>Choose a Category</option>`;
		const params = {
			'bu': this.value,
		};

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}

		const url = baseUrl("/auth/api/v6/manegeExamBu");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				for (let i = 0; i < obj.result.length; i++) {
					sc += `<option value="${obj.result[i].area}">${obj.result[i].area}</option>`;
				}

				area.html(sc);
				area.attr("disabled", false);
				cat.html(scCat);
				cat.attr("disabled", true);
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

	$("#btn-show-graphic").click(function() {
		const name = $("#input-name"); 
		const bu = $("#input-cat-bu"); 
		const area = $("#input-cat-area"); 
		const btn = $("#btn-show-graphic");
		let allow = true;

		if (!filterInput("#"+name.attr("id"), "#msg-"+name.attr("id"))) allow = false;
		if (!filterSelect("#"+bu.attr("id"), "#msg-"+bu.attr("id"))) allow = false;
		if (!filterSelect("#"+area.attr("id"), "#msg-"+area.attr("id"))) allow = false;

		if (!allow) return false;
		// $(".air-badge").html(loadingBackdrop());
		// name.attr("disabled", true);
		// bu.attr("disabled", true);
		// area.attr("disabled", true);
		// btn.attr("disabled", true);

		let params = {
			"user" : name.attr("data").trim(),
			"bu" : bu.val().trim(),
			"area" : area.val().trim(),
		}

		const executePost = {
			'data' : JEncrypt(JSON.stringify(params)),
		}
		
		const url = baseUrl("/auth/api/v6/userDiagram");

		const execute = postField(url, 'POST', executePost, false);

		execute.done(function(result) {
			let obj = JSON.parse(JSON.stringify(result));

			if (obj.code == 200) {
				// $(".air-badge").html(airBadge(obj.msg , 'success'));
				// setTimeout(function() {
				// 	window.location = window.location.href;
				// }, 5000);
			} else {
				$(".air-badge").html(airBadge(obj.msg , 'danger'));
			}
		});

		execute.fail(function() {
			$(".air-badge").html(airBadge("Request Time Out. Please Try!" , 'danger'));
		});
	});
}

let main = (function() {
	let isOn = $(".main-js").attr("my-js") || false;
	if (isOn == "manage-exam") manageExam();
	if (isOn == "result-exam") resultExam();
	if (isOn == "graphic") graphic();
	if (isOn == "exam-settings") examSettings();
	if (isOn == "user-list") userList();
	if (isOn == "print-result") printResult();
})();