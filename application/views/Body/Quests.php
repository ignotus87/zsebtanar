<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="text-center">
			<a href="#" class="btn btn-class openall">
				<img src="<?php echo base_url();?>assets/images/eye_open.png" alt="logo" width="20">
			</a>
			<a href="#" class="btn btn-class closeall">
				<img src="<?php echo base_url();?>assets/images/eye_close.png" alt="logo" width="20">
			</a>
		</div><br /><br /><br />
		<div class="panel-group" id="accordion"><?php

		foreach ($quests as $quest) {?>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title"><?php

						if ($quest['complete']) {?>

							<img src="<?php echo base_url().'assets/images/tick.png';?>" alt="star" width="20px">&nbsp;<?php

						} else {?>

							<img src="<?php echo base_url().'assets/images/tick_empty.png';?>" alt="star" width="20px">&nbsp;<?php

						}?>


						<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $quest['id'];?>">
							<?php echo $quest['name']; ?>
						</a>
					</h4>
				</div><?php

				$class = ($questID == $quest['id'] ? 'in' : '');?>

				<div id="collapse<?php echo $quest['id'];?>" class="panel-collapse collapse <?php echo $class;?>">
					<div class="panel-body">
						<ul><?php

						foreach ($quest['exercises'] as $exercise) {?>

							<li>
								<a href="<?php echo base_url().'application/setgoal/exercise/'.$exercise['id'];?>">
									<?php echo $exercise['name'];?>
								</a>&nbsp;<?php

								foreach ($exercise['levels'] as $level) {?>

									<img src="<?php echo base_url().'assets/images/star'.$level.'.png';?>" alt="star" width="15px"><?php

								}?>

							</li><?php


						}?>

						</ul><?php

						if (!$quest['complete']) {?>

						<div class="text-center">
							<a class="btn btn-primary" href="<?php echo base_url().'application/setgoal/quest/'.$quest['id'];?>">
								Mehet
							</a>
						</div><?php

						} else {?>

						<div class="text-center">
							<a class="btn btn-warning" href="<?php echo base_url().'application/clearresults/'.$subtopicID.'/'.$quest['id'];?>">
								Újrakezd
							</a>
						</div><?php

						}

						if (count($quest['links']) > 0) {

							if (count($quest['links']) == 1) {?>

								<br />Túl nehéz? Először nézd meg ezt:<br /><?php

							} else {?>

								<br />Túl nehéz? Először nézd meg ezeket:<br /><?php

							}?>

							<div class="text-center"><?php

							foreach ($quest['links'] as $link) {?>

								<a class="btn btn-default" href="<?php echo base_url().'view/subtopic/'.$link['subtopicID'].'/'.$link['questID'];?>"><?php

									if ($link['complete']) {?>

										<img src="<?php echo base_url().'assets/images/tick_grey.png';?>" alt="star" width="20px">&nbsp;<?php

									}

									echo $link['name'];?>

								</a>&nbsp;<?php

							}?>

							</div><?php

						}?>

					</div>
				</div>
			</div><?php

		}?>

		</div>
	</div>
	<div class="col-md-3"></div>
</div>