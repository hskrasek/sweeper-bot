<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

require __DIR__ . '/vendor/autoload.php';

$logger = new Logger('sweeper-bot',
    [
        new StreamHandler('path/to/your.log'),
        new SyslogHandler('sweeper-bot')
    ]
);

$client = new Client(
    new \GuzzleHttp\Client([
        'base_uri' => 'https://www.bungie.net/Platform/Destiny2/',
        'headers'  => ['X-API-Key' => 'BUNGIE API KEY HERE'],
    ]),
    $logger
);

$milestones = $client->getMilestones();

(new \GuzzleHttp\Client())->post('SLACK CHANNEL WEBHOOK HERE',
    [
        'json' => [
            'text'        => 'Incoming transmission!',
            'attachments' => $milestones->map(function (array $milestone) use ($client) {
                return array_merge($milestone, $client->getMilestoneContent($milestone['milestoneHash']));
            })->map(function (array $milestone) use ($client)  {
                if (!array_has($milestone, 'availableQuests')) {
                    return $milestone;
                }

                foreach ($milestone['availableQuests'] as $key => $availableQuest) {
                    $milestone['availableQuests'][$key] = array_merge($availableQuest,
                        $client->getItemDefinition($availableQuest['questItemHash']));

                    if (array_has($availableQuest, 'activity')) {
                        $milestone['availableQuests'][$key]['activity'] = array_merge($availableQuest['activity'],
                            $client->getActivityDefinition($availableQuest['activity']['activityHash']));
                    }

                    if (array_has($availableQuest, 'challenges')) {
                        foreach ($availableQuest['challenges'] as $challengeKey => $challenge) {
                            $milestone['availableQuests'][$key]['challenges'][$challengeKey] = array_merge($challenge,
                                $client->getObjectiveDefinition($challenge['objectiveHash']));
                        }
                    }
                }

                return $milestone;
            })->filter(function (array $milestone) {
                return array_has($milestone, 'about');
            })->except(4253138191)->map(function (array $milestone) {
                return MilestoneTransformer::transform($milestone);
            })->filter()->toArray(),
        ],
    ]);
