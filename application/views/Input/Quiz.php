<?php
if ($length < 10) {
	$width = 4;
} elseif ($length < 20) {
	$width = 6;
} else {
	$width = 8;
}?>

<div class="row">
	<div class="col-sm-<?php echo (12-$width)/2;?>"></div>
	<div class="col-sm-<?php echo $width;?>"><?php

	if (count($options) > 3) {?>

		<select name="answer" class="form-control"><?php

		foreach ($options as $key => $value) {?>
			
			<option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php

		}?>

		</select><?php

	} else {

		foreach ($options as $key => $value) {?>

			<div class="radio">
				<label>
					<input type="radio" name="answer" value="<?php echo $key; ?>"><?php echo $value; ?>
				</label>
			</div><?php

		}

	}?>

	</div>
	<div class="col-sm-<?php echo (12-$width)/2;?>"></div>
</div>