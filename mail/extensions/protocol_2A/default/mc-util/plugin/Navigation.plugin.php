<?php

/*


           -
         /   \
      /         \
   /   MINECRAFT   \
/         PHP         \
|\       CLIENT      /|
|.   \     2     /   .|
| ..     \   /     .. |
|    ..    |    ..    |
|       .. | ..       |
\          |          /
   \       |       /
      \    |    /
         \ | /
         
         
	by @shoghicp



			DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

	0. You just DO WHAT THE FUCK YOU WANT TO.


*/

require_once("plugin/PathFind.plugin.php");

class Navigation{
	protected $client, $player, $map, $materials, $event, $path, $maxBlocksPerTick, $lastBlock;
	var $fly;
	function __construct($client){
		$this->client = $client;
		$this->player = $this->client->getPlayer();
		$this->map = $this->client->map;
		include_once("misc/materials.php");
		$this->material = $material;
		$this->client->event("onSpoutBlock", "spoutBlock", $this);
		$this->last = microtime(true);
		$this->maxBlocksPerTick = 0.2; //speed
		$this->fly = false;
		$this->speedY = 0;
		$this->event = $this->client->event("onTick", "walker", $this);
		$this->path = null;
		$this->b = null;
		console("[INFO] [Navigation] Loaded");
	}
	
	public function go($x, $y, $z){
		$this->lastBlock = array($this->player->getPosition(true), 0);
		$this->path = new PathFind($this->client, $this->lastBlock[0], array("x" => $x, "y" => $y, "z" => $z));
		var_dump($this->path->path);
	}
	
	public function stop(){
		$this->deleteEvent("onTick", $this->event);
	}
	
	
	public function isSolid($block){
		if(isset($this->material["nosolid"][$block])){
			return true;
		}
		return false;
	}
	
	public function walker($time){
		$pos = $this->player->getPosition();
		if($pos === false or $pos["y"] < 0){
			return false;
		}
		
		if($this->path !== null){
			if($this->lastBlock[1] === 0){
				$this->lastBlock = array($this->b, 10);
				$this->b = $this->path->getNextBlock();
			}
			if($this->b !== null){
				$pos["x"] += ($this->b["x"] - $this->lastBlock["x"]) / $this->maxBlocksPerTick;
				$pos["y"] += ($this->b["y"] - $this->lastBlock["y"]) / $this->maxBlocksPerTick;
				$pos["z"] += ($this->b["z"] - $this->lastBlock["z"]) / $this->maxBlocksPerTick;
				--$this->lastBlock[1];
			}else{
				$this->path = null;
			}
		}else{
			$zone = $this->getZone(1,true);
			if(isset($zone[0][0][-1]) and $this->isSolid($zone[0][0][-1][0]) and $this->fly === false){ //Air
				$this->speedY += 0.9;
				$pos["y"] -= $this->speedY;
				$pos["ground"] = false;
			}elseif($this->fly === false){
				$pos["y"] = floor($pos["y"]);
				$this->speedY = 0;
				$pos["ground"] = true;
			}
		}
		
		$this->player->setPosition($pos["x"],$pos["y"],$pos["z"],$pos["stance"],$pos["yaw"],$pos["pitch"],$pos["ground"]);
	}
	
	public function spoutBlock($data){
		$this->material[$data["id"]] = $data["info"];
	}
	
	public function getBlockName($id){
		if(isset($this->material[$id])){
			return $this->material[$id];
		}
		return "Unknown";
	}
	
	public function getBlock($x, $y, $z){
		return $this->map->getBlock($x, $y, $z);
	}
	
	public function getColumn($x, $z){
		return $this->map->getColumn($x, $z);
	}
	
	public function getRelativeBlock($x = 0, $y = 0, $z = 0){
		$pos = $this->player->getPosition(true);
		if($pos === false){
			return false;
		}
		return $this->map->getBlock($pos["x"] + $x, $pos["y"] + $y, $pos["z"] + $z);
	}
	
	public function getRelativeColumn($x, $z){
		$pos = $this->player->getPosition(true);
		if($pos === false){
			return false;
		}
		$data = $this->map->getColumn($pos["x"], $pos["z"]);
		if($relative === true){
			$data2 = array();
			foreach($data as $x => $a1){
				$data2[$x - $pos["x"]] = array();
				foreach($a1 as $z => $a2){
					$data2[$x - $pos["x"]][$z - $pos["z"]] = array();
					foreach($a2 as $y => $block){
						$data2[$x - $pos["x"]][$z - $pos["z"]][$y - $pos["y"]] = $block;
					}
				}
			}
			return $data2;
		}
		return $data;
	}
	
	public function getZone($radius = 16, $relative = false){
		$pos = $this->player->getPosition(true);
		if($pos === false){
			return false;
		}
		$data = $this->map->getSphere($pos["x"], $pos["y"], $pos["z"], $radius);
		if($relative === true){
			$data2 = array();
			foreach($data as $x => $a1){
				$data2[$x - $pos["x"]] = array();
				foreach($a1 as $z => $a2){
					$data2[$x - $pos["x"]][$z - $pos["z"]] = array();
					foreach($a2 as $y => $block){
						$data2[$x - $pos["x"]][$z - $pos["z"]][$y - $pos["y"]] = $block;
					}
				}
			}
			return $data2;
		}
		return $data;
	}

}


