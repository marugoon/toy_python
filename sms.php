<?php
use Nurigo\Api\Message;
use Nurigo\Exceptions\CoolsmsException;
require_once "/home/seaofmymind/stock/vendor/coolsms/php-sdk/bootstrap.php";
$api_key = '{api_key}';
$api_secret = '{api_secret}';
$msg_type = 'SMS';
try {
    $db = new \PDO('mysql:host=localhost;dbname=slim_db', 'root', '******', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::ATTR_PERSISTENT => false));
    $handle = $db->prepare("SELECT items FROM stocks_history WHERE reg_date > DATE_ADD(NOW(), INTERVAL -135 MINUTE) AND not exists(select holiday from public_holidays where holiday = curdate())");
    $handle->execute();
    $result = $handle->fetch(\PDO::FETCH_ASSOC);
    if (empty($result)) {
        echo 'no rows';
    } else {
        $items = $result['items'];

        // $items = trim(iconv('utf-8','euc-kr',$items));
        if (strlen($items) > 72)
            $msg_type = 'LMS';
        $rest = new Message($api_key, $api_secret);
        $options = new stdClass();
        $options->to = '01190005719,01022125719'; // 수신번호
        // $options->to = '01022125719'; // 수신번호
        $options->from = '01022125719'; // 발신번호
        $options->type = $msg_type; // Message type ( SMS, LMS, MMS, ATA )
        $options->text = "{$items}"; // 문자내용
        // $options->charset = 'euckr'; // 문자내용
        // $options->app_version = 'PHP SDK 1.1';  //application name and version     
        $result = $rest->send($options);
    }
} catch (\PDOException $ex) {
    echo $ex->getMessage();
} catch(CoolsmsException $e) {
    echo $e->getMessage(); // get error message
    echo $e->getCode(); // get error code
}
