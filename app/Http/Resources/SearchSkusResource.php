<?php


namespace App\Http\Resources;


use App\Services\TransformImagePathService\TransformImagePathService;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchSkusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $transformImagePathService = app(TransformImagePathService::class);
        return [
            'id' => $this->id,
            'sku_id' => $this->sku_id,
            'volume' => $this->volume,
            'name' => $this->name,
            'sku_code' => $this->sku_code,
            'image' => $transformImagePathService->getDestinationPath($this->image, 'small'),
        ];
    }
}
