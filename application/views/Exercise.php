<div class="row">
	<div class="col-md-4"></div>
	<div class="col-md-4">
		<div class="row">
			<div class="col-md-1"><?php

			if ($youtube) {?>

				<a href="http://www.youtube.com/watch?v=<?php echo $youtube; ?>">
					<img src="<?php echo base_url();?>/assets/images/logo_small.png" alt="logo" width="20">
				</a><?php

			}?>

			</div>
			<div class="col-md-10"><?php

			echo $question;?>

			</div>
			<div class="col-md-1"></div>
		</div>
		<div class="row exercise_input">

			<div class="col-sm-12 text-center">
				<form id="exercise" autocomplete="off"><?php

				if ($type == 'int') {

					if (is_array($options)) {

						if (count($options) > 3) {?>

							<select name="answer" class="form-control" id="sel<?php echo $ex_no; ?>"><?php

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
						}

					} elseif ($options == '') {?>

						<table align="center" class="answer_fraction">
							<tbody>
								<tr>
									<td align="center">
										<input type="text" align="center" class="form-control smallInput" data-autosize-input='{ "space": 20 }' name="answer">
									</td>
								</tr>
							</tbody>
						</table>
						<?php

					}

				} elseif ($type == 'multi') {

					foreach ($options as $key => $value) {?>
						
						<input id="input<?php echo $key;?>" type="checkbox" name="answer" value="<?php echo $key; ?>">&nbsp;<?php echo $value; ?><br />
						<?php
					}

				}?>

				<input type="hidden" name="type" value="<?php echo $type;?>">
				<input type="hidden" name="correct" value="<?php echo json_encode($correct);?>">
				<input type="hidden" name="solution" value="<?php echo $solution;?>">
				
				<br />
				<p id="message"></p>
				<div id="button">
					<button class="btn btn-primary" onclick="checkSolution()">
						Mehet
					</button>
				</div>
			
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-4"></div>
</div>

<script>

	// Check solution
	function checkSolution() {
		var queryString = $("form").serializeArray();
		event.preventDefault();
		console.log(JSON.stringify(queryString));
		
	    $.ajax({
	        type: "GET",
	        url: "<?php echo base_url();?>application/checkanswer",
	        data: {
	            answer: JSON.stringify(queryString)
	        },
	        dataType: "json",
        	success: function(data) {
        		document.getElementById("message").innerHTML = '';
        		
        		// Exercise not finished
        		if (data['status'] == 'NOT_DONE') {
        			document.getElementById("message").innerHTML = data['message'];
        			return;
        		}

				// Disable buttons
				var radios = document.forms["exercise"]["answer"];

				if (Array.isArray(radios)) {
					for (var i=0, iLen=radios.length; i<iLen; i++) {
						radios[i].disabled = true;
					}
				} else {
					radios.disabled = true;
				}
        		
        		// Exercise finished
        		switch (data['status']) {
        			case 'CORRECT':
        				document.getElementById("message").innerHTML = '<div class="alert alert-success"><strong><span class=\"glyphicon glyphicon-ok\"></span></strong>&nbsp;&nbsp;' + data['message'] + '</div>';
        				document.getElementById("button").innerHTML = "<a class=\"btn btn-primary\" href=\"<?php echo base_url();?>view/exercise/<?php echo $id;?>/<?php echo $level+1;?>\">Tovább</button>";
        				break;
        			case 'WRONG':
        				document.getElementById("message").innerHTML = '<div class="alert alert-danger"><strong><span class=\"glyphicon glyphicon-remove\"></span></strong>&nbsp;&nbsp;' + data['message'] + '</div>';
        				MathJax.Hub.Queue(["Typeset",MathJax.Hub,"message"]);
        				if (data['submessages'].length > 0) {
        					for (var i = data['submessages'].length - 1; i >= 0; i--) {
        						var submessage = data['submessages'][i];
        						if (submessage == 'CORRECT') {
        							$('#input'+i).before('<span class=\"glyphicon glyphicon-ok green\"></span>&nbsp;');
        						} else {
        							$('#input'+i).before('<span class=\"glyphicon glyphicon-remove red\"></span>&nbsp;');
        						}
        					};
        				}
        				document.getElementById("button").innerHTML = "<a class=\"btn btn-primary\" href=\"<?php echo base_url();?>view/exercise/<?php echo $id;?>/<?php echo $level;?>\">Újra</button>";
        				break;
        		}
	        }
	    });
    }

	// Reload page
	function reloadPage() {
	    location.reload();
	}
</script>