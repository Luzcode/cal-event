		</div>
		<script src="./assets/js/jquery-3.4.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="./assets/js/main.js" type="text/javascript" charset="utf-8" async defer></script>
		<?php
			if (isset($pageInfo)) {
				foreach ($pageInfo["js"] as $jsPath) {
					echo '<script src=' . $jsPath .' type="text/javascript" charset="utf-8" async defer></script>';
				}
			}
		?>
	</body>
</html>