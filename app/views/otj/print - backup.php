<?php if ($this->allowFile): ?>

	<!-- 2480 x 3508  -->
	
	<div class="container-fluid" style="overflow: hidden; position: relative; background-color: white; width: 909px;">
		<div class="row main-js" my-js="print-result" data="">
			<div class="col-lg-12">
				<div class="bg-white">
					<div class="row mt-3 mx-n3 d-flex">
						<div class="col-2">
							<img src="<?= $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")) ?>" class="img-fluid text-center">
						</div>
						<div class="col-7 text-center">
							<h1 class="font-weight-bolder text-center">On-the-Job Training Log</h1>
						</div>
						<div class="col-3">
							<p class="text-right">No Doc : <?= $this->e($data['result'][0]['sid']) ?></p>
						</div>
					</div>

					<div class="row">
						<div class="col-9">
							<table class="ml-2 mt-4 mb-3">
								<tr>
									<td class="h6 text-primary">ERN</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['result'][0]['id']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Trainee Name</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['result'][0]['name']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Business Unit </td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e($data['result'][0]['bu']) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Joint Date </td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6"><?= $this->e(date("d/m/Y", $data['result'][0]['join'])) ?></td>
								</tr>
								<tr>
									<td class="h6 text-primary">Training Type</td>
									<td class="h6 px-3 py-2">:</td>
									<td class="h6">New Staff</td>
								</tr>
							</table>
						</div>
						<div class="col-3 mt-auto">
							<p class="small text-right mb-0"><?= date("l, F d, Y") ?></p>
							<p class="small text-right"><?= date("h:i:s A") ?></p>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<table class="table table-borderless mb-4">
								<thead class="thead-primary">
									<tr>
										<th scope="col">Category</th>
										<th scope="col">Qualification Area</th>
										<th scope="col">Score</th>
										<th scope="col">Date</th>
										<th scope="col">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($data['result'] as $result) : ?>
										<tr class="border-bottom border-primary">
											<td><?= $this->e($result['cat']) ?></td>
											<td><?= $this->e($result['area']) ?></td>
											<td><?= $this->e($result['score']) ?></td>
											<td><?= $this->e(date("d/m/Y", $result['created'])) ?></td>
											<td><?= $this->e($result['desc']) ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
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
						<div class="">
							<table class="table table-sm">
								<tr>
									<td>Comment by Trainer (if necessary) </td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3">PASS</td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3">&nbsp;</td>
									<td></td>
								</tr>
								<tr>
									<td class="pl-3">&nbsp;</td>
									<td></td>
								</tr>
								<tr class="border-bottom">
									<td class="pl-3">&nbsp;</td>
									<td></td>
								</tr>
							</table>
						</div>
						<div class="">
							<p class="text-right font-weight-bolder mb-1">Karawang, <?= date("d M Y") ?></p>
							<table class="table table-sm table-bordered">
								<tr>
									<td class="px-4">Assessed by</td>
									<td class="px-4">Checked by</td>
								</tr>
								<tr class="text-center">
									<td class="align-bottom">
										<div class="pt-4">
											<div class="py-4"></div>
											<span>Nunik K</span>
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