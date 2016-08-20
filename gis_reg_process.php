<?php

include_once '/lib/podio-php-4.3.0/PodioAPI.php';

//Podio submit
// This is to test the conection with the podio API and the authentication
Podio::setup('aiesec-mexico', 'H1m3TpwjqotvYxwJzTcXtNJVnPJP47UE6B825iOnS2VzEsmlHd9222c3yUcOGWZi');

//Podio::setup('test-aiesec-mexico', 'tbHvnd6k8MB2MMRCsqSVzL0FBbkshZorIt6izgtGsfDRY53FQBb9iRckDIgSgxLJ');

//getting the podio Id for each lc
$lc_podio = 'lc_podio.json';
$json_podio_lc = file_get_contents($lc_podio, false, stream_context_create($arrContextOptions)); 
$lc_podio_map = json_decode($json_podio_lc,true); 
$user_lc_podio = $lc_podio_map[$_POST['localcommittee']];

//getting the podio Id for each lc
$uni_podio = 'universidades_podio.json';
$json_podio_uni = file_get_contents($uni_podio, false, stream_context_create($arrContextOptions)); 
$uni_podio_map = json_decode($json_podio_uni,true); 
$user_uni_podio = $uni_podio_map[$_POST['university']];


$program = intval($_POST['interested_in']);
$podio_id = 1;

try {
//GENERAL
  //Podio::authenticate_with_app(16144547, 'dfbd22ffea04489f9973181241afd4bd');

//OGV
 if ($program == 1){
    Podio::authenticate_with_app(16460504, 'db35069c26694b8cb2d2a17eda7fa94a');
    $podio_id = 16460504;
  }
//OGT
  else {
    Podio::authenticate_with_app(16452042, 'c953eaeb05e246d1b465b9d1408ff81f');
  $podio_id = 16452042;
  }
    



//OGt
$fields = new PodioItemFieldCollection(array(
  new PodioTextItemField(array("external_id" => "titulo", "values" => ($_POST['first_name'] ) )),
  new PodioTextItemField(array("external_id" => "apellido", "values" => $_POST['last_name'])),
  new PodioTextItemField(array("external_id" => "correo", "values" => $_POST['email'])),
  new PodioTextItemField(array("external_id" => "numero-telefonico", "values" => $_POST['phone'])),

    new PodioCategoryItemField(array("external_id" => "comite-local", "values" => intval($user_lc_podio))),
    new PodioCategoryItemField(array("external_id" => "institutouniversidad", "values" => intval($user_uni_podio))),
new PodioCategoryItemField(array("external_id" => "fuente", "values" => intval($_POST['source'])))
));







// Create the item object with fields
// Be sure to add an app or podio-php won't know where to create the item
$item = new PodioItem(array(
  'app' => new PodioApp($podio_id), // Attach to app with app_id=123
  'fields' => $fields
));

// Save the new item
$item->save();


}
catch (PodioError $e) {
  // Something went wrong. Examine $e->body['error_description'] for a description of the error.
    echo $e;
}



////////////////GET RESPONSE 
/**
*FOR GET RESPONSE 
*
*
*/



//AUTHENTICATION


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api.getresponse.com/v3/accounts");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Auth-Token: api-key edd91283845856ad6863a3ee76a421c9'
    ));

echo curl_exec ($ch);

curl_close ($ch);
//AUTHENTICATION



var_dump($result);


///////////////GET RESPONSE ENDS



function is_iterable($var)
{
    return $var !== null 
        && (is_array($var) 
            || $var instanceof Traversable 
            || $var instanceof Iterator 
            || $var instanceof IteratorAggregate
            );
}

/**
* AIESEC GIS Form Submission via cURL
* 
* This is a basic form processor to create new users for the Opportunities Portal
* so you can create and manage a registration form on your country website.
* This was created for @Germany so the same data could also be submitted to
* Salesforce, reducing workload.
*
* Expects data via POST:
* first_name
* last_name
* email
* password
* local committee
*
* Result:
* Forwards user to thank-you page
* User receives "welcome to OP email"
* User visible in EXPA
* 
*/

// UNCOMMENT HERE: to view the data submitted from your registration form
// echo "<h2>Form submit</h2>";
// echo '<pre>';
// print_r($_POST);
// echo "</pre>";

// use curl to GET authenticity token & cookie from GIS
// save cookie & token with unique name for use in POST
// TODO: figure out a way to clear cookies - will fill temp dir very quickly
//$cookie_jar = tempnam('/tmp','registration_cookie'); // lol windows server
//$url = "https://auth.aiesec.org/users/sign_in";
//$ch1 = curl_init();
//curl_setopt($ch1, CURLOPT_URL, $url);
//curl_setopt($ch1, CURLOPT_COOKIEJAR, $cookie_jar); // save cookie
//curl_setopt($ch1, CURLOPT_RETURNTRANSFER, TRUE); // return result HTML
//curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, TRUE); // follow 302 redirects
//curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); // TODO: FIX SSL - see below
// curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 2);
// curl_setopt($ch1, CURLOPT_CAINFO, getcwd() . "\CACerts\VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
//$result = curl_exec($ch1);

// UNCOMMENT HERE: to view the HTML form requested from the GIS
//print $result;


$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://auth.aiesec.org/users/sign_in',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));
// Send the request & save response to $resp
$result = curl_exec($curl);


// Close request to clear up some resources
curl_close($curl);

// extract token from cURL result
preg_match('/<meta content="(.*)" name="csrf-token" \/>/', $result, $matches);
$gis_token = $matches[1];


// UNCOMMENT HERE: to view HTTP status and errors from curl
// curl_errors($ch1);

//close connection
curl_close($ch1);

// map LC name -> GIS ID
// we use javascript to map uni<->LC, so the first step is already taken care of
$lc_json = 'lc_id.json';

$json = file_get_contents($lc_json, false, stream_context_create($arrContextOptions)); 
$lc_gis_map = json_decode($json,true); 

/*foreach($leads as $key => $value){
    $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
}*/

$user_lc = $lc_gis_map[$_POST['localcommittee']];


// structure data for GIS
// form structure taken from actual form submission at auth.aiesec.org/user/sign_in

$configs = include('config.php');
$fields = array(
    'authenticity_token' => htmlspecialchars($gis_token),
    'user[email]' => htmlspecialchars($_POST['email']),
    'user[first_name]' => htmlspecialchars($_POST['first_name']),
    'user[last_name]' => htmlspecialchars($_POST['last_name']),
    'user[password]' => htmlspecialchars($_POST['password']),
    'user[phone]' => htmlspecialchars($_POST['phone']),
    'user[country]' => $configs["country_name"], //'POLAND', // EXAMPLE: 'GERMANY' 
    'user[mc]' => $configs["mc_id"], //'1626', // EXAMPLE: 1596
    'user[lc_input]' => $user_lc,
    'user[lc]' => $user_lc,
    'commit' => 'REGISTER'
    );


// UNCOMMENT HERE: to view the array which will be submitted to GIS
// echo "<h2>Text going to GIS</h2>";
// echo '<pre>';
// print_r($fields);
// echo "</pre>";

//url-ify the data for the POST
$fields_string = "";
foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
rtrim($fields_string, '&');
$innerHTML = "";
/* UNCOMMENT THIS BLOCK: to enable real GIS form submission*/


// POST form with curl
$url = "https://auth.aiesec.org/users";
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_POST, count($fields));
curl_setopt($ch2, CURLOPT_POSTFIELDS, $fields_string);

curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
// give cURL the SSL Cert for Salesforce
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // TODO: FIX SSL - VERIFYPEER must be set to true
//
// "without peer certificate verification, the server could use any certificate,
// including a self-signed one that was guaranteed to have a CN that matched 
// the server’s host name."
// http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
// 
// curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 2);
// curl_setopt($ch2, CURLOPT_CAINFO, getcwd() . "\CACerts\VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
$result = curl_exec($ch2);

curl_errors($ch2);
//close connection
curl_close($ch2);
//echo $result;
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($result);    
libxml_clear_errors();
$selector = new DOMXPath($doc);

$result = $selector->query('//div[@id="error_explanation"]');

$children = $result->item(0)->childNodes;
if (is_iterable($children))
{
    foreach ($children as $child) {
        $tmp_doc = new DOMDocument();
        $tmp_doc->appendChild($tmp_doc->importNode($child,true));  
        $innerHTML .= strip_tags($tmp_doc->saveHTML());
        //$innerHTML.add($tmp_doc->saveHTML());
    }
}

$innerHTML = preg_replace('~[\r\n]+~', '', $innerHTML);
$innerHTML = str_replace(array('"', "'"), '', $innerHTML);
//echo $innerHTML;

// loop through all found items
//foreach($result as $node) {
    //echo "sdafasdfasfd".$node->getAttribute('h2');
    //var_dump($result[0]);
//}
//





/*END UNCOMMENT BLOCK 
if ($innerHTML !== ''){
    $website_url = $_POST['website_url'];
    if(strpos($website_url, '?')!=null){
        header("Location: http://aiesec.org.mx/registro/&error=".$innerHTML);
    } else {
        header("Location: http://aiesec.org.mx/registro/?error=".$innerHTML);
    }
}else {
    $website_url = $_POST['website_url'];
    if(strpos($website_url, '?')!=null){
        header("Location: http://aiesec.org.mx/registro/&thank_you=true");
    } else {
        header("Location: http://aiesec.org.mx/registro/?thank_you=true");
    }
}
*/

 header("Location: http://aiesec.org.mx/registro/?thank_you=true");





function curl_errors($ch)
{
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errno= curl_errno($ch);
    //echo "<h2>cURL errors</h2>";
    //echo $http_status."<br>";
    //echo $curl_errno.": ".curl_error($ch)."<br>";
}
?>