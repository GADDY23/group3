<?php
session_start();
session_destroy();
session_unset("loggeduser")
session_unset("loggedrole")

header("Location: index.p.php");
exit;