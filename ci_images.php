<?php

// Image hohlen.

header('Content-type: image/png');

readfile("images/goldpresse/".$_GET['id'].".png");

?>