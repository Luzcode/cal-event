<?php
	require_once '../vendor/autoload.php';
	require_once '../vendor/autoload.php';
	include './page-config.php';
	use PhpDump\Dump;
	require '../util/send-mail.php';
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<meta name="author" content="Daniel Adu-Djan">
		<meta name="description" content='<?php if (isset($pageInfo)) { echo $pageInfo["description"];} ?>'>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php if (isset($pageInfo)) { echo $pageInfo["title"]; }?></title>
		<link rel="icon" href="../assets/img/favicon.ico">
        <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" >
		<link rel="stylesheet" href="../assets/css/main.css">
		<?php 
			if (isset($pageInfo)) {
				if (in_array("font-awesome", $pageInfo["import"])) {
					echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
				}
				foreach ($pageInfo["css"] as $cssPath) {
					echo '<link rel="stylesheet" href=' . $cssPath . '>';
				}
			}
		?>
	</head>
	<body>
		<div class="body backgound-overlay">
			<header>
				<div class="container">
					<div class="row d-flex flex-row justify-content-between align-items-center">
						<div class="logo-with-text pl-3">
							<a href="./index.php">
								<img src="../assets/img/logo-white-text.svg" width="60" height="60">
							</a>
						</div>
						
						<div id="nav-bs" class="navigation d-flex flex-row align-items-center">
							<?php
								if (isset($pageInfo)) {
									foreach ($pageInfo ["navLinks"] as $linkName => $href) {
										echo "<a href=" . $href . "><div class='nav-link py-2'>" . $linkName . "</div></a>";
									}
								}
							?>
						</div>
						<?php
							if (isset($pageInfo) && count($pageInfo["navLinks"]) > 0) {
								echo '<div id="nav-ss" class="navigation">
									<button id="menu-icon" class="pr-3 icon-btn"><img class="icon" src="../assets/img/menu-btn.svg" alt="menu" width="30">Menu</button>
								</div>';
							}
						?>
					</div>
					<div id="ss-nav-content" class="navigation row nav-content no-display">
						<?php
							if (isset($pageInfo)) {
								foreach ($pageInfo ["navLinks"] as $linkName => $href) {
									echo "<a href=" . $href . "><div class='row nav-link py-2'>" . ucwords(strtolower($linkName)) . "</div></a>";
								}
							}
						?>
					</div>
				</div>
			</header>

			<div class="scroll-to-top my-tooltip" style="display: none;">
				<button id="scroll-to-top-button"><img class="icon-svg" src="../assets/img/scroll-up.svg" width="50">
				scroll to top</button>
				<p class="tooltip-text tooltip-pos-top-left">Scroll To Top</p>
			</div>

			<main>
				<div class="content d-flex justify-content-center align-items-center">
					<div class="loader"></div>
					<div class="container my-4 ss-full-width col-8 content-container no-display">
						<div class="container my-4 py-4 white-bg content-wrap d-flex flex-column align-items-center justify-content-center">
							<div class="heading my-3">
								<h1>Choose an option</h1>
							</div>
							<div class="my-4">
								<button class="ics-btn" onclick="alert('Sorry, Google Calendar feature is not yet developed');">
									<div class="option-icon"><img src="../assets/img/g-calendar.png" width="100" height="100"/></div>
									<div class="option-text">Add to Google Calendar</div>
								</button>
							</div>
							<div class="my-4">
								<button class="ics-btn" onclick="alert('Sorry, Outlook Calendar feature is not yet developed.');">
									<div class="option-icon"><img src="../assets/img/outlook.png" width="100" height="100"/></div>
									<div class="option-text">Add to Outlook Calendar</div>
								</button>
							</div>
							<div class="my-4">
								<form id="download_form" action="download.php" method="post" class="my-4 d-flex flex-column justify-content-center">
									<div class="mb-3">
										<p>Other Platforms / Calendar Apps:</p>
									</div>
									<input id="file_name" type="hidden" name="fn" value="">
									<button type="submit" id="ics-download-btn" class="ics-btn">
										<div class="option-icon"><img src="../assets/img/download.svg" width="50" height="50"/></div>
										<div class="option-text">Download (.ics)</div>
									</button>
									<div class="option-note"><p>*Download .ics file and open with the calendar application.<p></div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<script src="../assets/js/jquery-3.4.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="../assets/js/main.js" type="text/javascript" charset="utf-8" async defer></script>
		<?php
			if (isset($pageInfo)) {
				foreach ($pageInfo["js"] as $jsPath) {
					echo '<script src=' . $jsPath .' type="text/javascript" charset="utf-8" async defer></script>';
				}
			}
		?>
	</body>
</html>