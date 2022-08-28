<?php

/*
 *      ___                                          _
 *    /   | ____ ___  ______ _____ ___  ____ ______(_)___  ___
 *   / /| |/ __ `/ / / / __ `/ __ `__ \/ __ `/ ___/ / __ \/ _ \
 *  / ___ / /_/ / /_/ / /_/ / / / / / / /_/ / /  / / / / /  __/
 * /_/  |_\__, /\__,_/\__,_/_/ /_/ /_/\__,_/_/  /_/_/ /_/\___/
 *          /_/
 *
 * Author - MaruselPlay
 * VK - https://vk.com/maruselplay
 *
 *
 */

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\block\EndPortalFrame;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\block\Solid;
use pocketmine\math\Vector3;
use pocketmine\{Player, Server};
use pocketmine\inventory\Inventory;
use pocketmine\level\particle\SmokeParticle;

class EyeOfEnder extends Item{

	private $temporalVector = null;
	/**
	 * EyeOfEnder constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::EYE_OF_ENDER, 0, $count, "Eye Of Ender");
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function canBeActivated() : bool{
		return true;
	}
	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$p = $player;
        $i = $player->getInventory()->getItemInHand();
        $b = $target;
        $l = $level;
        $x = $target->getX();
        $y = $target->getY();
        $z = $target->getZ();
        if($i->getId() == 381) {
            if($b->getId() == 120 and $b->getDamage() < 4){
                $p->getInventory()->setItemInHand(Item::get($i->getId() , $i->getDamage(), $i->getCount() - 1));
                $l->addParticle(new SmokeParticle(new Vector3($x, $y + 1, $z)));
								$faces = [
									0 => 4,
									1 => 5,
									2 => 6,
									3 => 7,
								];
								$meta = $faces[$b->getDamage()];
                $p->getLevel()->setBlock(new Vector3($x, $y, $z), Block::get(120, $meta));
                if($l->getBlock(new Vector3($x + 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 1, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x - 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x - 2, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x + 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 2, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 4))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 4, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z - 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z + 1), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 4, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z - 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z - 2), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 4, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 4, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x + 4, $y, $z + 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 3, $y, $z + 2), Block::get(119));
                }

                if($l->getBlock(new Vector3($x + 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 1, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x - 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x - 2, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x + 1, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 2, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 3, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x + 3, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 4))->getDamage() >= 4  and $l->getBlock(new Vector3($x + 2, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x + 2, $y, $z - 4))->getDamage() >= 4 and $l->getBlock(new Vector3($x + 1, $y, $z - 4))->getId() == 120 and $l->getBlock(new Vector3($x + 1, $y, $z - 4))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 1, $y, $z - 3), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x + 2, $y, $z - 3), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x - 4, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z - 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z + 1), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 3))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x - 4, $y, $z - 2))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z - 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z - 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z - 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z - 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z - 2), Block::get(119));
                }

                if($l->getBlock(new Vector3($x, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 1, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 1, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 2, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 2, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z + 3))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z + 3))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 3, $y, $z - 1))->getId() == 120 and $l->getBlock(new Vector3($x - 3, $y, $z - 1))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z))->getDamage() >= 4  and $l->getBlock(new Vector3($x - 4, $y, $z + 2))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z + 2))->getDamage() >= 4 and $l->getBlock(new Vector3($x - 4, $y, $z + 1))->getId() == 120 and $l->getBlock(new Vector3($x - 4, $y, $z + 1))->getDamage() >= 4) {
                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 1, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 2, $y, $z + 2), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z + 1), Block::get(119));

                    $p->getLevel()->setBlock(new Vector3($x - 3, $y, $z + 2), Block::get(119));
                }
            }
        }
	}
}
