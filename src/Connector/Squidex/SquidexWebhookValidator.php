<?php


namespace Efrogg\ContentRenderer\Connector\Squidex;


class SquidexWebhookValidator
{
    public static function validateRequest($requestBody,$secret,$requestSignature)
    {
        $controlHash = base64_encode(hash('sha256', $requestBody.$secret,true));
        return $controlHash === $requestSignature;
    }
}