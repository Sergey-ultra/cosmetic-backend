<?php

namespace App\Http\Resources\Admin\Parser;

use Illuminate\Http\Resources\Json\JsonResource;

class ParsedLinkResource extends JsonResource
{
    public function toArray($request): array
    {
        $content = json_decode($this->content, true);
        return [
            'id' => $this->id,
            'title' => $content['title'] ?? '',
            'link' => $this->link,
            'date' => $this->date,
        ];
    }
}
