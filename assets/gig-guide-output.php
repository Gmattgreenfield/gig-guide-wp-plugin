<table>

	<thead>
		<tr>
			<th>Venue</th>
			<th>Town</th>
			<th>Date</th>
			<th>Time</th>
		</tr>
	</thead>

	<tbody>

<?php
	$argument = array(
	  'post_type' => 'gigs'
	);
	$gigs = new WP_Query( $argument );
	if( $gigs->have_posts() ) {
	  while( $gigs->have_posts() ) {
		$gigs->the_post();

		$id = $post->ID;

		$town = get_post_meta(get_the_ID(), 'gig_town', true );
		$date = get_post_meta(get_the_ID(), 'gig_date', true );
		$formattedDate = date("d/m/Y", strtotime($date));

		$time = get_post_meta(get_the_ID(), 'gig_time', true );


?>

		<tr>
			<td><?php the_title() ?></td>
			<td><?= $town ?></td>
			<td><?= $formattedDate ?></td>
			<td><?= $time ?></td>
		</tr>

<?php
	  }
	}
	else {
	  echo 'There are currently no gigs to display.';
	}
?>





	</tbody>

</table>