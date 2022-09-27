<?php

declare(strict_types=1);

namespace bsalkaedaav\bsalkaedaavhcf\entity;

use bsalkaedaav\bsalkaedaavhcf\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use alkaedaav\player\Player;

class BlockShopEntity extends Human
{

    public function initEntity(): void
    {
        parent::initEntity();
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize('&l&a¡Toca para usar!&r' . PHP_EOL . '&6&l&oBlockShop' . PHP_EOL . '&r&7Usa también /blockshop'));
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