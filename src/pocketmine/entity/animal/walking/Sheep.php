<?php

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;
use pocketmine\entity\Colorable;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\block\Wool;
use pocketmine\math\Vector3;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Creature;
use pocketmine\level\sound\ShearSound;

class Sheep extends WalkingAnimal implements Colorable{

	const NETWORK_ID = 13;

	const DATA_COLOR_INFO = 16;

	public $width = 1.45;
	public $height = 1.12;
	public $shearedTicks = 0;

	public function getName() : string{
		return "Sheep";
	}

	public function initEntity(){
		parent::initEntity();
		$this->setMaxHealth(8);
		if(isset($this->namedtag["Color"])){
			$this->setDataProperty(self::DATA_COLOR_INFO, self::DATA_TYPE_BYTE, $this->getColor());
		}else{
			$this->namedtag->Color = new ByteTag("Color", Wool::WHITE);
		}
	}

	/**
	 * @return int
	 */
	public function getColor() : int{
		return (int) $this->namedtag["Color"];
	}

	/**
	 * @param int $color
	 */
	public function setColor(int $color){
		$this->namedtag->Color = new ByteTag("Color", $color);
		$this->setDataProperty(self::DATA_COLOR_INFO, self::DATA_TYPE_BYTE, $this->getColor());
	}

	public function canBeSheared(){
		return !$this->isSheared();
	}

	public function shear(Player $player){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SHEARED, true);
		$this->getLevel()->dropItem(new Vector3($this->getX(), $this->getY() + 1, $this->getZ()), ItemItem::get(35, $this->getColor(), mt_rand(1, 3)));
		$this->getLevel()->addSound(new ShearSound($player));

		$item = $player->getInventory()->getItemInHand();
		if($item->getDamage() >= 238){
			$player->getInventory()->setItemInHand(ItemItem::get(0, 0, 0));
		}else{
			$item->setDamage($item->getDamage() + 1);
			$player->getInventory()->setItemInHand($item);
		}
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		$hasUpdate = parent::entityBaseTick($tickDiff);

		$this->shearedTicks--;

		if($this->shearedTicks <= 0){
			$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SHEARED, false);
			$this->shearedTicks = 20 * 60;
		}

		return $hasUpdate;
	}

	public function targetOption(Creature $creature, $distance){
		if($creature instanceof Player){
			return $creature->spawned && $creature->isAlive() && !$creature->closed && $creature->getInventory()->getItemInHand()->getId() == Item::WHEAT && $distance <= 39;
		}
		return false;
	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::WOOL, 0, $count);
				}
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::RAW_MUTTON, 0, $count);
				}
			}
		}
		return $drops;
	}

}
