<?php

/*
 *
 *  _____            _               _____
 * / ____|          (_)             |  __ \
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/
 *                         __/ |
 *                        |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author GenisysPro
 * @link https://github.com/GenisysPro/GenisysPro
 *
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;

use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\math\Vector3;
use pocketmine\tile\Skull as SkullTile;
use pocketmine\entity\monster\flying\Wither;

use pocketmine\tile\Tile;

class SkullBlock extends Flowable {

	protected $id = self::SKULL_BLOCK;

	/**
	 * SkullBlock constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 1;
	}

	/**
	 * @return bool
	 */
	public function getName() : bool{
		return "Mob Head";
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
		//TODO: different bounds depending on attached face (meta)
		return new AxisAlignedBB(
			$this->x + 0.25,
			$this->y,
			$this->z + 0.25,
			$this->x + 0.75,
			$this->y + 0.5,
			$this->z + 0.75
		);
	}

	public function checkSkull($block){
		$level = $this->getLevel();
		if($level->getBlock($block->add(-1, 0, 0))->getId() == self::SKULL_BLOCK and $level->getBlock($block->add(1, 0, 0))->getId() == self::SKULL_BLOCK){
			$level->setBlock($block->add(-1, 0, 0), new Air());
			$level->setBlock($block->add(1, 0, 0), new Air());
			$level->addParticle(new DestroyBlockParticle($level->getBlock($block->add(-1, 0, 0)), $level->getBlock($block->add(-1, 0, 0))));
			$level->addParticle(new DestroyBlockParticle($level->getBlock($block->add(1, 0, 0)), $level->getBlock($block->add(1, 0, 0))));
			return true;
		}
		if($level->getBlock($block->add(0, 0, -1))->getId() == self::SKULL_BLOCK and $level->getBlock($block->add(0, 0, 1))->getId() == self::SKULL_BLOCK){
			$level->setBlock($block->add(0, 0, 1), new Air());
			$level->setBlock($block->add(0, 0, -1), new Air());
			$level->addParticle(new DestroyBlockParticle($level->getBlock($block->add(0, 0, 1)), $level->getBlock($block->add(0, 0, 1))));
			$level->addParticle(new DestroyBlockParticle($level->getBlock($block->add(0, 0, -1)), $level->getBlock($block->add(0, 0, -1))));
			return true;
		}
		return false;
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($face !== 0){
			$this->meta = $face;
			$rot = 0;
			if($face === Vector3::SIDE_UP and $player !== null){
				$rot = floor(($player->yaw * 16 / 360) + 0.5) & 0x0F;
			}
			$this->getLevel()->setBlock($block, $this, true);
			if($player != null){
				if($item->getDamage() === SkullTile::TYPE_WITHER){
					$level = $this->getLevel();
					if($player->getServer()->allowWither){
						if($this->checkSkull($block)){
							$block0 = $level->getBlock($block->add(0, -1, 0));
							$block1 = $level->getBlock($block->add(0, -2, 0));
							$block2 = $level->getBlock($block->add(-1, -1, 0));
							$block3 = $level->getBlock($block->add(1, -1, 0));
							$block4 = $level->getBlock($block->add(0, -1, -1));
							$block5 = $level->getBlock($block->add(0, -1, 1));
							if($block0->getId() == Item::SOUL_SAND and $block1->getId() == Item::SOUL_SAND){
								if($block2->getId() == Item::SOUL_SAND and $block3->getId() == Item::SOUL_SAND and $block4->getId() == Item::AIR and $block5->getId() == Item::AIR){
									$level->setBlock($block2, new Air());
									$level->addParticle(new DestroyBlockParticle($block2, $block2));
									$level->setBlock($block3, new Air());
									$level->addParticle(new DestroyBlockParticle($block3, $block3));
								}elseif($block4->getId() == Item::SOUL_SAND and $block5->getId() == Item::SOUL_SAND and $block2->getId() == Item::AIR and $block3->getId() == Item::AIR){
									$level->setBlock($block4, new Air());
									$level->addParticle(new DestroyBlockParticle($block4, $block4));
									$level->setBlock($block5, new Air());
									$level->addParticle(new DestroyBlockParticle($block5, $block5));
								}else return false;
								$level->setBlock($block, new Air());
								$level->addParticle(new DestroyBlockParticle($block, $block));
								$level->setBlock($block0, new Air());
								$level->addParticle(new DestroyBlockParticle($block0, $block0));
								$level->setBlock($block1, new Air());
								$level->addParticle(new DestroyBlockParticle($block1, $block1));
								$wither = new Wither($player->getLevel(), new CompoundTag("", [
									"Pos" => new ListTag("Pos", [
										new DoubleTag("", $this->x),
										new DoubleTag("", $this->y - 1.5),
										new DoubleTag("", $this->z)
									]),
									"Motion" => new ListTag("Motion", [
										new DoubleTag("", 0),
										new DoubleTag("", 0),
										new DoubleTag("", 0)
									]),
									"Rotation" => new ListTag("Rotation", [
										new FloatTag("", 0),
										new FloatTag("", 0)
									]),
								]));
								$wither->allowMove = true;
								$wither->spawnToAll();
							}
						}
					}
				}
			}
			$moveMouth = false;
			if($item->getDamage() === SkullTile::TYPE_DRAGON){
				if(in_array($target->getId(), [Block::REDSTONE_TORCH, Block::REDSTONE_BLOCK])) $moveMouth = true; //Temp-hacking Dragon Head Mouth Move
			}
			$nbt = new CompoundTag("", [
				new StringTag("id", Tile::SKULL),
				new ByteTag("SkullType", $item->getDamage()),
				new ByteTag("Rot", $rot),
				new ByteTag("MouthMoving", (bool) $moveMouth),
				new IntTag("x", (int) $this->x),
				new IntTag("y", (int) $this->y),
				new IntTag("z", (int) $this->z)
			]);
			if($item->hasCustomName()){
				$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
			}
			Tile::createTile("Skull", $this->getLevel(), $nbt);
			return true;
		}
		return false;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		$tile = $this->level->getTile($this);
		if($tile instanceof SkullTile){
			return [
				[Item::MOB_HEAD, $tile->getType(), 1]
			];
		}
		return [];
	}
}
