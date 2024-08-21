<?php
namespace hiperesp\server\enums;

use hiperesp\server\util\DragonFableNinja2;

enum Input {

    case NINJA2;
    case XML;
    case FORM;
    case RAW;

    public function get(): mixed {
        return match($this) {
            Input::NINJA2 => $this->ninja2(),
            Input::XML => $this->xml(),
            Input::FORM => $this->form(),
            Input::RAW => $this->raw(),
        };
    }

    private function ninja2(): \SimpleXMLElement {
        $xml = \file_get_contents("php://input");
        if(\preg_match('/^<ninja2>(.+)<\/ninja2>$/', $xml, $matches)) {
            $ninja2 = new DragonFableNinja2;
            $xml = $ninja2->decrypt($matches[1]);
        }
        $output = \simplexml_load_string($xml);
        if($output===false) {
            throw new \Exception("Invalid input Ninja2 XML: {$xml}");
        }
        return $output;
    }

    private function xml(): \SimpleXMLElement {
        $xml = \file_get_contents("php://input");
        $output = \simplexml_load_string($xml);
        if($output===false) {
            throw new \Exception("Invalid input XML: {$xml}");
        }
        return $output;
    }

    private function form(): array {
        return $_POST;
    }

    private function raw(): string {
        return \file_get_contents("php://input");
    }
}