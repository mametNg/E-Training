<?php if ($this->allowFile): ?>

	<!-- 2480 x 3508  -->
	
	<div class="container-fluid mt-5" style="overflow: hidden; position: relative; background-color: white; width: 909px;">
		<div class="row main-js" my-js="print-result" data="">
			<div class="col-lg-12">
				<div class="bg-white">
					<div class="row mt-3 mx-n3">
						<div class="col-2">
							<img src="<?= $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")) ?>" class="img-fluid text-center">
						</div>
						<div class="col-7 text-center">
							<h1 class="font-weight-bolder text-center">On-the-Job Training Log</h1>
							<h5 class="font-weight-bolder text-center mb-0 pb-0">(Theory Test)</h5>
						</div>
						<div class="col-3">
							<p class="text-right mb-0">No Doc : G02K - 031A</p>
							<p class="text-right mt-0">Rev. 02</p>
							<!-- <?= $this->e($data['result'][0][0]['sid']) ?> -->
						</div>
					</div>

					<div class="row">
						<div class="col-9">
							<table class="ml-2 mt-4 mb-3 text-dark">
								<tr>
									<td class="h6 text-primary">ERN</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['member']['id']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Trainee Name</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['member']['name']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Business Unit </td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['result']['bu']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Joint Date </td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['member']['join_date']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Training Type</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6">
										<span class="is-print train-type"></span>
										<div class="form-group no-print">
											<select class="form-control" id="train-type">
												<option value="New" selected>New</option>
												<option value="Requal">Requal</option>
												<option value="Disqual">Disqual</option>
												<option value="Cross">Cross</option>
												<option value="Refresh">Refresh</option>
											</select>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-3 mt-auto">
							<button type="button" id="print-otj" data="1" class="btn btn-dark btn-icon-split btn-sm mb-4 no-print">
								<span class="icon text-white-50 mr-auto">
									<i class="fas fa-print"></i>
								</span>
								<span class="text">Print</span>
							</button type="button">

							<p class="d-none small text-right mb-0"><?= date("l, F d, Y") ?></p>
							<p class="d-none small text-right"><?= date("h:i:s A") ?></p>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<table class="table table-borderless mb-4 text-dark">
								<thead class="thead-primary">
									<tr>
										<th scope="col">Category</th>
										<th scope="col">Qualification Area</th>
										<th scope="col">Test</th>
										<th scope="col">Score</th>
										<th scope="col">Date</th>
										<th scope="col">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($data['result']['cat'] as $cat => $results) : ?>
									<?php foreach ($results as $key2 => $result) : ?>

										<?php if ($result['score'] >= $data['regulation']['min_val']): ?>
											<tr class="border-bottom border-primary text-black-100">
												<td><?= $this->e($cat) ?></td>
												<td><?=  $this->e($data['result']['area']) ?></td>
												<td><?= ($key2) ?></td>
												<td><?= $this->e($result['score']) ?></td>
												<td><?= $this->e(date("d/m/Y", $result['created'])) ?></td>
												<td class="text-<?= $this->e($result['clr']) ?> font-weight-bolder"><?= $this->e($result['desc']) ?></td>
											</tr>
											<?php break; else : ?>
											<tr class="border-bottom border-primary text-black-100">
												<td><?= $this->e($cat) ?></td>
												<td><?= $this->e($data['result']['area']) ?></td>
												<td><?= ($key2) ?></td>
												<td><?= $this->e($result['score']) ?></td>
												<td><?= $this->e(date("d/m/Y", $result['created'])) ?></td>
												<td class="text-<?= $this->e($result['clr']) ?> font-weight-bolder"><?= $this->e($result['desc']) ?></td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row d-none">
						<div class="col-12">
							<table>
								<tr class="small">
									<td class="align-top">Note</td>
									<td class="align-top px-2">:</td>
									<td>Time of training follow condition of operator / opr skill plan production achievement Time of practical training to be monitoring by Leader (evidence by Blue print each station)</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="row justify-content-between mt-4 mx-1">
						<div class="col-5">
							<table class="table table-sm">
								<tr>
									<td>Comment by Trainer (if necessary) </td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3" contenteditable="true">&nbsp;</td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3" contenteditable="true">&nbsp;</td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3" contenteditable="true">&nbsp;</td>
									<td></td>
								</tr>
								<tr class="border-bottom">
									<td class="pl-3" contenteditable="true">&nbsp;</td>
									<td></td>
								</tr>
							</table>
						</div>
						<div class="col-5">
							<p class="text-right font-weight-bolder mb-1">Karawang, <?= date("d M Y") ?></p>
							<table class="table table-sm table-bordered">
								<tr class="text-center">
									<td class="px-4">Assessed by</td>
									<td class="px-4">Checked by</td>
								</tr>
								<tr class="text-center">
									<td class="align-bottom">
										<div class="pt-4">
											<div class="py-4"></div>
											<span class="is-print trainer">Nunik K</span>
											<div class="form-group mb-0">
												<select class="form-control mb-0 no-print" id="trainer">
													<option value="Lisa Agustin" selected>Lisa Agustin</option>
													<option value="Suparno">Suparno</option>
													<option value="M. Ashidiq">M. Ashidiq</option>
													<option value="Nunik K">Nunik K</option>
												</select>
											</div>
										</div>
									</td>
									<td class="align-bottom">
										<div class="pt-4">
											<div class="py-4"></div>
											<span>Lisa Agustin</span>
										</div>
									</td>
								</tr>
								<tr class="text-center">
									<td>Trainer</td>
									<td>Leader</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>