<?php
namespace hiperesp\server\vo;

class CharacterVO extends ValueObject {

    public readonly int $id;

    public readonly int $userId;

    public readonly string $createdAt;
    public readonly string $updatedAt;

    public readonly string $name;

    public readonly int $level;
    public readonly int $experience;
    public readonly int $experienceToLevel;

    public readonly int $hitPoints;
    public readonly int $manaPoints;

    public readonly int $silver;
    public readonly int $gold;
    public readonly int $gems;
    public readonly int $coins;

    public readonly int $maxBagSlots;
    public readonly int $maxBankSlots;
    public readonly int $maxHouseSlots;
    public readonly int $maxHouseItemSlots;

    public readonly bool $dragonAmulet;

    public readonly string $gender;
    public readonly string $pronoun;

    public readonly int $hairId;
    public readonly string $colorHair;
    public readonly string $colorSkin;
    public readonly string $colorBase;
    public readonly string $colorTrim;

    public readonly int $questId;

    public readonly int $strength;
    public readonly int $dexterity;
    public readonly int $intelligence;
    public readonly int $luck;
    public readonly int $charisma;
    public readonly int $endurance;
    public readonly int $wisdom;

    public readonly int $skillPoints;
    public readonly int $statPoints;

    public readonly string $lastDailyQuestDone;

    public readonly string $armor;
    public readonly string $skills;
    public readonly string $quests;

    public readonly int $raceId;
    public readonly int $classId;
    public readonly int $baseClassId;

    public function __construct(array $char) {
        $char['colorHair'] = \hexdec($char['colorHair']);
        $char['colorSkin'] = \hexdec($char['colorSkin']);
        $char['colorBase'] = \hexdec($char['colorBase']);
        $char['colorTrim'] = \hexdec($char['colorTrim']);
        parent::__construct($char);
    }

    public function getAccessLevel(): int {
        return $this->dragonAmulet ? 1 : 0;
    }

    public function getEquippable(): string {
        $equippable = [
            "Sword", "Mace", "Dagger", "Axe", "Ring", "Necklace", "Staff", "Belt", "Earring", "Bracer",
            "Pet", "Cape", "Wings", "Helmet", "Armor", "Wand", "Scythe", "Trinket", "Artifact"
        ];
        return \implode(",", $equippable);
    }

    public function getDailyQuestAvailable(): bool {
        $today = \date('Y-m-d');
        return $this->lastDailyQuestDone != $today;
    }

}
