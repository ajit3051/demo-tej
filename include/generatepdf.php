<?php
require_once(_BASEPATH . 'assets/vendor/autoload.php');

use Mpdf\Mpdf;

function generatePDF($bill_no, $pdf_type = 'bill')
{
    $validationHelper = new validation();

    $db = connect();   

    switch ($pdf_type) {
        case 'coordinator':
            $pdf_Template = file_get_contents(_BASEPATH . 'pdf_templates/coordinator_signup_pdf.html');

            $officeUseOnly = file_get_contents(_BASEPATH . 'pdf_templates/office_use_only.html');

            if (_WEBSITE_TYPE == 'state') {
                $officeUseOnly = file_get_contents(_BASEPATH . 'pdf_templates/office_use_only_state.html');
                $pdf_Template = str_replace('[FOR_OFFICE_USE]', $officeUseOnly, $pdf_Template);
            } else {
                $officeUseOnly = file_get_contents(_BASEPATH . 'pdf_templates/office_use_only.html');
                $pdf_Template = str_replace('[FOR_OFFICE_USE]', $officeUseOnly, $pdf_Template);
            }
            break;
        default:
            $pdf_Template = setSytemMailConstants('bill-invoice.html');

            $stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=?", 's', $bill_no);

            $res = $stmt->fetch_assoc();

            foreach ($res as $key => $value) {
                $$key = $validationHelper->filterText($value);
            }
            $stmt->close();


            $query = $db->select("SELECT * FROM tbl_bill WHERE bill_number=? ORDER BY id DESC", 's', $bill_no);
            $rowcount = $query->num_rows();
            if ($rowcount > 0) {
                $gst_amount = 0.00;
                $total_amount = 0.00;
                $total_taxable_amount = 0.00;
                $item_list ='';
                for ($i = 1; $i <= $rowcount; $i++) {
                    $row = $query->fetch_assoc();

                    $gst_amount += (float)$row['gst_amount'];
                    $total_amount += (float)$row['amount'];
                    $total_taxable_amount += (float)$row['taxable_amount'];
                    
                    /* $item_list .='<tr>
                       
                        <td class="cs-width_3">'. $row['item_name'] .' - HSN :33074100 </td>
                        <td class="cs-width_1">'. $row['purchase_price'] .'</td>
                        <td class="cs-width_2">'. $row['qty'] .'</td>
                        <td class="cs-width_2">'. $row['net_price'] .'</td>
                        <td class="cs-width_2 cs-text_right">'. $row['taxable_amount'] .'</td>
                    </tr>'; */

                    $item_list .='<tr>
                       
                    
                    <td class="cs-width_3">'. $row['item_name'] .' - HSN : 33074100 </td>
                    <td class="cs-width_1">'. $row['purchase_price'] .'</td>
                    <td class="cs-width_2">'. $row['qty'] .'</td>
                    <td class="cs-width_2">'. $row['net_price'] .'</td>
                    <td class="cs-width_2 cs-text_right">'. $row['taxable_amount'] .'</td>
                </tr>';
                   
                }
            }

            if ($under_gst == 'Inter') { 
                $under_gst = '<tr class="cs-border_left">
                  <td class="cs-width_3 cs-semi_bold cs-primary_color cs-focus_bg">IGST 5%</td>
                  <td class="cs-width_3 cs-semi_bold cs-focus_bg cs-primary_color cs-text_right">₹ '. $gst_amount .'</td>
                </tr>';
               } else if ($under_gst == 'Local') {
                $under_gst ='<tr>
                  <td class="cs-p-b10">CGST 2.5%</td>
                  <td class="cs-text_right cs-primary_color cs-p-b10">₹ '. getGSTValueBygstType($gst_amount) .'</td>
                </tr>
                <tr>
                  <td class="cs-p-b10">SGST 2.5%</td>
                  <td class="cs-text_right cs-primary_color cs-p-b10">₹ '. getGSTValueBygstType($gst_amount) .'</td>
                </tr>
                 <tr>
                  <td class="cs-p-b10">Tax Amt</td>
                  <td class="cs-text_right cs-primary_color cs-p-b10">₹ '. number_format($gst_amount, 2) .'</td>
                </tr>';
               } 

            $pdf_Template = str_replace('[INVOICE]', $bill_number, $pdf_Template);
            $pdf_Template = str_replace('[PURCHAGE_DATE]', dateformat($purchage_date), $pdf_Template);
            $pdf_Template = str_replace('[CASHIER]', $_SESSION['login_user'], $pdf_Template);
            $pdf_Template = str_replace('[ACCOUNT_NAME]', $account_name, $pdf_Template);
            $pdf_Template = str_replace('[MOBILE_NUMBER]', $mobile_number, $pdf_Template);
            $pdf_Template = str_replace('[PAY_MODE]', getPayModeByCode($pay_mode), $pdf_Template);
            $pdf_Template = str_replace('[ITEM_LIST]', $item_list, $pdf_Template);
            $pdf_Template = str_replace('[TOTAL_TAXABLE_AMOUNT]', number_format($total_taxable_amount, 2), $pdf_Template);
            $pdf_Template = str_replace('[UNDER_GST]', $under_gst, $pdf_Template);
            $pdf_Template = str_replace('[TOTAL_AMOUNT]', number_format($total_amount, 2), $pdf_Template);

            break;
    }
    /*  echo $pdf_Template;
    die; */


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

    $pdfFileName = md5(uniqid(time())) . '_' . $bill_no . '_' . $pdf_type . '.pdf';
    $pdfFilePath = _UPLOAD_DIR . 'generated_pdf/' . $pdf_type . '/';


    /****************** Part 2 *********************/
    $mpdf->Output($pdfFilePath . $pdfFileName, 'F');

    /********** PDF Generation END *************************** */

    return 'generated_pdf/' . $pdf_type . '/' . $pdfFileName;
}

function downloadGeneratedFile($generatedFile, $fileName)
{

    header("Content-disposition: attachment; filename=" . $fileName);
    header("Content-type: application/pdf");
    readfile(_UPLOAD_DIR . $generatedFile);
}
