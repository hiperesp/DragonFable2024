<?php
// [WIP]
var_dump(convert("shop", __DIR__."/shops/shop2.xml"));die;
var_dump(convert("quest", __DIR__."/quests/quest54.xml"));

function convert($type, $file) {
    $xml = simplexml_load_file($file);
    $xmlJsonStr = \json_encode($xml, JSON_PRETTY_PRINT);
    $xmlJson = \json_decode($xmlJsonStr, true); // fast way to get xml props as array

    $out = [];

    if($type=="quest") {

        $out["quest"]         = [];
        $out["monster"]       = [];
        $out["quest_monster"] = [];
        $out["race"]          = [];

        if(!isset($xmlJson["quest"][0])) {
            $xmlJson["quest"] = [$xmlJson["quest"]];
        }
        foreach($xmlJson["quest"] as $quest) {
            if(!isset($quest["monster"])) {
                $quest["monster"] = [];
            }
            if(!isset($quest["monster"][0])) {
                $quest["monster"] = [$quest["monster"]];
            }

            $questAdd = [
                "id"              => (int)$quest['@attributes']['QuestID'],
                "name"            =>      $quest['@attributes']['strName'],
                "description"     =>      $quest['@attributes']['strDescription'],
                "complete"        =>      $quest['@attributes']['strComplete'],
                "swf"             =>      $quest['@attributes']['strFileName'],
                "swfX"            =>      $quest['@attributes']['strXFileName'],
                "maxSilver"       => (int)$quest['@attributes']['intMaxSilver'],
                "maxGold"         => (int)$quest['@attributes']['intMaxGold'],
                "maxGems"         => (int)$quest['@attributes']['intMaxGems'],
                "maxExp"          => (int)$quest['@attributes']['intMaxExp'],
                "minTime"         => (int)$quest['@attributes']['intMinTime'],
                "counter"         => (int)$quest['@attributes']['intCounter'],
                "extra"           =>      $quest['@attributes']['strExtra'],
                "dailyIndex"      => (int)$quest['@attributes']['intDailyIndex'],
                "dailyReward"     => (int)$quest['@attributes']['intDailyReward'],
                "monsterMinLevel" => (int)$quest['@attributes']['intMonsterMinLevel'],
                "monsterMaxLevel" => (int)$quest['@attributes']['intMonsterMaxLevel'],
                "monsterType"     =>      $quest['@attributes']['strMonsterType'],
                "monsterGroupSwf" =>      $quest['@attributes']['strMonsterGroupSwf'],
            ];
            $out["quest"][] = $questAdd;

            foreach($quest["monster"] as $monster) {
                $monsterAdd = [
                    "id"            =>    (int)$monster['@attributes']['MonsterID'],
                    "name"          =>         $monster['@attributes']['strCharacterName'],
                    "level"         =>    (int)$monster['@attributes']['intLevel'],
                    "experience"    =>    (int)$monster['@attributes']['intExp'],
                    "hitPoints"     =>    (int)$monster['@attributes']['intHP'],
                    "manaPoints"    =>    (int)$monster['@attributes']['intMP'],
                    "silver"        =>    (int)$monster['@attributes']['intSilver'],
                    "gold"          =>    (int)$monster['@attributes']['intGold'],
                    "gems"          =>    (int)$monster['@attributes']['intGems'],
                    "coins"         =>    (int)$monster['@attributes']['intDragonCoins'],
                    "gender"        =>         $monster['@attributes']['strGender'],
                    "hairStyle"     => \dechex($monster['@attributes']['intHairStyle']),
                    "colorHair"     => \dechex($monster['@attributes']['intColorHair']),
                    "colorSkin"     => \dechex($monster['@attributes']['intColorSkin']),
                    "colorBase"     => \dechex($monster['@attributes']['intColorBase']),
                    "colorTrim"     => \dechex($monster['@attributes']['intColorTrim']),
                    "strength"      =>    (int)$monster['@attributes']['intStr'],
                    "dexterity"     =>    (int)$monster['@attributes']['intDex'],
                    "intelligence"  =>    (int)$monster['@attributes']['intInt'],
                    "luck"          =>    (int)$monster['@attributes']['intLuk'],
                    "charisma"      =>    (int)$monster['@attributes']['intCha'],
                    "endurance"     =>    (int)$monster['@attributes']['intEnd'],
                    "wisdom"        =>    (int)$monster['@attributes']['intWis'],
                    "element"       =>         $monster['@attributes']['strElement'],
                    "raceId"        =>    (int)$monster['@attributes']['RaceID'],
                    "armorId"       =>         NULL,
                    "weaponId"      =>         NULL,
                    "movName"       =>         $monster['@attributes']['strMovName'],
                    "swf"           =>         $monster['@attributes']['strMonsterFileName'],

                    "#armor" => [
                        "id"            =>      NULL, // auto generated for now or verify if exists at shop
                        "name"          =>      $monster['@attributes']['strArmorName'],
                        "description"   =>      $monster['@attributes']['strArmorDescription'],
                        "designInfo"    =>      $monster['@attributes']['strArmorDesignInfo'],
                        "resists"       =>      $monster['@attributes']['strArmorResists'],
                        "defenseMelee"  => (int)$monster['@attributes']['intDefMelee'],
                        "defensePierce" => (int)$monster['@attributes']['intDefPierce'],
                        "defenseMagic"  => (int)$monster['@attributes']['intDefMagic'],
                        "parry"         => (int)$monster['@attributes']['intParry'],
                        "dodge"         => (int)$monster['@attributes']['intDodge'],
                        "block"         => (int)$monster['@attributes']['intBlock'],
                    ],
                    "#weapon" => [
                        "id"            =>      NULL, // auto generated for now or verify if exists at shop
                        "name"          =>      $monster['@attributes']['strWeaponName'],
                        "description"   =>      $monster['@attributes']['strWeaponDescription'],
                        "designInfo"    =>      $monster['@attributes']['strWeaponDesignInfo'],
                        "resists"       =>      $monster['@attributes']['strWeaponResists'],
                        "level"         =>      0, // default
                        "icon"          =>      "", // default
                        "type"          =>      $monster['@attributes']['strType'],
                        "itemType"      =>      "", // default
                        "critical"      => (int)$monster['@attributes']['intCrit'],
                        "damageMin"     => (int)$monster['@attributes']['intDmgMin'],
                        "damageMax"     => (int)$monster['@attributes']['intDmgMax'],
                        "bonus"         => (int)$monster['@attributes']['intBonus'],
                        "swf"           =>      $monster['@attributes']['strWeaponFile'],
                    ]
                ];

                $out["monster"][] = $monsterAdd;
                $out["quest_monster"][] = [
                    "questId"   => (int)$quest['@attributes']['QuestID'],
                    "monsterId" => (int)$monster['@attributes']['MonsterID'],
                ];
                $out["race"][] = [
                    "id"        => (int)$monster['@attributes']['RaceID'],
                    "name"      =>      $monster['@attributes']['strRaceName'],
                    "resists"   =>      "", // default
                ];
            }
        }
    } else if($type == "shop") {

        $out["shop"]      = [];
        $out["item"]      = [];
        $out["shop_item"] = [];

        if(!isset($xmlJson["shop"][0])) {
            $xmlJson["shop"] = [$xmlJson["shop"]];
        }
        foreach($xmlJson["shop"] as $shop) {
            if(!isset($shop["items"])) {
                $shop["items"] = [];
            }
            if(!isset($shop["items"][0])) {
                $shop["items"] = [$shop["items"]];
            }

            $shopAdd = [
                'id'    => (int)$shop['@attributes']['ShopID'],
                'name'  =>      $shop['@attributes']['strCharacterName'],
                'count' => (int)$shop['@attributes']['intCount'],
                '#items' => [],
            ];

            foreach($shop['items'] as $item) {
                $out["item"][] = [
                    "id"            =>    (int)$item['@attributes']['ItemID'],
                    "name"          =>         $item['@attributes']['strItemName'],
                    "description"   =>         $item['@attributes']['strItemDescription'],
                    "visible"       =>    (int)$item['@attributes']['bitVisible'],
                    "destroyable"   =>    (int)$item['@attributes']['bitDestroyable'],
                    "sellable"      =>    (int)$item['@attributes']['bitSellable'],
                    "dragonAmulet"  =>    (int)$item['@attributes']['bitDragonAmulet'],
                    "currency"      =>    (int)$item['@attributes']['intCurrency'],
                    "cost"          =>    (int)$item['@attributes']['intCost'],
                    "maxStackSize"  =>    (int)$item['@attributes']['intMaxStackSize'],
                    "bonus"         =>    (int)$item['@attributes']['intBonus'],
                    "rarity"        =>    (int)$item['@attributes']['intRarity'],
                    "level"         =>    (int)$item['@attributes']['intLevel'],
                    "type"          =>         $item['@attributes']['strType'],
                    "element"       =>         $item['@attributes']['strElement'],
                    "category"      =>         $item['@attributes']['strCategory'],
                    "equipSpot"     =>         $item['@attributes']['strEquipSpot'],
                    "itemType"      =>         $item['@attributes']['strItemType'],
                    "fileName"      =>         $item['@attributes']['strFileName'],
                    "icon"          =>         $item['@attributes']['strIcon'],
                    "strength"      =>    (int)$item['@attributes']['intStr'],
                    "dexterity"     =>    (int)$item['@attributes']['intDex'],
                    "intelligence"  =>    (int)$item['@attributes']['intInt'],
                    "luck"          =>    (int)$item['@attributes']['intLuk'],
                    "charisma"      =>    (int)$item['@attributes']['intCha'],
                    "endurance"     =>    (int)$item['@attributes']['intEnd'],
                    "wisdom"        =>    (int)$item['@attributes']['intWis'],
                    "damageMin"     =>    (int)$item['@attributes']['intMin'],
                    "damageMax"     =>    (int)$item['@attributes']['intMax'],
                    "defenseMelee"  =>    (int)$item['@attributes']['intDefMelee'],
                    "defensePierce" =>    (int)$item['@attributes']['intDefPierce'],
                    "defenseMagic"  =>    (int)$item['@attributes']['intDefMagic'],
                    "critical"      =>    (int)$item['@attributes']['intCrit'],
                    "parry"         =>    (int)$item['@attributes']['intParry'],
                    "dodge"         =>    (int)$item['@attributes']['intDodge'],
                    "block"         =>    (int)$item['@attributes']['intBlock'],
                    "resists"       =>         $item['@attributes']['strResists'],
                ];

                $out["shop_item"][] = [
                    "id"    => (int)$item['@attributes']['ShopItemID'], // associative key??
                    "shop"  => (int)$item['@attributes']['ShopID'],
                    "item"  => (int)$item['@attributes']['ItemID'],
                ];
            }
        }

        $out["shop"][] = $shopAdd;
    }

    return $out;
}