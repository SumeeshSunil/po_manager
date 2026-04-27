<?php
include 'config.php';
checkLogin();
checkRole(['user']);

$po_id = (int)$_POST['po_id'];
$date = $_POST['expected_delivery_date'];
$qtys = $_POST['deliverable_qty'];
$reason = trim($_POST['short_reason_hidden'] ?? '');

$conn->begin_transaction();

try {

$hasShort=false;

foreach($qtys as $id=>$val){

$id=(int)$id;
$val=(float)$val;

$q=$conn->prepare("SELECT qty FROM po_items WHERE id=? AND po_id=?");
$q->bind_param("ii",$id,$po_id);
$q->execute();
$row=$q->get_result()->fetch_assoc();

$poQty=$row['qty'];

if($val>$poQty) throw new Exception("Invalid qty");

if($val<$poQty) $hasShort=true;
}

if($hasShort && !$reason){
throw new Exception("Reason required");
}

foreach($qtys as $id=>$val){

$id=(int)$id;
$val=(float)$val;

$q=$conn->prepare("SELECT qty FROM po_items WHERE id=? AND po_id=?");
$q->bind_param("ii",$id,$po_id);
$q->execute();
$row=$q->get_result()->fetch_assoc();

$poQty=$row['qty'];

$status='full';
if($val==0) $status='cannot';
elseif($val<$poQty) $status='partial';

$saveReason = ($val<$poQty)?$reason:null;

$u=$_SESSION['user_id'];

$up=$conn->prepare("
UPDATE po_items
SET deliverable_qty=?,
expected_delivery_date=?,
reason=?,
user_status=?,
updated_by=?,
updated_at=NOW()
WHERE id=? AND po_id=?
");

$up->bind_param("isssiii",$val,$date,$saveReason,$status,$u,$id,$po_id);
$up->execute();
}

$po=$conn->prepare("UPDATE purchase_orders SET expected_delivery_date=?, po_status='sent_to_schedule_delivery' WHERE id=?");
$po->bind_param("si",$date,$po_id);
$po->execute();

$conn->commit();

header("Location: po_view.php?id=".$po_id);
exit;

}catch(Exception $e){
$conn->rollback();
die($e->getMessage());
}