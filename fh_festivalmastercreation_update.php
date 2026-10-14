<?php
$update_id = $_GET['update_id'] ?? '';
if (!empty($update_id)) {
    header("Location: fh_festivalmastercreation.php?update_id=" . urlencode($update_id));
} else {
    header("Location: fh_festivalmastercreation.php");
}
exit;
?>