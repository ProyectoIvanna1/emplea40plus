<?php
session_name("session_" . $nombre_usuario);
session_start();
session_unset();
session_destroy();
header("Location: ../../index.html");
exit;