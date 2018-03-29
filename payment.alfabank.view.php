<?php

/**
 * Шаблон платежа через систему Альфа-Банк
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

echo '<p>'.$result["text"].'</p>';


if (!empty($result['error'])) {
    echo '<p>' . $result['error'] . '</p>';
}
if (!empty($result['link'])) {
    echo '<p><a href="' . $result['link'] . '" class="btn">'.(!empty($result['link_name']) ? $result['link_name'] : $this->diafan->_('Продолжить')).'</a></p>';
}