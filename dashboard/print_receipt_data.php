<?php
session_start();
require('fpdf.php');
require('./classes/Payments_received.class.php');
require("./includes/config.inc.php");
require("./includes/db.inc.php");

class PDF extends FPDF
{
// Page header
function Header($data = array())
{
    // Logo
	$main_logo = "../images/main-logo".$data['logo'].".png";
    $this->Image($main_logo,16,12);
	$this->SetXY(16,26);
	$this->SetFont('Courier','',8);
	$this->Cell(16,32, "Rock Solid Phone Forwarding Services");

    // Move to the right
    $this->SetXY(110,16);
    // Arial bold 15
    //$this->SetFont('Courier','',12);
    $this->Cell(100,6,"Date# ".$data['date_paid']);
	$this->SetXY(110,18);
    // Arial bold 15
    //$this->SetFont('Courier','',12);
    $this->Cell(100,10,"Invoice# SN".$data['inv_no']);
    //$this->SetFont('Courier','',12);
    $this->SetXY(110, 20);
	$this->Cell(100,14,"Transaction# ". $data['transid']);
	//$this->SetFont('Arial','',12);
    $this->SetXY(110,30);
    $this->MultiCell(100,5,"Bill TO: \nSteve S.\nSheikh Zayed RoadDubai ");
    // Title
	$this->SetXY(10, 36);
    $this->SetFont('Arial','B',12);
    $this->Cell(90);
    $this->Cell(26,30,'Invoice Receipt',0,0,'C');
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
    $this->SetFont('Courier','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

function ImprovedTable($data)
{
    // Column widths
    $w = array(40, 20, 48, 28, 16, 28);
    $this->SetFont('Courier','',12);
    // Header
    //for($i=0;$i<count($header);$i++)
	$this->setFillColor(210,210,230); 
    $this->Cell(150,7,"Description",1,0,'L', true);
	$this->setFillColor(210,220,230); 
	$this->Cell(30,7,"Amount",1,0,'R', true);
    $this->Ln();
    
	 $this->SetX(16);
	$this->Cell(150,7,$data[0],1,0,'L');
	$this->Cell(30,7,"$".$data[1],1,0,'R');
    $this->Ln();
	
	
    for($i = 0 ; $i < 4 ; $i++)
    {
        $this->SetX(16);
    	$this->Cell(150,7,"",'LRB',0,'C');
        $this->Cell(30,7,"",1,0,'C');
        $this->Ln();
    }
	
	$this->SetX(16);
	$this->Cell(150,7,"Total",1,0,'R');
	$this->Cell(30,7,"$".$data[1],1,0,'R');
    $this->Ln();
	$this->SetX(16);
	$this->Cell(150,7,"Paid",1,0,'R');
	$this->Cell(30,7,"$".$data[1],1,0,'R');
    $this->Ln();
	$this->SetX(16);
	$this->Cell(150,7,"Amount Owing",1,0,'R');
	$this->Cell(30,7,"$0.00",1,0,'R');
    $this->Ln();
    // Closing line
    //$this->SetX(16);
    //$this->Cell(array_sum($w),0,'','T');
    
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

$logo = "-r1";
// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('','',array("logo"=>$logo, "transid" => $transid, "inv_no" => $inv_details["inv_num"], "date_paid" => $inv_details["date_paid_dt"]));
$pdf->SetFont('Courier','',8);
/*$pdf->SetFont('Times','B',12);
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
$pdf->Cell(0,10,'Invoice Number :  '.$inv_details["inv_num"]);*/
$pdf->SetFont('Times','B',12);
$status = "COMPLETED";

$plan_info = $inv_details['country_name']." Number ";
switch(intval($inv_details["chosen_plan"])){
	case 0:
		$plan_info .= "-Account Credit";
        break;
	case 1:
    	$plan_info .= "Plan 1 +".$inv_details["did_number"];
        break;
    case 2:
        $plan_info .= "Plan 2 +".$inv_details["did_number"];
        break;
    case 3:
        $plan_info .= "Plan 3 +".$inv_details["did_number"];
        break;
    default:
        $plan_info = "Unknown Plan";
}//end of switch

// Column headings
$header = array('Description', 'Amount');
$data = array($plan_info,$inv_details["amount"]);
//$data[1] = array('','','','','TOTAL',$inv_details["amount"]);

$pdf->SetFont('Times','',12);
$y = $pdf->GetY()+20;
$pdf->SetXY(16,$y);
$pdf->ImprovedTable($data);



//$pdf->SetFont('Times','',12);
$y = $pdf->GetY()+5;
$pdf->SetXY(15, 150);
$pdf->MultiCell(90,10,"Pyament Information\n".$inv_details['pp_id']);
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,$name,$card_number);
$y = $pdf->GetY()+25;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'StickyNumber.com would like to thank you for your business.');
$y = $pdf->GetY()+5;
$pdf->SetXY(16,$y);
$pdf->Cell(0,10,'If you have any questions regarding this information, please contact us at billing@stickynumber.com');

$pdf->Output();
?>