<?php

declare(strict_types=1);

namespace bsalkaedaav\bsalkaedaavhcf\utils;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use bsalkaedaav\bsalkaedaavhcf\blockshop;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use alkaedaav\player\Player;

final class Utils
{

    /**
     * @param Player $player
     */
    public static function openBlockShop(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => ItemFactory::get(241, 1),
            1 => ItemFactory::get(241, 1),
            7 => ItemFactory::get(241, 1),
            8 => ItemFactory::get(241, 1),
            9 => ItemFactory::get(241, 1),
            17 => ItemFactory::get(241, 1),
            36 => ItemFactory::get(241, 1),
            44 => ItemFactory::get(241, 1),
            45 => ItemFactory::get(241, 1),
            46 => ItemFactory::get(241, 1),
            52 => ItemFactory::get(241, 1),
            53 => ItemFactory::get(241, 1)
        ]);
        
        $menu->getInventory()->setItem(12, ItemFactory::get(86)->setCustomName(TextFormat::colorize('&l&6Bloques de Halloween')));
        $menu->getInventory()->setItem(13, ItemFactory::get(114)->setCustomName(TextFormat::colorize('&l&6Bloques del Infierno')));
        $menu->getInventory()->setItem(14, ItemFactory::get(79)->setCustomName(TextFormat::colorize('&l&6Bloques Navideños')));
        
        $menu->getInventory()->setItem(21, ItemFactory::get(241, 8)->setCustomName(TextFormat::colorize('&l&6Cristales de colores')));
        $menu->getInventory()->setItem(22, ItemFactory::get(155)->setCustomName(TextFormat::colorize('&l&6Bloques de cuarzo')));
        $menu->getInventory()->setItem(23, ItemFactory::get(32)->setCustomName(TextFormat::colorize('&l&6Arbustos y hojas')));
        
        $menu->getInventory()->setItem(30, ItemFactory::get(121)->setCustomName(TextFormat::colorize('&l&6Bloques del fin')));
        $menu->getInventory()->setItem(31, ItemFactory::get(108)->setCustomName(TextFormat::colorize('&l&6Bloques de piedra')));
        $menu->getInventory()->setItem(32, ItemFactory::get(159)->setCustomName(TextFormat::colorize('&l&6Bloques de arcilla')));
        
        $menu->getInventory()->setItem(39, ItemFactory::get(37)->setCustomName(TextFormat::colorize('&l&6Flores')));
        $menu->getInventory()->setItem(40, ItemFactory::get(35)->setCustomName(TextFormat::colorize('&l&6Lanas')));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $type = TextFormat::clean($item->getCustomName());
            
            if (self::getPage($type) !== null) {
                self::openPageBlockShop($player, $type);
                $player->removeWindow($transaction->getAction()->getInventory());
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&l&5Tienda &r&8-&7 ¡Compra y vende bloques!'));
    }
    
    /**
     * @param Player $player
     * @param string $type
     */
    public static function openPageBlockShop(Player $player, string $type): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => ItemFactory::get(241, 1),
            1 => ItemFactory::get(241, 1),
            7 => ItemFactory::get(241, 1),
            8 => ItemFactory::get(241, 1),
            9 => ItemFactory::get(241, 1),
            17 => ItemFactory::get(241, 1),
            36 => ItemFactory::get(241, 1),
            44 => ItemFactory::get(241, 1),
            45 => ItemFactory::get(241, 1),
            46 => ItemFactory::get(241, 1),
            52 => ItemFactory::get(241, 1),
            53 => ItemFactory::get(241, 1)
        ] + self::getPage($type));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player $player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('precio') !== null) {
                $newItem = ItemFactory::get($item->getId(), $item->getDamage(), $item->getCount());
                $newBalance = $player->getBalance() - $item->getNamedTag()->getInt('precio');
                
                if ($newBalance < 0) {
                    $player->sendMessage(TextFormat::colorize('&l&6INFO&f> &r&c¡No tienes el dinero suficiente para comprar esto!'));
                    return $transaction->discard();
                }
                
                if ($player->getInventory()->canAddItem($newItem)) {
                    $player->getInventory()->addItem($newItem);
                    $player->setBalance($newBalance);
                } else $player->sendMessage(TextFormat::colorize('&l&6INFO&f> &r¡No tienes espacio en tu inventario!'));
            }
            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function (Player $player, $inventory) {
            BlockShop::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                if ($player->isOnline())
                    self::openBlockShop($player);
            }), 1);
        });
        $menu->send($player, TextFormat::colorize('&7' . $type));
    }
    
    /**
     * @param Item $item
     * @param int $precio
     * @return Item
     */
    private static function prepareItem(Item $item, int $precio): Item
    {
        $item->setLore([TextFormat::colorize('&fPrecio: &6$' . $precio)]);
        
        $namedtag = $item->getNamedTag();
        $namedtag->setInt('precio', $precio);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @param string $type
     * @return array|null
     */
    private static function getPage(string $type): ?array
    {
        switch ($type) {
            case 'Bloques de Halloween':
                return [
                    22 => self::prepareItem(ItemFactory::get(86, 0, 16), 260),
                    31 => self::prepareItem(ItemFactory::get(91, 0, 16), 260)
                ];
            
            case 'Bloques del Infierno':
                return [
                    12 => self::prepareItem(ItemFactory::get(405, 0, 16), 80),
                    13 => self::prepareItem(ItemFactory::get(113, 0, 16), 80),
                    14 => self::prepareItem(ItemFactory::get(114, 0, 16), 80)
                ];
                
            case 'Bloques Navideños':
                return [
                    12 => self::prepareItem(ItemFactory::get(80, 0, 16), 125),
                    13 => self::prepareItem(ItemFactory::get(174, 0, 16), 125),
                    14 => self::prepareItem(ItemFactory::get(78, 0, 16), 125),
                    21 => self::prepareItem(ItemFactory::get(79, 0, 16), 125)
                ];
                
            case 'Cristales de colores':
                return [
                    12 => self::prepareItem(ItemFactory::get(241, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::get(241, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::get(241, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::get(241, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::get(241, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::get(241, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::get(241, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::get(241, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::get(241, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::get(241, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::get(241, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::get(241, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::get(241, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::get(241, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::get(241, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::get(241, 15, 16), 300)
                ];
                
            case 'Bloques de cuarzo':
                return [
                    22 => self::prepareItem(ItemFactory::get(406, 0, 16), 225),
                    31 => self::prepareItem(ItemFactory::get(156, 0, 16), 225)
                ];
                
            case 'Arbustos y hojas':
                return [
                    22 => self::prepareItem(ItemFactory::get(32, 0, 16), 200),
                    31 => self::prepareItem(ItemFactory::get(2, 0, 16), 200)
                ];
                
            case 'Bloques del fin':
                return [
                    22 => self::prepareItem(ItemFactory::get(121, 0, 16), 5000)
                ];
                
            case 'Bloques de piedra':
                return [
                    21 => self::prepareItem(ItemFactory::get(67, 0, 16), 250),
                    22 => self::prepareItem(ItemFactory::get(98, 0, 16), 250),
                    23 => self::prepareItem(ItemFactory::get(109, 0, 16), 250),
                    30 => self::prepareItem(ItemFactory::get(48, 0, 16), 250),
                    31 => self::prepareItem(ItemFactory::get(1, 0, 16), 250)
                ];
                
            case 'Bloques de arcilla':
                return [
                    12 => self::prepareItem(ItemFactory::get(159, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::get(159, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::get(159, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::get(159, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::get(159, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::get(159, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::get(159, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::get(159, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::get(159, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::get(159, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::get(159, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::get(159, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::get(159, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::get(159, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::get(159, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::get(159, 15, 16), 300)
                ];
                
            case 'Flores':
                return [
                    21 => self::prepareItem(ItemFactory::get(37, 0, 16), 250),
                    22 => self::prepareItem(ItemFactory::get(38, 0, 16), 250),
                    23 => self::prepareItem(ItemFactory::get(37, 0, 16), 250)
                ];
                
            case 'Lanas':
                return [
                    12 => self::prepareItem(ItemFactory::get(35, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::get(35, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::get(35, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::get(35, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::get(35, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::get(35, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::get(35, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::get(35, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::get(35, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::get(35, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::get(35, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::get(35, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::get(35, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::get(35, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::get(35, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::get(35, 15, 16), 300)
                ];
        }
        return null;
    }
}