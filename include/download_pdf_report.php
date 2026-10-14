<?php
require_once(_BASEPATH . 'assets/vendor/autoload.php');

use Mpdf\Mpdf;

function exportResultPdf($serviceName, $stmt)
{
    $dataArr = array();
    $temp = '<table>';

    // Add the header row first
    $tableHeader = '<tr><th>S.No.</th>';
    $isHeaderSet = false;

    // Initialize row counter for serial numbers
    $rowCounter = 1;

    while ($row = $stmt->fetch_assoc()) {
        // Add the header dynamically on the first iteration
        if (!$isHeaderSet) {
            foreach ($row as $key => $value) {
                if ($key == 'user_id') {
                    continue; // Skip user_id column
                }
                $tableHeader .= '<th>' . ucwords(str_replace('_', ' ', $key)) . '</th>';
            }
            $tableHeader .= '</tr>';
            $temp .= $tableHeader; // Append the header to the table
            $isHeaderSet = true;
        }

        // Add the data row
        $temp .= '<tr>';
        $temp .= '<td>' . $rowCounter++ . '</td>'; // Add serial number
        foreach ($row as $key => $value) {
            if ($key == 'user_id') {
                continue; // Skip user_id column
            }

            $value = trim($value);

            // Handle special case for project_organisation
            if ($key == 'project_organisation') {
                $orgDetails = getRegFullOrg2($row['user_id']);
                $temp .= '<td>' . $orgDetails['project'] . '</td>';
            } else {
                $temp .= '<td>' . $value . '</td>';
            }
        }
        $temp .= '</tr>';
    }

    $temp .= '</table>';


    $pdf_Template = setSytemMailConstants('export-pdf.html');
    $pdf_Template = str_replace('[PDF_DATA]', $temp, $pdf_Template);
  
    date_default_timezone_set('Asia/Kolkata');
    $file_name = $serviceName . '-' . date('j-M-Y-H-i-s');

    $config = [
        'format' => 'A4',        // Page format (e.g., A4, Letter)
        'orientation' => 'P',    // Orientation ('P' for portrait, 'L' for landscape)
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 5,
        'margin_bottom' => 5
    ];

    

    $mpdf = new Mpdf($config);
    
    $mpdf->Bookmark('Start of the document');
    $mpdf->SetDisplayMode('fullpage');
    $stylesheet = file_get_contents(_BASEPATH . 'assets/invoice/css/style.css');

    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

    $mpdf->WriteHTML($pdf_Template, \Mpdf\HTMLParserMode::HTML_BODY);

    $file_name = $serviceName . '-' . date('j-M-Y-H-i-s');
    $pdfFileName = $file_name . '.pdf';

    // Specify the file name and download the PDF
    $mpdf->Output($pdfFileName, \Mpdf\Output\Destination::DOWNLOAD);

    exit;
}
