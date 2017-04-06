<?php
echo 'before includes<br>';
include_once '/lib/podio-php-4.3.0/PodioAPI.php';
echo 'before requires <br>';
require '/home/webmaster/wp-config-files/vendor/google/recaptcha/src/autoload.php';
//private keys config files
echo 'before configs <br>';
$configs_external = include('/home/webmaster/wp-config-files/wp_cop_login_config.php');
$crm_configs = include('/home/webmaster/wp-config-files/crm_config.php');

//Podio submit
// This is to test the conection with the podio API and the authentication
if (check_captcha()){
send_to_podio();
send_to_cop();
save_to_crm();
header("Location: http://aiesec.org.mx/registro_cop/?thank_you=true");

}else{
  header("Location: http://aiesec.org.mx/registro_no");
}

/**
 * this method check if the recaptcha validations is wokring 
 * @return bool true if captcha worked
 */
function check_captcha(){
  global $crm_configs;
  global $configs_external;
  $recaptcha = new \ReCaptcha\ReCaptcha($configs_external['recaptcha_secret']);
  $resp = $recaptcha->verify($_POST['g-recaptcha-response'], get_client_ip());
  if (!$resp->isSuccess()) {
    //$errors = $resp->getErrorCodes();
    return false;
  }
  return true;
}

/**
 * send the regiter to podio
 * @return 
 */
function send_to_podio(){
  global $configs_external;
  Podio::setup('test-aiesec-mexico', $configs_external['podio_key']);
  try {
    Podio::authenticate_with_app(intval($configs_external['podio_space_cop_id']),$configs_external['podio_space_cop_key']);
    $podio_id = intval($configs_external['podio_space_cop_id']);
    $fields = new PodioItemFieldCollection(array(
      new PodioTextItemField(array("external_id" => "title", "values" => $_POST['first_name'] )),
      new PodioTextItemField(array("external_id" => "apellido", "values" => $_POST['last_name'])),
      new PodioEmailItemField(array(
        'external_id' => "email",
        'values' => array(
          array('type' => 'other', 'value' => $_POST['email']))
        )),
      new PodioPhoneItemField(array(
        'external_id' => "telefono",
        'values' => array(
          array('type' => 'work', 'value' => $_POST['phone'])
          )
        )),
      new PodioTextItemField(array("external_id" => "nombre-de-la-compania", "values" => $_POST['company_name'])),
      new PodioTextItemField(array("external_id" => "tu-puesto", "values" => $_POST['position'])),
      new PodioTextItemField(array("external_id" => "ciudad", "values" => $_POST['city'])),
      new PodioTextItemField(array("external_id" => "en-que-te-podemos-ayudar", "values" => $_POST['how_can_we_help'])),
      new PodioCategoryItemField(array("external_id" => "tipo-de-organizacion-2", "values" => intval($_POST['company-type']))),
      new PodioCategoryItemField(array("external_id" => "source", "values" => intval($_POST['source'])))
      ));
$item = new PodioItem(array('app' => new PodioApp($podio_id),   'fields' => $fields));
$item->save();


}catch (PodioError $e) {
  // Something went wrong. Examine $e->body['error_description'] for a description of the error.
  echo $e;
}


}



/**
 * sends the register to COP 
 * @return 
 */
function send_to_cop(){
  global $configs_external;
  // map LC name -> GIS ID
  // we use javascript to map uni<->LC, so the first step is already taken care of
  $org_type_json = 'companies_gis.json';

  $json = file_get_contents($org_type_json, false, stream_context_create($arrContextOptions)); 
  $org_map = json_decode($json,true); 

  /*foreach($leads as $key => $value){
      $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
  */

    $org_type = $org_map[$_POST['company-type']];
    echo $_POST['company-type'];

  // structure data for GIS
  // form structure taken from actual form submission at auth.aiesec.org/user/sign_in

    $configs = include('config.php');
    $fields = array(
      'employee[email]' => htmlspecialchars($_POST['email']),
      'employee[first_name]' => htmlspecialchars($_POST['first_name']),
      'employee[last_name]' => htmlspecialchars($_POST['last_name']),
      'employee[organisation_name]' => htmlspecialchars($_POST['company_name']),
      'user[password]' => htmlspecialchars($_POST['password']),
      'employee[position]' => htmlspecialchars($_POST['position']),
      'employee[phone]' => htmlspecialchars($_POST['phone']),
      'employee[city]' => htmlspecialchars($_POST['city']),
      'employee[company_type]' => $org_type,
      'user[country]' => $configs["country_name"],  
      'employee[mc_id]' => $configs["mc_id"], 

      );



    $url = 'https://gis-api.aiesec.org/v2/employee_signups';

  // use key 'http' even if you send the request to https://...
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => http_build_query($fields)
        )
      );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
  if ($result === FALSE) { /* Handle error */ }




}


/**
 * gets the ip of a host
 * @return [type] [description]
 */
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


/**
 * sends the lead to the crm using the permisons of the file spceified
 * @return [type] [description]
 */
function save_to_crm(){
  global $crm_configs;
  try {

    $rootUrl = $crm_configs['rootUrl'];//'http://107.178.211.253/espocrm/api/v1/';
    $username = $crm_configs['username'];//'crm.manager@aiesec.org.mx';
    $password = $crm_configs['password'];//'tjdr6bt9';
    
    /******************************/
    /* CREATE A NEW COMPANY LEAD. */
    /******************************/
    $url = $rootUrl . 'Lead';
    $params = array(
      'salutationName' => 'Mr.',
      'firstName' => $_POST['first_name'],
      'lastName' => $_POST['last_name'],
      'emailAddress' => $_POST['email'],
      'phoneNumber' => $_POST['phone'],
      'description' => 'Compañía: ' . $_POST['company_name'] . ' Posición: ' . $_POST['position'] . ' Ciudad: ' . $_POST['city'] . ' Ayuda: ' . $_POST['how_can_we_help'],
      'status' => 'New',
      'source' => 'COP',
      'assignedUserId' => $crm_configs['assignedUserId'],
      'assignedUserName' => $crm_configs['assignedUserName']
      );
    $json_params = json_encode($params);
    
    $ch1 = curl_init();
    
    curl_setopt($ch1,CURLOPT_URL,$url);
    curl_setopt($ch1,CURLOPT_POSTFIELDS,$json_params);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic ' . base64_encode($username . ':' . $password), 'Espo-Authorization: ' . base64_encode($username . ':' . $password), 'Content-Length: '.strlen($json_params)));
    curl_setopt($ch1,CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch1);
    curl_close($ch1);
  } catch (exception $ex) {
    echo $ex->getMessage();
  }

}


?>