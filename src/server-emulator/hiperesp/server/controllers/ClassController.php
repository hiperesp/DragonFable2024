<?php declare(strict_types=1);
namespace hiperesp\server\controllers;

use hiperesp\server\attributes\Request;
use hiperesp\server\enums\Input;
use hiperesp\server\enums\Output;

class ClassController extends Controller {

    // [WIP]
    #[Request(
        endpoint: '/cf-classload.asp',
        inputType: Input::RAW,
        outputType: Output::RAW
    )]
    public function load(string $input): string {
        return "";
    }

}