<?php

namespace App\Infra\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticsearchClient
{
    /**
     * @param  array  $params
     * @return Elasticsearch|Promise
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(array $params = []): Elasticsearch|Promise
    {
        return $this->getConnection()->search($params);
    }

    /**
     * @return Client
     *
     * @throws AuthenticationException
     */
    private function getConnection(): Client
    {
        $host = config('database.connections.elastic.host');
        $port = config('database.connections.elastic.port');

        return ClientBuilder::create()->setHosts(["$host:$port"])->build();
    }
}
