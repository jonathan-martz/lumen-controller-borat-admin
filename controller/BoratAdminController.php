<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Class BoratAdminController
 * @package App\Http\Controllers
 */
class BoratAdminController extends Controller
{
    /**
     * @return JsonResponse
     * @throws ValidationException
     */
    public function edit()
    {
        $validation = $this->validate($this->request, []);

        $this->addMessage('success', 'Packaged updated');

        return $this->getResponse();
    }

    /**
     * @return JsonResponse
     * @throws ValidationException
     */
    public function delete()
    {
        $validation = $this->validate($this->request, [
            'id' => 'required|integer'
        ]);

        if($this->request->user()->getRoleName() === 'admin') {
            $packages = DB::table('packages')->where('id', '=', $this->request->input('id'));

            if($packages->count() === 1) {
                $result = DB::table('packages')->delete($this->request->input('id'));

                if($result) {
                    $this->addMessage('success', 'Package removed');
                }
                else {
                    $this->addMessage('error', 'Upps something went wrong. Try again later and Contact an Admin when the Error doesnt disappear later.');
                }
            }
            else {
                $this->addMessage('error', 'Package with id ' . $this->request->input('id') . ' doesnt exists.');
            }
        }
        else {
            $this->addMessage('error', 'Only admins can remove a package!');
        }

        return $this->getResponse();
    }
}
