<?php if ($this->allowFile): ?>
	<!-- Content Row -->
	<div class="row main-js" my-js="quest">
		<div class="col-lg-3" id="number-quest">
			<div class="card shadow mb-4">

				<div class="card-body">

					<div data-spy="scroll" data-target="#list-example" data-offset="0" class="scrollspy-example">
						<?php for ($i=0; $i < count($data['quest']); $i++) : ?>
							<button type="button" id="scroll-page" target="quest-<?= $i ?>" class="btn <?= (isset($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]) && $data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['qid'] == $data['quest'][$i]['id'] ? "btn-primary":"btn-secondary") ?> btn-sm text-center m-1 mr-n1"><?= substr($this->def, 0, (strlen($this->def)-strlen(($i+1)))).($i+1) ?></button>



						<?php endfor; ?>
					</div>

					<hr class="my-2">

					<div class="d-flex justify-content-between mb-2 mx-n2">
						<div class="col-lg">
							<button type="button" class="btn btn-dark btn-icon-split btn-sm btn-block">
								<span class="icon text-white-50 mr-auto">
									<i class="fas fa-times"></i>
								</span>
								<span class="text quest-start"><?= (count($data['quest']) - count($data['load'])) ?></span>
							</button type="button">
						</div>
						<div class="col-lg">
							<button type="button" class="btn btn-primary btn-icon-split btn-sm btn-block">
								<span class="icon text-white-50 mr-auto">
									<i class="fas fa-check"></i>
								</span>
								<span class="text quest-finish"><?= count($data['load']) ?></span>
							</button type="button">
						</div>
					</div>

					<div class="d-flex justify-content-between">
						<span class="countdown-timer" data="<?= date("F d, Y h:i:s a", $this->e($_SESSION['quest']['endtime'])) ?>">Time : <?= (($_SESSION['quest']['endtime']-$_SESSION['quest']['starttime'])/60) ?>m</span>
						<!-- <span class="countdown-timer" data="<?= date("F d, Y h:i:s a", (time() + 10)) ?>">Time : <?= (($_SESSION['quest']['endtime']-$_SESSION['quest']['starttime'])/60) ?>m</span> -->
						<span class="countdown">Timeout : </span>
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
									<td><?= $this->e($_SESSION['quest']['bu']) ?></td>
								</tr>

								<tr>
									<td>AREA</td>
									<td>:</td>
									<td><?= $this->e($_SESSION['quest']['area']) ?></td>
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
								<span class="question-<?= $i ?> text-dark font-weight-bold"><?= $data['quest'][$i]['quest'] ?></span>
								<div>
									<?php if (strlen($this->e($data['quest'][$i]['image']))) : ?>
									<img data-toggle="modal" data-target="#modal-image" src="<?= $this->base_url() ?>/assets/img/exam/<?= $this->e($data['quest'][$i]['image']) ?>" class="img-fluid wm" style="max-height: 200px;" alt="<?= $this->e($data['quest'][$i]['quest']) ?>">
									<?php endif; ?>
								</div>
								<div class="ml-2 text-dark">
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['id'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['id'] ?>" id="quest-<?= $data['quest'][$i]['id'] ?>-a" value="A" <?= (isset($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]) && $data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['qid'] == $data['quest'][$i]['id'] && strtoupper($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['answer']) == "A" ? "checked":"") ?> <?= (isset($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['id'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['answer']) == "A" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label" for="quest-<?= $data['quest'][$i]['id'] ?>-a"><?= $data['quest'][$i]['quest_a'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['id'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['id'] ?>" id="quest-<?= $data['quest'][$i]['id'] ?>-b" value="B" <?= (isset($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]) && $data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['qid'] == $data['quest'][$i]['id'] && strtoupper($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['answer']) == "B" ? "checked":"") ?> <?= (isset($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['id'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['answer']) == "B" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label" for="quest-<?= $data['quest'][$i]['id'] ?>-b"><?= $data['quest'][$i]['quest_b'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['id'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['id'] ?>" id="quest-<?= $data['quest'][$i]['id'] ?>-c" value="C" <?= (isset($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]) && $data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['qid'] == $data['quest'][$i]['id'] && strtoupper($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['answer']) == "C" ? "checked":"") ?> <?= (isset($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['id'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['answer']) == "C" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label" for="quest-<?= $data['quest'][$i]['id'] ?>-c"><?= $data['quest'][$i]['quest_c'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input input-pg pg-<?= $i ?>" data="<?= $data['quest'][$i]['id'] ?>" cat="<?= $data['quest'][$i]['cat'] ?>" target="quest-<?= $i ?>" type="radio" name="quest-<?= $data['quest'][$i]['id'] ?>" id="quest-<?= $data['quest'][$i]['id'] ?>-d" value="D" <?= (isset($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]) && $data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['qid'] == $data['quest'][$i]['id'] && strtoupper($data['load'][array_search($data['quest'][$i]['id'] , array_column($data['load'], 'qid'))]['answer']) == "D" ? "checked":"") ?> <?= (isset($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]) && $data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['qid'] == $data['quest'][$i]['id'] ? (strtoupper($data['finish'][array_search($data['quest'][$i]['id'] , array_column($data['finish'], 'qid'))]['answer']) == "D" ? "checked disabled":"disabled") :"") ?>>
										<label id="label-input-<?= $i ?>" class="form-check-label" for="quest-<?= $data['quest'][$i]['id'] ?>-d"><?= $data['quest'][$i]['quest_d'] ?></label>
									</div>
								</div>
							</li>
						<?php endfor; ?>
					</ol>
					<hr class="sidebar-divider">
					<div class="form-row d-flex justify-content-center">
						<div class="form-group col-md-3">
							<button type="button" class="btn btn-primary btn-block" id="save-quest">Submit</button>
						</div>
					</div>
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

	<!-- Modal Delete exam -->
	<div class="modal modal-secondary fade" id="modal-collect" tabindex="-1" role="dialog" aria-labelledby="modal-collect" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-warning">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to collect now!</h5>
						<p class="text-sm text-sm">All answers that have been collected cannot be changed.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="btn-collect" data-info="" data-role class="btn btn-warning">Submit Now</button>
						</div>
						<div class="m-2">
							<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Delete exam -->
	<div class="modal modal-secondary fade" id="modal-timeout" tabindex="-1" role="dialog" aria-labelledby="modal-timeout" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center my-2">
						<div class="icon text-danger">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">TIMEOUT!</h5>
						<p class="text-sm text-sm">You will be redirected soon.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>


