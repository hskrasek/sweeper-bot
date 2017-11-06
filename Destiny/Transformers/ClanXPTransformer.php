<?php

class ClanXPTransformer
{
    public function __invoke(array $milestone): array
    {
        $imageUrl = array_get($milestone, 'availableQuests.0.pgcr_image', '');

        return [
            'title'     => array_get($milestone, 'availableQuests.0.displayProperties.name', ''),
            'text'      => array_get($milestone, 'about', ''),
            'image_url' => empty($imageUrl) ? $imageUrl : 'https://www.bungie.net' . $imageUrl,
        ];
    }
}
