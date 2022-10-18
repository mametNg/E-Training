<?php if (isset($this->allowFile) && $this->allowFile): ?>
	<!DOCTYPE html>
	<html lang="en"><head>

		<meta charset="UTF-8">
		<title>404 page</title>


		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arvo">

		<style>
			.page_404{ padding:40px 0; background:#fff; font-family: 'Arvo', serif;
			}

			.page_404  img{ width:100%;}

			.four_zero_four_bg{

				background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
				height: 400px;
				background-position: center;
			}


			.four_zero_four_bg h1{
				font-size:80px;
			}

			.four_zero_four_bg h3{
				font-size:80px;
			}

			.link_404{			 
				color: #fff!important;
				padding: 10px 20px;
				background: #39ac31;
				margin: 20px 0;
				display: inline-block;}
				.contant_box_404{ margin-top:-50px;}
			</style>

			<script>
				window.console = window.console || function(t) {};
			</script>



			<script>
				if (document.location.search.match(/type=embed/gi)) {
					window.parent.postMessage("resize", "*");
				}
			</script>


		</head>

		<body translate="no">
			<section class="page_404">
				<div class="container">
					<div class="row">	
						<div class="col-sm-12 ">
							<div class="col-sm-10 col-sm-offset-1  text-center">
								<div class="four_zero_four_bg">
									<h1 class="text-center ">404</h1>


								</div>

								<div class="contant_box_404">
									<h3 class="h2">
										Look like you're lost
									</h3>

									<p>the <strong><?= $this->e($data['error-file']) ?></strong> page you are looking for is not available!</p>

									<a href="" class="link_404">Go to Home</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</body>
	</html>
<?php endif; ?>