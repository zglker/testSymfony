<?php
namespace Console\Services;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendEmailService
{
    const HOST = 'smtp.gmail.com';
    const USERNAME = 'xxxx@xxx.xx';
    const PASSWORD = '';

    public function send(string $emailAddress, string $fileName, string $format): string
    {
        try {
            $mail = new PHPMailer(true);
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = self::HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = self::USERNAME;                     //SMTP username
            $mail->Password   = self::PASSWORD;                               //SMTP password
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPSecure = "tls";

            $mail->IsHTML(true);
            //Recipients
            $mail->SetFrom('zglker@gmail.com', 'Dennis Zhang'); //Name is optional
            $mail->Subject   = 'Order Summary';
            $mail->Body      = 'Please have a look at attachment. Thanks';
            $mail->AddAddress($emailAddress);

            $file_to_attach = $fileName.'.'.$format;

            $mail->AddAttachment( $file_to_attach , $fileName.'.'.$format );

            //Content
            $mail->Subject   = 'Order Summary';
            $mail->Body      = 'Please have a look at attachment. Thanks';
            return $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        return false;
    }


    public function getOrderTotal(array $items): float
    {
        $total = 0;
        foreach($items as $item) {
            $total += floatval($item->quantity) * floatval($item->unit_price);
        }
        return $total;
    }

    public function getOrderTotalWithDiscount(float $total, $discounts) : string
    {
        $discountsArray = (array)$discounts;
        if(count($discountsArray) > 0) {
            usort($discountsArray, function ($a, $b) {
                return $a->priority <=> $b->priority;
            });
            foreach($discountsArray as $discount) {
                switch ($discount->type) {
                    case 'DOLLAR':
                        $total = bcsub($total, $discount->value, 2);
                        break;
                    case 'PERCENTAGE':
                        $total = $total * (100 - intval($discount->value)) / 100;
                        break;
                    default:
                        break;
                }
            }
        }
        return number_format($total, 2);
    }

    public function getAverageUnitPrice(float $total, $totalUnits) : float
    {
        return bcdiv($total, $totalUnits,2);
    }

    public function getTotalUnitsCount($items) : int
    {
        return array_reduce($items, function($carry, $item) {
            $carry += $item->quantity;
            return $carry;
        }, 0);
    }


}