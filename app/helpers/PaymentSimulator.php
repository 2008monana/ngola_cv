<?php
// app/helpers/PaymentSimulator.php

class PaymentSimulator {
    /**
     * Simula um gateway de pagamento. Para ambiente de testes, aprova sempre.
     */
    public static function charge($dados) {
        return [
            'approved' => true,
            'status' => 'aprovado',
            'reference' => self::generateReference($dados['plano'] ?? 'plano'),
            'message' => 'Pagamento aprovado pelo simulador.'
        ];
    }

    public static function generateReference($prefix = 'NGCV') {
        $cleanPrefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefix));
        return 'NGCV-' . $cleanPrefix . '-' . date('YmdHis') . '-' . random_int(1000, 9999);
    }

    public static function getPlanConfig($plano) {
        $plans = [
            'premium' => [
                'nome' => 'Premium',
                'valor' => 2500,
                'descricao' => '5 currículos ativos, templates completos e exportação PDF sem marca d’água.'
            ],
            'profissional' => [
                'nome' => 'Profissional',
                'valor' => 5000,
                'descricao' => 'Currículos ilimitados, estatísticas completas e suporte prioritário.'
            ]
        ];

        return $plans[$plano] ?? null;
    }
}
?>
