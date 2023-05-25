<?php

namespace App\Module\ApiIntegration\Service;

use Planka\Bridge\Config;
use Planka\Bridge\Exceptions\AuthenticateException;
use Planka\Bridge\PlankaClient;
use Planka\Bridge\TransportClients\Client;
use Symfony\Component\HttpClient\NativeHttpClient;

final class PlankaApiIntegrator
{
    private PlankaClient $client;

    private bool $isAuthenticate = false;

    public function __construct(
        string $user,
        string $password,
        string $uri,
        int $port
    )
    {
        $config = new Config(user: $user, password: $password, baseUri: $uri, port: $port);
        $this->client = new PlankaClient($config);
    }

    public function getClient(): PlankaClient
    {
        return $this->client;
    }

    /**
     * @throws AuthenticateException
     */
    public function authenticate(bool $reAuth = false): void
    {
        if ($reAuth || !$this->isAuthenticate) {
            $this->client->authenticate();
            $this->isAuthenticate = true;
        }
    }
}