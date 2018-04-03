<?php

/**
 * Настройки платежной системы Альфа-Банк для административного интерфейса
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

class Payment_alfabank_admin {

    public $config;

    public function __construct() {
        $this->config = array(
            "name" => 'Альфа-Банк',
            "params" => array(
                'userName' => 'Логин магазина',
                'password' => 'Пароль магазина',
                'tax' => array(
                    'type' => 'select',
                    'name' => 'Настройки ставок НДС',
                    'help' => 'Для магазинов с настройкой фискализации',
                    'select' => array(
                        '0' => 'НДС не облагается',
                        '1' => 'ставка НДС 0%',
                        '2' => 'ставка НДС 10%',
                        '3' => 'ставка НДС 18%',
                        '4' => 'ставка НДС расч. 10/110',
                        '5' => 'ставка НДС расч. 18/118'
                    ),
                ),
                'taxationSystem' => array(
                    'type' => 'select',
                    'name' => 'Система налогообложения',
                    'help' => 'Для магазинов с настройкой фискализации',
                    'select' => array(
                        '0' => 'Общая, ОСН',
                        '1' => 'Упрощенная доход, УСН доход',
                        '2' => 'Упрощенная доход минус расход, УСН доход - расход',
                        '3' => 'Единый налог на вмененный доход, ЕНВД',
                        '4' => 'Единый сельскохозяйственный налог, ЕСН',
                        '5' => 'Патентная система налогообложения, Патент'
                    )
                ),
                'test' => array('name' => 'Тестовый режим', 'type' => 'checkbox'),
            )
        );
    }

}
