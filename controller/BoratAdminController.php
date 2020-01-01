<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Support\Facades\DB;
use function base_path;

class BoratAdminController extends Controller
{
    public function edit()
    {
        $validation = $this->validate($this->request, []);

        $this->addMessage('success', 'Packaged updated');

        return $this->getResponse();
    }

    public function delete()
    {
        $validation = $this->validate($this->request, []);

        $this->addMessage('success', 'Package removed');

        return $this->getResponse();
    }
}
