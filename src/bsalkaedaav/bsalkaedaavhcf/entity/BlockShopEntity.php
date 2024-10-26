<?php

declare(strict_types=1);

namespace bsalkaedaav\bsalkaedaavhcf\entity;

use bsalkaedaav\bsalkaedaavhcf\utils\Utils;
use pocketmine\entity\passive\Villager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class BlockShopEntity extends Villager
{
    public function initEntity(): void
    {
        parent::initEntity();
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize("&l&a¡Golpéame!&r \n &6&l&oBlockShop \n &r&7Usa también /blockshop"));
    }

    /**
     * Aplica la rotación del jugador al NPC.
     *
     * @param Player $player
     */
    public function setRotationFromPlayer(Player $player): void
    {
        $this->setRotation($player->getYaw(), $player->getPitch());
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
