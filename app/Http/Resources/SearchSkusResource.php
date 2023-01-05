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
    public function toArray($request)
    {
        $transformImagePathService = app(TransformImagePathService::class);
        return [
            'id' => $this->id,
            'volume' => $this->volume,
            'name' => $this->name,
            'url' => $this->url,
            'image' => $transformImagePathService->getDestinationPath($this->image, 'small'),
            'min_price' =>$this->min_price
        ];
    }
}
