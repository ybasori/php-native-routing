<?php

namespace App\Controllers;

use App\Models\DataTypeItem;
use App\Models\Path;

class HomeController extends Controller
{

    public function index()
    {
        return $this->view("layout", [
            "title" => "Yusuf App",
            "meta" => [
                (object) [
                    "name" => "description",
                    "content" => "Yusuf App"
                ],
                (object) [
                    "name" => "keyword",
                    "content" => "HTML, CSS, Javascript, PHP"
                ],
                (object) [
                    "name" => "author",
                    "content" => "Yusuf Basori"
                ],
            ]
        ]);
    }

    public function docs()
    {
        $path = new Path;
        $data_path = $path->getAll([]);
        $this->view("layouts/base-layout/header");
        $this->view("pages/docs/index", ["data_path" => $data_path]);
        $this->view("layouts/base-layout/footer");
    }

    public function docs_any()
    {

        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestUri['path'];
        $path = new Path;
        $data_path = $path->getAll([]);

        $selectedPath = "";
        $params = [];
        $matched = false;
        foreach ($data_path as $dt) {

            if ($matched) {
                continue;
            }
            $fullpath = $dt->full_path;

            $fullpath = str_replace("\\", "\\\\", $fullpath);


            $explodePath = explode("/", $fullpath);

            $arrayPath = [];
            foreach ($explodePath as $value) {
                if ($value != "") {
                    $arrayPath[] = $value;
                }
            }

            $explodeReqPath = explode("/", $requestPath);


            $arrayReqPath = [];
            foreach ($explodeReqPath as $value) {
                if ($value != "") {
                    $arrayReqPath[] = $value;
                }
            }

            $newParams = [];

            if ($requestPath == "/" && $requestPath === $path) {
                $matched = true;
            } else {

                $skip = false;
                $any = false;

                foreach ($arrayReqPath as $key => $value) {

                    if ($skip) {
                        continue;
                    }

                    if (!empty($arrayPath[$key]) || $any) {

                        if (!$any && $arrayPath[$key] != $value) {

                            if (substr($arrayPath[$key], 0, 1) == ":") {
                                if ($arrayPath[$key] == ":any") {
                                    $any = true;
                                } else {
                                    $newParams[substr($arrayPath[$key], 1, strlen($arrayPath[$key]))] = $value;
                                }
                            } else {
                                $skip = true;
                            }
                        }
                    } else {
                        $skip = true;
                    }

                    if (count($arrayReqPath) - 1 == $key && !$any) {
                        if (!empty($arrayPath[$key + 1]) && $arrayPath[$key + 1] != "") {
                            $skip = true;
                        }
                    }
                }


                // if (!$skip && $method === $handler['method']) {
                //     if (count($arrayReqPath) != 0 && count($arrayPath) != 0) {
                //         $matched = true;
                //     }
                // }
            }



            if ($matched) {
                $selectedPath = $dt->full_path;
                $params = $newParams;
            }
        }
    }
}
