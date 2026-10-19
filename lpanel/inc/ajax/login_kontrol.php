<?php
session_start();
if ($_SESSION["yonetici_id"]) {
	echo true;
} else {
	echo false;
}
?>