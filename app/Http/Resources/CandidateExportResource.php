<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateExportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'Name' => $this->user->name ?? 'No Name',
            'Email' => $this->user->email ?? 'No Email',
            'Role' => $this->jobRole->name ?? 'No Role',
            'Profession' => $this->profession->name ?? 'No Role',
            'Gender' => $this->gender ?? 'No Gender',
            'Website' => $this->website ?? 'No Website',
            'Number' => $this->whatsapp_number ?? 'No Number',
            'Address' => $this->Address ?? 'No Address',
        ];
    }
}
