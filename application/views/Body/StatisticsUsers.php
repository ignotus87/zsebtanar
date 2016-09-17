<div class="container">
    <h2>Felhasználók</h2>
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Idő</th>
                <th class="text-center">Feladatok</th>
                <th class="text-center">Elért szint</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody><?php

            if (count($users) > 0) {
            	foreach ($users as $user) {?>
    	            <tr>
    	                <td class="text-center"><?php echo $user['id'];?></td>
    	                <td class="text-center"><?php echo $user['time'];?></td>
                        <td class="text-center"><?php echo count($user['exercises']);?></td>
    	                <td class="text-center"><?php echo count($user['max_level']);?></td>
    	                <td><a class="btn btn-primary" href="<?php echo base_url();?>/view/statistics/<?php echo $user['id'];?>">Részletek
    	                	&nbsp;<span class="glyphicon glyphicon-chevron-right"></span></a></td>
    	            </tr><?php

            	}
            }?>

        </tbody>
    </table>
</div>
