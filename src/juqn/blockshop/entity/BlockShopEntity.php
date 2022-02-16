<?php

declare(strict_types=1);

namespace juqn\blockshop\entity;

use juqn\blockshop\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use VitalHCF\player\Player;

class BlockShopEntity extends Human
{

    public function initEntity(): void
    {
        parent::initEntity();
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize('&l&a  × NEW ×&r' . PHP_EOL . '&6Block Shop' . PHP_EOL . '&r&o&7/blockshop'));
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->setCancelled();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('remove.npc.blockshop') && $damager->getInventory()->getItemInHand()->getId() === 7) {
                    $this->kill();
                    return;
                }
                Utils::openBlockShop($damager);
            }
        }
    }
}