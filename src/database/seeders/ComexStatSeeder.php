<?php

namespace Database\Seeders;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class ComexStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    public function run(): void
    {
        $client = ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();

        $params = [
            'index' => 'comexstat',
            'ignore_unavailable' => true,
        ];

        $client->indices()->delete($params);

        $mappings = [
            'properties' => [
                'year' => ['type' => 'integer'],
                'ncm_code' => ['type' => 'keyword'],
                'ncm_description' => ['type' => 'text'],
                'importer_state' => ['type' => 'keyword'],
                'transport_mode' => ['type' => 'keyword'],
                'country_origin' => ['type' => 'keyword'],
                'fob_value' => ['type' => 'float'],
                'net_weight' => ['type' => 'float'],
            ],
        ];

        $params = ['index' => 'comexstat', 'body' => ['mappings' => $mappings]];
        $client->indices()->create($params);

        $csv = Reader::createFromPath(storage_path('app/comex_stat_dec22_imports.csv'));
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader();
        $records = $csv->getRecords();

        $bar = $this->command->getOutput()->createProgressBar($csv->count() / 1000);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);
        $bar->start();
        $cont = 0;
        $params = ['body' => []];
        foreach ($records as $row) {
            $cont++;
            $params['body'][] = [
                'index' => [
                    '_index' => 'comexstat',
                ]
            ];
            $params['body'][] = array_combine($header, $row);
            if ($cont % 1000 == 0) {
                $bar->advance();
                $responses = $client->bulk($params);
                $params = ['body' => []];
                unset($responses);
            }
        }

        if (!empty($params['body'])) {
            $this->command->newLine();
            $responses = $client->bulk($params);
            unset($responses);
        }

        $bar->finish();
        $this->command->newLine();

        $params = ['index' => 'comexstat'];
        $response = $client->count($params);
        $this->command->info("  ".$response['count']."/".$csv->count()." documentos adicionados.");
    }
}
