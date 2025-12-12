<?php
namespace App\Services;

class EmailService {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
    }
    
    public function send($to, $subject, $body, $isHtml = true) {
        $headers = [
            'From: ' . $this->config['mail']['from_name'] . ' <' . $this->config['mail']['from'] . '>',
            'Reply-To: ' . $this->config['mail']['from'],
            'X-Mailer: PHP/' . phpversion()
        ];
        
        if ($isHtml) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        }
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    public function sendOrderConfirmation($order, $user, $product) {
        $subject = "Confirmation de commande #{$order->order_number}";
        
        $body = "
            <h2>Merci pour votre achat !</h2>
            <p>Bonjour {$user->name},</p>
            <p>Votre commande a été confirmée.</p>
            <h3>Détails :</h3>
            <ul>
                <li>Produit: {$product->title}</li>
                <li>Prix: \${$order->amount}</li>
                <li>Commande: {$order->order_number}</li>
            </ul>
            <p>Vous pouvez télécharger votre produit depuis votre compte.</p>
        ";
        
        return $this->send($user->email, $subject, $body);
    }
    
    public function sendSellerNotification($order, $seller, $product) {
        $subject = "Nouvelle vente - {$product->title}";
        
        $body = "
            <h2>Vous avez réalisé une vente !</h2>
            <p>Bonjour {$seller->name},</p>
            <p>Votre produit \"{$product->title}\" vient d'être vendu.</p>
            <h3>Détails :</h3>
            <ul>
                <li>Prix: \${$order->amount}</li>
                <li>Vos revenus: \${$order->seller_earnings}</li>
                <li>Commission: \${$order->platform_fee}</li>
            </ul>
        ";
        
        return $this->send($seller->email, $subject, $body);
    }
    
    public function sendPasswordReset($user, $token) {
        $subject = "Réinitialisation de mot de passe";
        $resetUrl = $this->config['app']['url'] . "/reset-password?token=$token";
        
        $body = "
            <h2>Réinitialisation de mot de passe</h2>
            <p>Bonjour {$user->name},</p>
            <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
            <p><a href=\"$resetUrl\">Réinitialiser mon mot de passe</a></p>
            <p>Ce lien expire dans 1 heure.</p>
            <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
        ";
        
        return $this->send($user->email, $subject, $body);
    }
}
