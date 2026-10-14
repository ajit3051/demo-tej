<?php
function exportResult($serviceName, $stmt)
{
    $dataArr = array();
    $headerArr = array('S.No.');
    $isHeaderSet = false;
    $counter = 1;
    while ($row = $stmt->fetch_assoc()) {
        $temp = array($counter);
        foreach ($row as $key => $value) {

            if ($key == 'user_id') {
                continue;
            }
            $value = trim($value);
            if ($key == 'project_organisation') {
                $orgDetails = getRegFullOrg2($row['user_id']);
                array_push($temp, $orgDetails['project']);
            } else {
                array_push($temp, $value);
            }

            // Creating Exported File Column Header 
            if (!in_array($key, $headerArr)) {
                array_push($headerArr, ucwords(str_replace('_', ' ', $key)));
            }
        }
        if (!$isHeaderSet) {
            $dataArr[] = $headerArr;
        }
        $isHeaderSet = true;
        $dataArr[] = $temp;
        $counter++;
    }

    date_default_timezone_set('Asia/Kolkata');
    $file_name = $serviceName . '-' . date('j-M-Y-H-i-s');

    require_once(_CLASS_PATH . "php-excel.class.php");
    $xls = new Excel_XML('UTF-8', false, $file_name);
    $xls->addArray($dataArr);
    $xls->generateXML($file_name);
    exit;
}
?>
