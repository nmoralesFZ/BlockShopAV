<?php

declare(strict_types=1);

namespace bsalkaedaav\bsalkaedaavhcf\command;

use bsalkaedaav\bsalkaedaavhcf\entity\BlockShopEntity;
use bsalkaedaav\bsalkaedaavhcf\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

use alkaedaav\Factions;
use alkaedaav\player\Player;

class BlockShopCommand extends Command
{

    /**
     * BlockShopCommand construct.
     */
    public function __construct()
    {
        parent::__construct('blockshop', 'Command for blockshop');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (!Factions::isSpawnRegion($sender))
            return;
        /*if (Loader::getClaimManager()->getClaimType($sender->getPosition()) !== ClaimManager::SPAWN)
            return;*/
        if (isset($args[0]) && $sender->hasPermission('npc.blockshop.command')) {
            if ($args[0] === 'npc') {
                $nbt = Entity::createBaseNBT($sender->asVector3(), null, $sender->getYaw(), $sender->getPitch());
                $nbt->setTag(new CompoundTag('Skin', [
                    'Data' => new StringTag('Data', $sender->getSkin()->getSkinData()),
                    'Name' => new StringTag('Name', $sender->getSkin()->getSkinId())
                ]));
                $entity = new BlockShopEntity($sender->getLevelNonNull(), $nbt);
                $entity->spawnToAll();
                return;
            }
        }
        Utils::openBlockShop($sender);
    }
}