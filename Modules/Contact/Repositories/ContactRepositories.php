<?php

namespace Modules\Contact\Repositories;

use Modules\Contact\Actions\MultipleDeleteContact;

class ContactRepositories implements ContactInterface
{
    public function multipleDestroy(object $data)
    {
        return MultipleDeleteContact::delete($data);
    }
}
