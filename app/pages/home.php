<div class="row">
	<h4>Aktuelle Angebote</h4>
	<h5>Die besten Angebote aus der Schweiz, Deutschland und Österreich</h5>
</div>
<div class="row">
<?php
	include 'php/read.php';
	$events = getEvents();
	foreach ($events as $event) 
	{
		?>
		<div class="col s4 m4 l4">
			<div class="card event-home-card">
				<div class="card-image waves-effect waves-block waves-light">
					<img class="activator" <?php echo "src='img/".$event["Image"]."'" ?>>
				</div>
				<div class="card-content">
					<span class="card-title grey-text text-darken-4"><?php echo $event["ShortName"]; ?><i class="material-icons right">more_vert</i></span>
				</div>
				<div class="card-reveal">
					<span class="card-title grey-text text-darken-4"><?php echo $event["ShortName"]; ?><i class="material-icons right">close</i></span>
					<p><?php echo $event["Description"]; ?></p>
					<div class="card-action">
						<button class="btn waves-effect waves-light tooltipped like-event" data-position="bottom" data-delay="50" data-tooltip="Like" type="button" event-info-id=<?php echo "'".$event["Id"]."'"; ?> type="button">
							<i class="material-icons">thumb_up</i>
						</button>
						<button class="btn waves-effect waves-light tooltipped share-event" data-position="bottom" data-delay="50" data-tooltip="Share" type="button" event-info-id=<?php echo "'".$event["Id"]."'"; ?> type="button">
							<i class="material-icons">share</i>
						</button>
						<button class="btn waves-effect waves-light tooltipped buy-event" data-position="bottom" data-delay="50" data-tooltip="Tickets" type="button" event-info-id=<?php echo "'".$event["Id"]."'"; ?> type="button">
							<i class="material-icons">add_shopping_cart</i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
?>
</div>