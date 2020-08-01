<?php
   class Phpmailer_library{
    function __construct()
        {
            require_once(APPPATH."third_party/PHPMailer/src/Exception.php");
            require_once(APPPATH."third_party/PHPMailer/src/PHPMailer.php");
            require_once(APPPATH.'third_party/PHPMailer/src/SMTP.php');
        }
   }
   
?>