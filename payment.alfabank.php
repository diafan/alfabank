<?php
/**
 * Работа с платежной системой Альфа-Банк
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    6.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2016 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
	$path = __FILE__; $i = 0;
	while(! file_exists($path.'/includes/404.php'))
	{
		if($i == 10) exit; $i++;
		$path = dirname($path);
	}
	include $path.'/includes/404.php';
}



if(empty($_GET['orderId'])) {
    Custom::inc('includes/404.php');
}

$pay_id = DB::query_result("SELECT id FROM {payment_history} WHERE payment_data='%s'",$_GET['orderId']);
if(empty($pay_id)) {
    Custom::inc('includes/404.php');
}

$pay = $this->diafan->_payment->check_pay($pay_id, 'alfabank');

// оплата прошла успешно
if ($_GET["rewrite"] == "alfabank/success")
{
	$this->diafan->_payment->success($pay, 'all');
}

$this->diafan->_payment->fail($pay);