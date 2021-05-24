<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    /* 
        Up to you which header to send, some prefer 404 even if 
        the files does exist for security
    */
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );

    /* choose the appropriate page to redirect users */
    die( header( 'location: ../Login/index.html' ) );
}

    if(isset($_POST['login']))
    {
        if(!isset($_POST['username']) || !isset($_POST['pass']))
        {
            die('Incomplete information ! try again');
        }
        if($_POST['username'] === 'admin' && $_POST['pass'] === 'admin')
        {
            session_start();
            $_SESSION['user'] = 'admin';
            $_SESSION['pwd'] = 'admin';
            header('location: ../PAGES/');
        }
        else
        {
            header('location: ../Login/index.php?invalid=userpass');
        }
    }
?>