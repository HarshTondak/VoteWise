<?php
session_start();
// Ends all the SESSIONS
session_destroy();
session_unset();
?>

<script>
    location.assign("../index.php");
</script>