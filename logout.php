<?php
session_start(); //just makes sure a session is open, unsets everything, and destroys it
session_unset();
session_destroy();
?>