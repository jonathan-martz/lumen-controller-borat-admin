<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use function json_decode;

/**
 * Class BoratAdminController
 * @package App\Http\Controllers
 */
class BoratAdminController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param string $url
     * @return bool|mixed
     */
    public function checkComposerJson(string $url)
    {
        $repo = $this->getRepoByUrl($url);
        $owner = $this->getOwnerByUrl($url);

        $this->data['module'] = $repo;
        $this->data['vendor'] = $owner;
        $this->data['repo'] = $url;

        $requestUrl = str_replace(' ', '', 'https://api.github.com/repos/' . $owner . '/' . $repo . '/contents/composer.json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_USERAGENT, 'flagbit rockt');
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output);

        $data = (array)$data;

        if(!empty($data['message']) && $data['message'] == 'Not Found') {
            return false;
        }
        return $data['download_url'];
    }

    /**
     * @param string $url
     * @return array
     */
    public function loadComposerJson(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'flagbit rockt');
        $output = curl_exec($ch);
        curl_close($ch);

        return (array)json_decode($output);
    }

    /**
     * @param string $url
     * @return string
     */
    public function getRepoByUrl(string $url): string
    {
        $url = str_replace('git@github.com:', '', $url);
        $fullname = explode('/', str_replace('.git', '', $url));

        if(!empty($fullname[1])) {
            return $fullname[1];
        }

        return 'unknown';
    }

    /**
     * @param string $url
     * @return string
     */
    public function getOwnerByUrl(string $url): string
    {
        $url = str_replace('git@github.com:', '', $url);
        $fullname = explode('/', str_replace('.git', '', $url));

        if(!empty($fullname[0])) {
            return $fullname[0];
        }

        return 'unknown';
    }


    /**
     * @return JsonResponse
     * @throws ValidationException
     */
    public function confirmAdd()
    {
        $validation = $this->validate($this->request, [
            'result' => 'required|array',
            'result.module' => 'required|string',
            'result.vendor' => 'required|string',
            'result.fullname' => 'required|string',
            'result.repo' => 'required|string',
            'result.type' => 'required|string',
        ]);

        if($this->request->user()->getRoleName() === 'admin') {

            $result = DB::table('packages')->insert($this->request->input('result'));

            if($result) {
                $this->addMessage('success', 'Package added.');
            }
            else {
                $this->addMessage('error', 'Upps something went wrong.');
            }
        }
        else {
            $this->addMessage('error', 'Only admins can add Packages!');
        }


        return $this->getResponse();
    }

    /**
     * @return JsonResponse
     * @throws ValidationException
     */
    public function add()
    {
        $validation = $this->validate($this->request, [
            'url' => 'required',
            'type' => 'required'
        ]);

        $url = trim($this->request->input('url'));

        if($this->request->user()->getRoleName() === 'admin') {

            if($this->request->input('type') == 'public' || $this->request->input('type') == 'private' || $this->request->input('type') == 'proxy') {

                $check['repo'] = DB::table('packages')->where('repo', '=', $url);

                if($check['repo']->count() === 0) {
                    $exists = $this->checkComposerJson($url);

                    if($exists) {
                        $data = $this->loadComposerJson($exists);

                        if(!is_array($data)) {
                            $this->addMessage('error', 'Package doesnt contain composer.json');
                        }
                        else {
                            if(!empty($data['name'])) {
                                $check['fullname'] = DB::table('packages')->where('repo', '=', $data['name']);

                                if($check['fullname']->count() === 0) {

                                    $insert = [
                                        'vendor' => $this->data['vendor'],
                                        'module' => $this->data['module'],
                                        'fullname' => $data['name'],
                                        'repo' => $this->data['repo'],
                                        'type' => $this->request->input('type'),
                                    ];

                                    $this->addResult('confirm', $insert);
                                }
                                else {
                                    $this->addMessage('error', 'Package was Name already exists. (Id: ' . $check['fullname']->first()->id . ')');
                                }
                            }
                            else {
                                $this->addMessage('error', 'Package has no Name.');
                            }
                        }
                    }
                    else {
                        $this->addMessage('error', 'File doesnt exists.');
                    }
                }
                else {
                    $this->addMessage('error', 'Repo with already exists. (Id: ' . $check['repo']->first()->id . ')');
                }
            }
        }
        else {
            $this->addMessage('error', 'Type doesnt exists.');
        }

        return $this->getResponse();
    }

    /**
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update()
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
