<?php
$update_id = $_GET['update_id'] ?? '';
if (!empty($update_id)) {
    header("Location: fh_expenses_headmastercreation.php?update_id=" . urlencode($update_id));
} else {
    header("Location: fh_expenses_headmastercreation.php");
}
exit;
?>
