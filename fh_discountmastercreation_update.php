<?php
$update_id = $_GET['update_id'] ?? '';
if ($update_id) {
    header("Location: fh_discountmastercreation.php?update_id=" . urlencode($update_id));
} else {
    header("Location: fh_discountmastercreation.php");
}
exit;
