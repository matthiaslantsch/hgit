<?php foreach($activities as $activity): ?>
	<div class="row">
		<h5><?=$activity->user->name." ".$activity->timestamp?></h5>
		<p><?=$activity->content?></p> 
	</div>
	<hr>
<?php endforeach; ?>