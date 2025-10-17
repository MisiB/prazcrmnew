<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "ticket"=>$this->ticket,
            "regnumber"=>$this->regnumber,
            "name"=>$this->name.' '.$this->surname,
            "email"=>$this->email,
            "phone"=>$this->phone,
            "title"=>$this->title,
            "description"=>$this->description,
            "category"=>$this->issuetype->name,
            "assigneedept"=>$this->task->user->department->department->name??null,
            "userId"=>$this->user_id,
            "status"=>$this->status,
            "startdate"=>$this->created_at,
            "enddate"=>$this->updated_at,
        ];
    }
}
