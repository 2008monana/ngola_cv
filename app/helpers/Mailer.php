<?php
// app/helpers/Mailer.php
class Mailer {
    
    public static function send($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Ngola CV <naoresponda@ngolacv.ao>" . "\r\n";
        
        return mail($to, $subject, $message, $headers);
    }
    
    public static function sendResetPassword($to, $nome, $token) {
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/ngola-cv/public/index.php?page=redefinir-senha&token=" . $token;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2c3e66; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; background: #e67e22; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Ngola CV</h2>
                </div>
                <div class='content'>
                    <h3>Olá, $nome!</h3>
                    <p>Recebemos uma solicitação para redefinir sua senha.</p>
                    <p>Clique no botão abaixo para criar uma nova senha:</p>
                    <p style='text-align: center;'>
                        <a href='$link' class='button'>Redefinir Senha</a>
                    </p>
                    <p>Se você não solicitou esta alteração, ignore este e-mail.</p>
                    <p>Este link expira em 1 hora.</p>
                </div>
                <div class='footer'>
                    <p>Ngola CV - Seu currículo profissional em Angola</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return self::send($to, "Redefinição de Senha - Ngola CV", $message);
    }
}
?>