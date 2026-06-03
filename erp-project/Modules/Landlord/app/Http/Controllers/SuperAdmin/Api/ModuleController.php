<?php

namespace Modules\Landlord\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ModuleController extends Controller
{
    public function index()
    {
        $modulesPath = base_path('Modules');
        $result = [];

        if (File::isDirectory($modulesPath)) {
            $directories = File::directories($modulesPath);
            foreach ($directories as $dir) {
                $jsonPath = $dir . '/module.json';
                if (File::exists($jsonPath)) {
                    $config = json_decode(File::get($jsonPath), true);
                    $name = $config['name'] ?? basename($dir);
                    $alias = $config['alias'] ?? strtolower(basename($dir));
                    $description = $config['description'] ?? '';

                    $result[] = [
                        'key'         => $alias,
                        'name'        => $name,
                        'description' => $description,
                        'label'       => [
                            'en' => $name,
                            'ar' => $name,
                            'fr' => $name,
                        ],
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }
}
