<?php
////////////////GET RESPONSE 
/**
*FOR GET RESPONSE 
*
*
*/

require_once 'GetResponseAPI3.class.php';


function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

//AUTHENTICATION
/*

$url = 'https://api3.getresponse360.com/v3';


// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => 'X-Auth-Token:api-key edd91283845856ad6863a3ee76a421c9',
        'header' => 'X-DOMAIN:aiesec.org.mx',
        'method'  => 'GET'
        
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { echo "salio mal"; }
*/

//AUTHENTICATION
//SENDING CONTACT START 
/*$ip = get_client_ip();

$data = '{
	"name" :"test",
	"email": "enrique.wps@gmail.com",
    "dayOfCycle": "0",
    "campaign": {
        "campaignId": "47181157"
    },
    "customFieldValues": [
        {
            "customFieldId": "universidad",
            "value": [
                "UNAM"
            ]
        }
    ],
    "ipAddress": "{$ip}"
}';
$url = 'https://api3.getresponse360.com/v3/contacts';
// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => 'X-Auth-Token:api-key edd91283845856ad6863a3ee76a421c9',
        'header' => 'X-DOMAIN:aiesec.org.mx',
        'method'  => 'POST',
        'content' => $data
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { echo 'fallo'; }
echo $ip;
*/

/*
   $api_key = 'edd91283845856ad6863a3ee76a421c9';
    $api_url = 'https://api3.getresponse360.com/v3/contacts';
    $client = new jsonRPCClient($api_url);
    $result = NULL;




$CAMPAIGN_ID = array_pop($campaigns);

    $subscriberEmail =  $_GET['email'];

try {
    $result = $client->add_contact(
        $api_key,
        array (
            'campaign'  => $CAMPAIGN_ID,
            'name'     =>  $subscriberName,
            'email'     =>  $subscriberEmail,
            'cycle_day' => '0'
                    )
    );
}
catch (Exception $e) {
    # check for communication and response errors
    # implement handling if needed
    die($e->getMessage());
}
*/


//SENDING CONTACT END



echo ('Antes');

$getresponse = new GetResponse('cf98eff6085058251de512cc645dd5fe');

$getresponse->enterprise_domain = 'test.aiesec.org.mx';
$getresponse->api_url = 'https://api3.getresponse360.com/v3'; 

if ($getresponse == null){
	echo 'is null';
}else{
	echo 'que pedo';
}

try{
 echo 'a huevo0';
 $getresponse->setCustomField(array(
                'name' => 'LOL',
                'type' => 'text',
                'hidden' => 'false',
    ));
 echo 'a huevo1';
}catch (Exception $e){
	echo 'errorrrrrrr';
}
echo ('Despues');

/*
$getresponse->addContact(array(
    'name'              => 'Jon Smith',
    'email'             => 'esuarez@aiesec.org.mx',
    'dayOfCycle'        => 0,
    'campaign'          => array('campaignId' => '47181157'),
    'ipAddress'         => '{get_client_ip()}',
    'customFieldValues' => array(
        array('customFieldId' => 'custom_field_id_obtained_by_API',
            'value' => array(
                'Y'
            )),
         array('customFieldId' => 'custom_field_id_obtained_by_API',
            'value' => array(
                'Y'
            ))
    )
));

/*
$getresponse->addContact(array(
    'name'              => 'Jon Smith',
    'email'             => 'enrique.wps@gmail.com',
    'dayOfCycle'        => 0,
    'campaign'          => array('campaignId' => '47181157'),
    'ipAddress'         => get_client_ip,
    'customFieldValues' => array(
        array('customFieldId' => 'custom_field_id_obtained_by_API',
            'value' => array(
                'Y'
            )),
         array('customFieldId' => 'custom_field_id_obtained_by_API',
            'value' => array(
                'Y'
            ))
    )
));
*/




?>