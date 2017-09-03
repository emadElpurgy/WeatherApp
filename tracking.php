<?
header("Cache-Control: no-cache, must-revalidate");
include("db2.php");
$getCodeTypeQuery = 'select * from shipment where shipment_code = "'.$_GET['tracknumber'].'"';
$getCodeTypeResult = mysql_query($getCodeTypeQuery)or die("error getCodeTypeQuery not done ".mysql_error());
if(mysql_num_rows($getCodeTypeResult) > 0){
$codeQueryPart = ' shipment.shipment_code = "'.$_GET['tracknumber'].'"';
}else{
$codeQueryPart = ' container.container_code = "'.$_GET['tracknumber'].'"';
}

$getInfoQuery = 'select * from (select 
"0" as "state_id",
container.container_id,
container.container_code,
container.container_size,
container.final_pod,
container.shipped_to,
UNIX_TIMESTAMP(now()) as "time",
container_state.state_name,

shipment.shipment_code as "shipmentCode",
shipment.departure_date,
shipment.vessel as "shipment_vessel",
shipment.port_of_load,
shipment.port_of_discharge,
(case when(shipment.transhipment =1)then("Yes")else("No") end)as "transhipment" 

from 
container_state_log
inner join container on(container.container_id = container_state_log.container_id)
inner join shipment on(shipment.shipment_id = container.shipment_id)
inner join container_state on(container_state.state_id = container_state_log.state_id)
where 
'.$codeQueryPart.' 
order by container.container_id , container_state_log.date desc )as p group by p.container_id';
$getInfoResult = mysql_query($getInfoQuery)or die("error getInfoQuery not done ".mysql_error());
$jsonData = array();
while($mainInfo = mysql_fetch_array($getInfoResult)){
$getStateLogQuery = 'select 
container.container_code,
container.container_size,
container.final_pod,
container.shipped_to,
container_state_log.date,
container_state_log.port,
container_state_log.vessel,
container_state_log.voyage,
container_state.state_name
from 
container_state_log
inner join container on(container.container_id = container_state_log.container_id)
inner join container_state on(container_state.state_id = container_state_log.state_id)
where 
container.container_id = "'.$mainInfo['container_id'].'"
order by container_state_log.date ';
$getStateLogResult = mysql_query($getStateLogQuery)or die("error getStateLogQuery not done ".mysql_error());
$logArray = array();
while($log = mysql_fetch_array($getStateLogResult)){
$actionArray = array('date'=>$log['date'],'state'=>$log['state_name'],'vessel'=>$log['vessel'],'Voyage'=>$log['voyage'],'port'=>$log['port']);
array_push($logArray,$actionArray);
}
$resultArray = array(
'key'=>$mainInfo['container_code'],
'label'=>$mainInfo['container_code'],
'shipmentCode'=>$mainInfo['shipmentCode'],
'departureDate'=>$mainInfo['departure_date'],
'vessel'=>$mainInfo['shipment_vessel'],
'portOfLoad'=>$mainInfo['port_of_load'],
'portOfDischarge'=>$mainInfo['port_of_discharge'],
'transhipment'=>$mainInfo['transhipment'],
'currently'=>array(
	'time'=>(($mainInfo['time']+0)),
	'summary'=>$mainInfo['state_name'],
	'icon'=>'state-1',
	'size'=>$mainInfo['container_size'],
	'finalPOD'=>$mainInfo['final_pod'],
	'shippedTo'=>$mainInfo['shipped_to']),
'logData'=>array(
	'data'=>$logArray
	)
);
array_push($jsonData,$resultArray);
}
echo json_encode($jsonData);
?>