<?php
$update_id = $_GET['update_id'] ?? '';
if ($update_id) {
    header("Location: fh_salesman_target_mastercreation.php?update_id=" . urlencode($update_id));
} else {
    header("Location: fh_salesman_target_mastercreation.php");
}
exit;
