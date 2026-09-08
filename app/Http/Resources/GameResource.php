<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return [
        'id' => $this->id,
        'creator_id' => $this->creator_id,
        'hint_text' => $this->hint->text,
        'stake' => $this->stake->amount,
        'status' => $this->status,

        'color' => $this->when(
            $this->status !== 'open',
            $this->color
        ),
    ];
    }
}
