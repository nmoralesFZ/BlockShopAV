<?php

declare(strict_types=1);

namespace juqn\blockshop;

use juqn\blockshop\command\BlockShopCommand;
use juqn\blockshop\entity\BlockShopEntity;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

/**
 * Class BlockShop
 * @package juqn\blockshop
 */
class BlockShop extends PluginBase
{
    use SingletonTrait;
    
    public function onLoad()
    {
        self::setInstance($this);
    }
    
    public function onEnable()
    {
        # InvMenu
        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

        # Register command
        $this->getServer()->getCommandMap()->register('BlockShop', new BlockShopCommand());

        # Register entity
        Entity::registerEntity(BlockShopEntity::class, true);
    }
}