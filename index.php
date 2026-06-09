<?php
// Redirect root to the shop page so visiting http://localhost/ opens the store
// Use absolute path to avoid relative-resolve issues in some browsers
// Use full absolute URL to avoid any server-side rewriting of Location header
// Serve a simple HTML meta-refresh + JS redirect to ensure browsers land on the shop
http_response_code(200);
?>
<!doctype html>
<html lang="ro">
	<?php
	// Redirect root to the shop page so visiting http://localhost/ opens the store
	// Use absolute path to avoid relative-resolve issues in some browsers
	header('Location: /practica/bijuterii.html');
	exit;
	?>
	</head>
