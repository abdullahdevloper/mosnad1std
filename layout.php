
<!DOCTYPE html>
<html >
	<head>
		<link rel="stylesheet" href="styles.css"/>
			<?php echo '<title>' . $title . '</title>'; ?>
	</head>
	<body>
		<header>
			<section>
				<h1>mosnad Website</h1>
			</section>
		</header>

		<?php
		require 'navBar.php';
		?>

		<main>
			<?php
			if(isset($_SESSION['loggedin'])){
					require 'sideBar.php';
			}
 			?>

			<?php echo $content; ?>

		</main>

	

	</body>
</html>
