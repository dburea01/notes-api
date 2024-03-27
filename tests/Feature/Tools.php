<?php

namespace Tests\Feature;

trait Tools
{
    public function getEndPoint(): string
    {
        return '/api/v1/';
    }

    public function getHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];
    }
}
