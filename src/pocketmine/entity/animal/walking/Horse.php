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

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;
use pocketmine\entity\Attribute;
use pocketmine\entity\Rideable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use pocketmine\entity\Creature;

class Horse extends WalkingAnimal implements Rideable{
	const NETWORK_ID = 23;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0.6;
	protected $riderOffset = [0, 2.3, 0];
	public $flySpeed = 0.8;
	public $switchDirectionTicks = 100;

	public function getName() : string{
			return "Horse";
	}

	public function initEntity(){
		$this->setMaxHealth(20);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SADDLED, true);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_POWERED, true);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LEASHED, true);
		//$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SPRINTING, 0.2);
		parent::initEntity();
	}

	public function setChestPlate($id){
		/*
		416, 417, 418, 419 only
		*/
		$pk = new MobArmorEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->slots = [
			ItemItem::get(0, 0),
			ItemItem::get($id, 0),
			ItemItem::get(0, 0),
			ItemItem::get(0, 0)
		];
		foreach($this->level->getPlayers() as $player){
			$player->dataPacket($pk);
		}
	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				$count = mt_rand(0, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::LEATHER, 0, $count);
				}
			}
		}
		return $drops;
	}
}
