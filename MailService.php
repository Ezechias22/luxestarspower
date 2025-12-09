<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;
    
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    private function configure()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = env('MAIL_HOST');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = env('MAIL_USERNAME');
            $this->mailer->Password = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $this->mailer->Port = env('MAIL_PORT', 587);
            $this->mailer->setFrom(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME', config('app.name'))
            );
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            logger()->error('Mail configuration failed', ['error' => $e->getMessage()]);
        }
    }
    
    public function send($to, $subject, $template, $data = [])
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->renderTemplate($template, $data);
            
            $sent = $this->mailer->send();
            $this->mailer->clearAddresses();
            
            return $sent;
        } catch (Exception $e) {
            logger()->error('Mail sending failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function renderTemplate($template, $data)
    {
        extract($data);
        ob_start();
        $path = base_path("views/{$template}.php");
        if (file_exists($path)) {
            require $path;
        } else {
            return "Template not found: {$template}";
        }
        return ob_get_clean();
    }
    
    public function sendPurchaseConfirmation($order)
    {
        global $db;
        
        $stmt = $db->prepare("
            SELECT p.title, p.type
            FROM products p
            WHERE p.id = ?
        ");
        $stmt->execute([$order['product_id']]);
        $product = $stmt->fetch();
        
        $downloadService = new DownloadService();
        $downloadLink = $downloadService->generateDownloadLink($order['id']);
        
        return $this->send(
            $order['buyer_email'],
            __('mail.purchase_confirmation_subject'),
            'emails/purchase-confirmation',
            [
                'order' => $order,
                'product' => $product,
                'downloadUrl' => $downloadLink['download_url'] ?? '#',
            ]
        );
    }
    
    public function sendRefundNotification($order)
    {
        return $this->send(
            $order['buyer_email'],
            __('mail.refund_notification_subject'),
            'emails/refund-notification',
            ['order' => $order]
        );
    }
    
    public function sendPayoutNotification($sellerId, $payout)
    {
        global $db;
        $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$sellerId]);
        $user = $stmt->fetch();
        
        if (!$user) return false;
        
        return $this->send(
            $user['email'],
            __('mail.payout_notification_subject'),
            'emails/payout-notification',
            ['payout' => $payout]
        );
    }
}
