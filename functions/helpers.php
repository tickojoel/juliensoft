<?php
function getProductsByCategory() {
    return array(
        '1' => array('name' => 'Cuidado Facial', 'icon' => 'fas fa-spa'),
        '2' => array('name' => 'Maquillaje', 'icon' => 'fas fa-paint-brush'),
        '3' => array('name' => 'Cuidado Capilar', 'icon' => 'fas fa-cut'),
        '4' => array('name' => 'Fragancias', 'icon' => 'fas fa-wind'),
        '5' => array('name' => 'Cuidado Corporal', 'icon' => 'fas fa-hand-holding-water')
    );
}

function showNotification($message, $type = 'success') {
    $_SESSION['notification'] = array(
        'message' => $message,
        'type' => $type
    );
}

function getNotification() {
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        unset($_SESSION['notification']);
        return $notification;
    }
    return null;
}
?>