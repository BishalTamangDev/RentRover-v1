<?php

if (isset($_GET['userId']) && isset($_GET['task']) && isset($_GET['roomId']) && isset($_GET['url'])) {
    if ($_GET['userId'] != '' && $_GET['task'] != '' && $_GET['roomId'] != '' && $_GET['url'] != '') {
        $url = $_GET['url'];

        include_once '../../../Class/wishlist_class.php';
        
        $wishlist = new Wishlist();

        $wishlist->setWish($_GET['userId'], $_GET['roomId']);

        if ($_GET['task'] == 'add')
            $wishlist->addWish($url);
        else
            $wishlist->removeWish($url);

        header("location: $url");
    } else {
        header("location: ../");
    }
}else{
    header("location: ../");
}


// include_once '../../Class/wishlist_class.php';

// $url = $_GET['url'];

// $wishlist = new Wishlist();
// $wishlist->setWish($_GET['userId'], $_GET['roomId']);

// if ($_GET['task'] == 'add')
//     $wishlist->addWish($url);
// else
//     $wishlist->removeWish($url);