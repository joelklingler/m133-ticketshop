<?php
// Checks the users login-status.
$role = -1;
if(isset($_SESSION['Role']))
{
	$role = $_SESSION['Role'];
}
else
{
	// If not logged-in - show the login-form.
	?>
	<script>
		$('.login').fadeIn('300');
	</script>
	<?php
}
if($role == 1)
{
?>
<h5>Meine Veranstaltungen</h5>
<p>Informationen zu Ihren Veranstaltungen. Wählen Sie eine Veranstaltung zum bearbeiten aus.</p>
<div class="row">
	<?php
	// Shows all events on cards.
	include 'php/read.php';
	$results = getEventsByUser($_SESSION['User']);
	if(is_array($results) || is_object($results))
	{
		$i = 0; ?>
		<script>
			// data-array for chart-data.
			var data = new Array();
		</script>
		<?php
		foreach ($results as $result) {
			$types = getEventType($result["Id"]);
			$ticketsLeft = $result["TicketsLeft"];
			$ticketsSold = $result["TicketQuantity"] - $ticketsLeft;
			?>
			<div class="col s6 m6 l6">
				<div class=<?php echo "'card ".$result["Id"]."'"; ?> event-id=<?php echo "'".$result["Id"]."'"?>>
					<div class="card-content">
						<span class="card-title">
							<?php echo $result["ShortName"]; ?>
							<?php
							foreach ($types as $type) {
								?>
								<div class="chip">
									<?php
									echo $type[0]["ShortName"];
									?>
								</div>
								<?php
							}
							?>
						</span>
						<p><?php echo $result["Description"] ?></p>
						<div class="canvas-holder">
							<canvas class=<?php echo "'doughnut-chart-".$i." chart'"; ?> id="chart-area"/>
							<script>
							// Chart data
							var doughnutData = [
								{
									value: <?php echo $ticketsSold; ?>,
									color: "#4caf50 ",
									highlight: "#66bb6a",
									label: "Verkauft"
								},
								{
									value: <?php echo $ticketsLeft; ?>,
									color: "#f44336",
									highlight: "#ef5350",
									label: "Verfügbar"
								}
							];
							data.push(doughnutData);
							</script>
						</div>
						<div class="event-info-collection">
							<ul class="collection">
								<li class="collection-item avatar">
									<i class="material-icons circle green">attach_money</i>
									<span class="title">Ticketpreis</span>
									<p><?php echo $result["TicketCost"].".- CHF"; ?></p>
								</li>
								<li class="collection-item avatar">
									<i class="material-icons circle blue">place</i>
									<span class="title">Ort</span>
									<p><?php echo $result["Location"]; ?></p>
								</li>
									<li class="collection-item avatar">
									<i class="material-icons circle brown">today</i>
									<span class="title">Datum</span>
									<p><?php echo $result["EventStartDate"]." - ".$result["EventEndDate"]; ?></p>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-action">
						<button class="btn waves-effect waves-light edit-event" event-id=<?php echo "'".$result["Id"]."'"?> type="edit" value="edit" name="action">Bearbeiten
							<i class="material-icons right">mode_edit</i>
						</button>
					</div>
				</div>
			</div>
			<?php
			$i ++;
		}
	}
?>
</div>
<script>
	window.onload = function()
	{
		// Initializes the chart foreach chart-card.
		var i = 0;
		$('.chart').each(function() {
			var ctx = $(this).get(0).getContext("2d");
			window.myDoughnut = new Chart(ctx).Doughnut(data[i], {responsive : true});
			i++;
		});
	};
</script>
<?php
}
else
{
	?>
	<h5>Sie verfügen nicht über die Berechtigungen zum betrachten dieser Seite.</h5>
	<?php
}
?>