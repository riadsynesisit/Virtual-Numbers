<?php
session_start();
require('fpdf.php');
require('./classes/Payments_received.class.php');
require("./includes/config.inc.php");
require("./includes/db.inc.php");

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('../images/logo.png',16,12);
    // Move to the right
    $this->SetXY(140,16);
    // Arial bold 15
    $this->SetFont('Arial','B',12);
    $this->MultiCell(100,6,"Hawking Software FZE");
    $this->SetFont('Arial','',12);
    $this->SetX(140);
    $this->MultiCell(100,5,"Office 508, 5th Floor\nThe Fairmont Dubai\nSheikh Zayed Road\nDubai\nUnited Arab Emirates\nCompany No:");
    // Title
    $this->SetFont('Arial','B',16);
    $this->Cell(78);
    $this->Cell(36,10,'Tax Invoice',1,0,'C');
    // Line break
    $this->Ln(20);
    $this->SetLineWidth(1);
    $y = $this->GetY();
    $this->Line(16,$y,194,$y);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

function ImprovedTable($header, $data)
{
    // Column widths
    $w = array(40, 20, 48, 28, 16, 28);
    
    // Header
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    
    $total = 0;
    // Data
    foreach($data as $row)
    {
        $this->SetX(16);
    	$this->Cell($w[0],6,$row[0],'LRB',0,'C');
        $this->Cell($w[1],6,$row[1],'LRB',0,'C');
        $this->Cell($w[2],6,$row[2],'LRB',0,'C');
        $this->Cell($w[3],6,$row[3],'LRB',0,'C');
        $this->Cell($w[4],6,$row[4],'LRB',0,'C');
        $this->Cell($w[5],6,number_format($row[5],2),'LRB',0,'C');
        $total = $total + $row[5];
        $this->Ln();
    }
    // Closing line
    $this->SetX(16);
    $this->Cell(array_sum($w),0,'','T');
    
}
}

if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
	//Simply continue no action required
}else{
	echo "Your session has expired. Please <a href='login.php'>re-login</a>";
	exit;
}

$payments_received = new Payments_received();
if(is_object($payments_received)){
	$inv_details = $payments_received->getInvoiceDetails($_GET["id"],$_SESSION["user_logins_id"]);
}else{
	die("Could not create Payments_received object.");
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
$pdf->SetX(16);
$pdf->Cell(0,10,'Invoice For:  '.$inv_details["name"]);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'                       '.$inv_details["name"]);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'Address :       ');
$y = $pdf->GetY()+10;
$pdf->SetXY(16,$y);
$pdf->SetFont('Times','',12);
$pdf->Cell(0,10,'Date Created      :  '.$inv_details["date_paid"]);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'Invoice Number :  '.$inv_details["inv_num"]);
$pdf->SetFont('Times','B',12);
$status = "COMPLETED";

$plan_info = "";
switch(intval($inv_details["chosen_plan"])){
	case 0:
		$plan_info = "Account Credit";
        break;
	case 1:
    	$plan_info = "Plan 1 - ".$inv_details["did_number"];
        break;
    case 2:
        $plan_info = "Plan 2 - ".$inv_details["did_number"];
        break;
    case 3:
        $plan_info = "Plan 3 - ".$inv_details["did_number"];
        break;
    default:
        $plan_info = "Unknown Plan";
}//end of switch

// Column headings
$header = array('Date', 'Order Num', 'Description', 'Status', 'Tax', 'Amount ('.SITE_CURRENCY.')');
$data[0] = array($inv_details["date_paid"],$inv_details["inv_num"],$plan_info,$status,0,$inv_details["amount"]);
$data[1] = array('','','','','TOTAL',$inv_details["amount"]);

$pdf->SetFont('Times','',12);
$y = $pdf->GetY()+20;
$pdf->SetXY(16,$y);
$pdf->ImprovedTable($header,$data);

$transid="";
switch(trim($inv_details["pymt_gateway"])){
	case "PayPal":
    	$transid = trim($inv_details["pp_receiver_trans_id"]);
        break;
    case "WorldPay":
	case "Credit_Card":
        $transid = trim($inv_details["transId"]);
        break;
    default:
        $transid = "N.A.";
        break;
}

$pdf->SetFont('Times','',12);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'Paid Through: '.$inv_details["pymt_gateway"]);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'Transaction ID: '.$transid);
$y = $pdf->GetY()+25;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'StickyNumber.com would like to thank you for your business.');
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'If you have any questions regarding this information, please contact us at billing@stickynumber.com');

$pdf->Output();
?>