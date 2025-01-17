<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\formatDate;

class PostResourse extends JsonResource
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
            'content' => $this->content,
            'file_type' => $this->file_type,
            'file_url' => $this->file_url,
            'date' => formatDate($this->created_at, 'LONG_DATE', 'en', true),
            'comments' => $this->comments_count,
            'watches' => $this->watches_count,
            'likes' => $this->likes,
            'user_id' => auth()->id(),
        ];
    }
}
