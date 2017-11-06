<?php

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $log;

    public function __construct(GuzzleClient $client, LoggerInterface $log)
    {
        $this->client = $client;
        $this->log    = $log;
    }

    /**
     * @param string $endpoint
     *
     * @return array
     * @throws \Exception
     */
    public function get(string $endpoint): array
    {
        $response = $this->client->get(Str::endsWith($endpoint, '/') ? $endpoint : $endpoint . '/');

        $response = json_decode((string) $response->getBody(), true);

        if (array_get($response, 'ErrorCode') !== 1) {
            $this->log->error('destiny.api.error', [
                'message' => array_get($response, 'Message'),
                'status'  => array_get($response, 'ErrorStatus'),
            ]);

            throw new \Exception('There was an error calling the Destiny 2 API.');
        }

        return array_get($response, 'Response', []);
    }

    public function getMilestones()
    {
        return collect($this->get('Milestones/'));
    }

    public function getMilestoneContent($milestoneHash)
    {
        return $this->get('Milestones/' . $milestoneHash . '/Content/');
    }

    public function getItemDefinition($itemHash)
    {
        return $this->get('Manifest/DestinyInventoryItemDefinition/' . $itemHash . '/');
    }

    public function getActivityDefinition($activityHash)
    {
        return $this->get('Manifest/DestinyActivityDefinition/' . $activityHash . '/');
    }

    public function getObjectiveDefinition($objectiveHash)
    {
        return $this->get('Manifest/DestinyObjectiveDefinition/' . $objectiveHash . '/');
    }
}
