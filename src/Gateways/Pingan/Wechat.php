<?php
namespace Runner\NezhaCashier\Gateways\Pingan;

use Runner\NezhaCashier\Requests\Charge;
use Runner\NezhaCashier\Requests\Close;
use Runner\NezhaCashier\Requests\Query;
use Runner\NezhaCashier\Requests\Refund;

abstract class Wechat extends AbstractPinganGateway
{
    protected function doQuery(array $response, Query $form): array
    {
        $tradeResult = json_decode($response['trade_result'], true);

        return [
            'order_id' => $response['out_no'],
            'status' => $this->normalizeStatus($response['status']),
            'trade_sn' => $response['ord_no'],
            'buyer_identifiable_id' => $tradeResult['openid'] ?? '',
            'buyer_is_subscribed' => (isset($tradeResult['is_subscribe']) ? ('Y' === $tradeResult['is_subscribe'] ? 'yes' : 'no') : 'no'),
            'amount' => $response['trade_amount'],
            'buyer_name' => '',
            'paid_at' => !empty($response['trade_time']) ? $response['trade_time'] : null,
            'raw' => $response,
        ];
    }

    public function chargeNotify(array $receives): array
    {
        $tradeResult = json_decode($receives['trade_result'], true);
        return [
            'order_id' => $receives['out_no'],
            'status' => 'paid',
            'trade_sn' => $receives['ord_no'],
            'buyer_identifiable_id' => $tradeResult['openid'],
            'buyer_is_subscribed' => 'N' === $tradeResult['is_subscribe'] ? 'no' : 'yes',
            'amount' => $receives['amount'],
            'buyer_name' => '',
            'paid_at' => $this->normalizePayTime($receives['pay_time']),
            'raw' => $receives,
        ];
    }
}