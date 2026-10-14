<?php
$update_id = $_GET['update_id'] ?? '';
if (!empty($update_id)) {
    header("Location: fh_groupmastercreation.php?update_id=" . urlencode($update_id));
} else {
    header("Location: fh_groupmastercreation.php");
}
exit;
?>