<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Support\Facades\DB;
use function base_path;

/**
 * Class BoratAdminController
 * @package App\Http\Controllers
 */
class BoratAdminController extends Controller
{
    /**
     * @return mixed
     */
    public function edit()
    {
        $validation = $this->validate($this->request, []);

        $this->addMessage('success', 'Packaged updated');

        return $this->getResponse();
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $validation = $this->validate($this->request, []);

        $this->addMessage('success', 'Package removed');

        return $this->getResponse();
    }
}
