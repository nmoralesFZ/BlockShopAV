<?php

declare(strict_types=1);

namespace bsalkaedaav\bsalkaedaavhcf;

use bsalkaedaav\bsalkaedaavhcf\command\BlockShopCommand;
use bsalkaedaav\bsalkaedaavhcf\entity\BlockShopEntity;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
class BlockShop extends PluginBase
{
    use SingletonTrait;
    public function onLoad()
    {
        self::setInstance($this);
    }
    public function onEnable()
    {
        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);
        $this->getServer()->getCommandMap()->register('BlockShop', new BlockShopCommand());
        Entity::registerEntity(BlockShopEntity::class, true);
    }
}