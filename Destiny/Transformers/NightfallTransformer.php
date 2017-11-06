<?php

class NightfallTransformer
{
    public function __invoke(array $milestone): array
    {
        $imageUrl = array_get($milestone, 'availableQuests.0.pgcr_image', '');

        return [
            'title'     => array_get($milestone, 'availableQuests.0.activity.displayProperties.name', ''),
            'text'      => array_get($milestone, 'availableQuests.0.activity.displayProperties.description', ''),
            'image_url' => empty($imageUrl) ? $imageUrl : 'https://www.bungie.net' . $imageUrl,
            'fields'    => $this->buildChallengesArray($milestone),
            'color'     => '#526283',
        ];
    }

    private function buildChallengesArray($milestone)
    {
        return collect(array_get($milestone, 'availableQuests.0.challenges'))->filter(function ($challenge) use (
            $milestone
        ) {
            return array_get($challenge, 'activityHash') === array_get($milestone,
                    'availableQuests.0.activity.activityHash');
        })->map(function (array $challenge) {
            return [
                'title' => array_get($challenge, 'displayProperties.name', ''),
                'value' => array_get($challenge, 'displayProperties.description', ''),
                'short' => true,
            ];
        })->toArray();
    }
}
