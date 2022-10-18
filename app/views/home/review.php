<?php if ($this->allowFile): ?>
	<!-- Content Row -->
	<div class="row main-js" my-js="review">
		<div class="col-lg-3" id="number-quest">
			<div class="card shadow mb-4">

				<div class="card-body">

					<div data-spy="scroll" data-target="#list-example" data-offset="0" class="scrollspy-example">
						<?php for ($i=0; $i < count($data['quest']); $i++) : ?>
							<button type="button" id="scroll-page" target="quest-<?= $i ?>" class="btn <?= ($data['quest'][$i]['answer_key'] == strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) ? "btn-primary" : "btn-dark")?> btn-sm text-center m-1 mr-n1"><?= substr($this->def, 0, (strlen($this->def)-strlen(($i+1)))).($i+1) ?></button>
						<?php endfor; ?>
					</div>

					<hr class="my-2">

					<div class="d-flex justify-content-between mb-2 mx-n2">
						<div class="col-lg">
							<button type="button" class="btn btn-dark btn-icon-split btn-sm btn-block">
								<span class="icon text-white-50 mr-auto">
									<i class="fas fa-times"></i>
								</span>
								<span class="text quest-start"><?= $data['score']['false']; ?></span>
							</button type="button">
						</div>
						<div class="col-lg">
							<button type="button" class="btn btn-primary btn-icon-split btn-sm btn-block">
								<span class="icon text-white-50 mr-auto">
									<i class="fas fa-check"></i>
								</span>
								<span class="text quest-finish"><?= $data['score']['true']; ?></span>
							</button type="button">
						</div>
					</div>

					<div class="d-flex justify-content-between">
						<span class="countdown">Score : </span>
					</div>

					<hr class="my-2">

					<div class="row">
						<div class="col-md-6">
							<table>
								<tr>
									<td>Name</td>
									<td>:</td>
									<td><?= $this->e(ucwords($data['user']['name'])) ?></td>
								</tr>
								<tr>
									<td>BU</td>
									<td>:</td>
									<td><?= $this->e($data['session']['bu']) ?></td>
								</tr>

								<tr>
									<td>AREA</td>
									<td>:</td>
									<td><?= $this->e($data['session']['area']) ?></td>
								</tr>
							</table>
						</div>
						<div class="col-md-6 d-lg-flex d-none justify-content-center align-items-center border-left">
							<div class="text-center">
								<small class="mb-n2">Trial to</small>
								<h1 class="mt-n2">#<?= $this->e($data['session']['try']+1) ?></h1>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="col-lg-9">
			<div class="card shadow mb-4">
				<div class="card-body">
					<ol>
						<?php for ($i=0; $i < count($data['quest']); $i++) : ?>

							<li id="quest-<?= $i ?>" class="scroll-page" data-toggle="tooltip" data-placement="top" title="<?= $data['quest'][$i]['cat'] ?>">
								<span class="question-<?= $i ?> <?= ($data['quest'][$i]['answer_key'] == strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) ? "text-success" : "text-danger")?> font-weight-bold"><?= $data['quest'][$i]['quest'] ?></span>
								<div>
									<?php if (strlen($this->e($data['quest'][$i]['image']))) : ?>
									<img data-toggle="modal" data-target="#modal-image" src="<?= $this->base_url() ?>/assets/img/exam/<?= $this->e($data['quest'][$i]['image']) ?>" class="img-fluid wm" style="max-height: 200px;" alt="<?= $this->e($data['quest'][$i]['quest']) ?>">
									<?php endif; ?>
								</div>
								<div class="ml-2 text-dark">
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['qid'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['qid'] ?>" id="quest-<?= $data['quest'][$i]['qid'] ?>-a" value="A" <?= (isset($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['qid'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) == "A" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label<?= ($data['quest'][$i]['answer_key'] == "A" ? " text-primary" : "")?>" for="quest-<?= $data['quest'][$i]['qid'] ?>-a"><?= $data['quest'][$i]['quest_a'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['qid'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['qid'] ?>" id="quest-<?= $data['quest'][$i]['qid'] ?>-b" value="B" <?= (isset($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['qid'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) == "B" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label<?= ($data['quest'][$i]['answer_key'] == "B" ? " text-primary" : "")?>" for="quest-<?= $data['quest'][$i]['qid'] ?>-b"><?= $data['quest'][$i]['quest_b'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['qid'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['qid'] ?>" id="quest-<?= $data['quest'][$i]['qid'] ?>-c" value="C" <?= (isset($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['qid'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) == "C" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label<?= ($data['quest'][$i]['answer_key'] == "C" ? " text-primary" : "")?>" for="quest-<?= $data['quest'][$i]['qid'] ?>-c"><?= $data['quest'][$i]['quest_c'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['qid'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['qid'] ?>" id="quest-<?= $data['quest'][$i]['qid'] ?>-d" value="D" <?= (isset($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['qid'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer']) == "D" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label<?= ($data['quest'][$i]['answer_key'] == "D" ? " text-primary" : "")?>" for="quest-<?= $data['quest'][$i]['qid'] ?>-d"><?= $data['quest'][$i]['quest_d'] ?></label>
									</div>
								</div>
							</li>
						<?php endfor; ?>
					</ol>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade align-items-center" id="modal-image" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content bg-transparent border-0">
				<div class="modal-body p-0 d-flex justify-content-center">
					<img src="" id="image-modal" class="img-thumbnail">
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>


