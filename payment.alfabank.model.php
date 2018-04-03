<?php

/**
 * Формирует данные для формы платежной системы Альфа-Банк
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    6.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2016 OOO «Диафан» (http://www.diafan.ru/)
 */
if (!defined('DIAFAN')) {
    $path = __FILE__;
    $i = 0;
    while (!file_exists($path . '/includes/404.php')) {
        if ($i == 10)
            exit;
        $i++;
        $path = dirname($path);
    }
    include $path . '/includes/404.php';
}

class Payment_alfabank_model extends Diafan {

    /**
     * Конвертирует в float результат работы функции number_format
     * @param string $number
     * @return float
     */
    private function parse_number($number) {
        $dec_point = $this->diafan->configmodules("format_price_2", "shop");
        return floatval(str_replace($dec_point, '.', preg_replace('/[^\d' . preg_quote($dec_point) . ']/', '', $number)));
    }

    private function getOrderBundle($order_id, $taxType = 0) {
        $info = $this->diafan->_shop->order_get($order_id);
        
        // цены передаются без учета скидки общей...
        $discount = 0;
        if(!empty($info['discount_summ'])) {
            $discount = $this->parse_number($info['discount_summ'])/intval($info['count']);
        }

        $result = array(
            'cartItems' => array(
                'items' => array()
            )
        );

        foreach ($info['rows'] as $i => $row) {
            $item = array();
            $item['positionId'] = $i;
            $item['name'] = $row['name'];
            $item['quantity'] = array(
                'value' => intval($row['count']),
                'measure' => (!empty($row['measure_unit']) ? $row['measure_unit'] : 'шт')
            );
            $item['itemCode'] = $row['id'];
            $item['itemPrice'] = ($this->parse_number($row['price']) - $discount) * 100;
            $item['tax'] = array('taxType' => intval($taxType));

            $result['cartItems']['items'][] = $item;
        }
        
        if(!empty($info['delivery'])) {
            $item = array();
            $item['positionId'] = count($info['rows']);
            $item['name']=$info['delivery']['name'];
            $item['quantity'] = array(
                'value' => 1,
                'measure' => 'шт'
            );
            $item['itemCode'] = $info['delivery_id'];
            $item['itemPrice'] = $this->parse_number($info['delivery']['summ']) * 100;
            $item['tax'] = array('taxType' => intval($taxType));
            
            $result['cartItems']['items'][] = $item;
        }

        return json_encode($result);
    }

    /**
     * Формирует данные для формы платежной системы "Альфа-Банк"
     * 
     * @param array $params настройки платежной системы
     * @param array $pay данные о платеже
     * @return array
     */
    public function get($params, $pay) {
        $result['text'] = $pay['text'];
        Custom::inc('plugins/httprequest/httprequest.php');

        try {
            $url = (!empty($params['test']) ? 'https://web.rbsuat.com/ab/rest/' : 'https://pay.alfabank.ru/payment/rest/');

            // регистрация заказа
            $http = DHttpRequest::post($url . 'register.do')->form(
                            array(
                                'userName' => urldecode($params['userName']),
                                'password' => urldecode($params['password']),
                                'orderNumber' => $pay['id'],
                                'amount' => intval(floatval($pay['summ']) * 100),
                                'returnUrl' => BASE_PATH . 'payment/get/alfabank/success/?' . rand(1111, 9999),
                                'failUrl' => BASE_PATH . 'payment/get/alfabank/fail/?' . rand(1111, 9999),
                                'description' => $pay['desc'],
                                'orderBundle' => $this->getOrderBundle($pay['element_id'], $params['tax']),
                                'taxSystem' => $params['taxationSystem']
                            )
                    )->acceptJson();

            $json = $http->ok() ? json_decode($http->body(), true) : null;

            if (array_key_exists('formUrl', $json)) {
                $result['link'] = $json['formUrl'];
                $result['link_name'] = $this->diafan->_('Оплатить');
                DB::query("UPDATE {payment_history} SET payment_data='%s' WHERE id='%d'", $json['orderId'], $pay['id']);
            } else {
                $result['error'] = $json['errorMessage'];
            }
        } catch (DHttpRequestException $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

}
