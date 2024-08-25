<?php
namespace hiperesp\server\controllers;

use hiperesp\server\attributes\Request;
use hiperesp\server\enums\Input;
use hiperesp\server\enums\Output;
use hiperesp\server\storage\Storage;
use hiperesp\server\util\DragonFableNinja2;

class Dev extends Controller {

    #[Request(
        endpoint: '/dev/sandbox',
        inputType: Input::RAW,
        outputType: Output::RAW
    )]
    public function sandbox(string $input): string {

        return \implode("\n", []);
    }

    #[Request(
        endpoint: '/dev/',
        inputType: Input::RAW,
        outputType: Output::HTML
    )]
    public function dev(string $input): string {
        $output = <<<HTML
<h1>Dev</h1>
<hr>
<div style="display: flex;">
    <fieldset>
        <legend>Ninja2</legend>
        <form action="ninja2decrypt">
            <button>Ninja2 Decrypt</button>
        </form>
        <form action="ninja2encrypt">
            <button>Ninja2 Encrypt</button>
        </form>
    </fieldset>
    <fieldset>
        <legend>Database</legend>
        <form action="database/setup">
            <button>Setup remaining collections</button>
        </form>
    </fieldset>
    <fieldset>
        <legend>Util</legend>
        <form action="sandbox">
            <button>Sandbox</button>
        </form>
    </fieldset>
</div>
HTML;
// create a group with legend with the name of the group and the endpoints
        
        return $output;
    }

    #[Request(
        endpoint: '/dev/database/setup',
        inputType: Input::RAW,
        outputType: Output::RAW
    )]
    public function databaseSetup(string $input): string {
        \ini_set('memory_limit', '16G');
        \set_time_limit(0);
        $storage = Storage::getStorage(false);
        $storage->setup();
        return "Database setup OK!";
    }

    #[Request(
        endpoint: '/dev/ninja2decrypt',
        inputType: Input::FORM,
        outputType: Output::HTML
    )]
    public function ninja2decrypt(array $input): string {
        $ninja2 = new DragonFableNinja2;

        $outputTxt = "";
        if(isset($input['input'])) {
            $outputTxt = \htmlspecialchars("{$ninja2->decrypt($input['input'])}");
        }
        return <<<HTML
        <pre>{$outputTxt}</pre>
        <form method='post'>
            <textarea name='input'></textarea><br>
            <button>Submit</button>
        </form>
        HTML;
    }

    #[Request(
        endpoint: '/dev/ninja2encrypt',
        inputType: Input::FORM,
        outputType: Output::HTML
    )]
    public function ninja2encrypt(array $input): string {
        $ninja2 = new DragonFableNinja2;

        $outputTxt = "";
        if(isset($input['input'])) {
            $outputTxt = \htmlspecialchars("{$ninja2->encrypt($input['input'])}");
        }
        return <<<HTML
        <pre>{$outputTxt}</pre>
        <form method='post'>
            <textarea name='input'></textarea><br>
            <button>Submit</button>
        </form>
        HTML;
    }

}