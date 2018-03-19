<?php
	function redirection($lien)
	{
		/* HTTP/1.1 accepte en header les URI absolue cf doc PHP */
		$lienAbsolu="https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/".$lien;
		header("Location:".$lienAbsolu);
		exit(0);
	}
?>
