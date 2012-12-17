<?php
class Alerts{
	public $error = false;
	public $errorMessage = "";

	public $success = false;
	public $successMessage = "";

	public $warning = false;
	public $warningMessage = "";

	public function hasError(){
		return $this->error;
	}

	public function hasSuccess(){
		return $this->success;
	}

	public function hasWarning(){
		return $this->warning;
	}

	public function displayErrors(){
		if($this->hasError()) $this->display("error", $this->errorMessage);
	}

	public function displayWarnings(){
		if($this->hasWarning()) $this->display("warning", $this->warningMessage);
	}

	public function displaySuccess(){
		if($this->hasSuccess()) $this->display("success", $this->successMessage);
	}

	public function displayAllAlerts(){
		if($this->hasError()) $this->display("error", $this->errorMessage);
		if($this->hasWarning()) $this->display("warning", $this->warningMessage);
		if($this->hasSuccess()) $this->display("success", $this->successMessage);
	}

	public function display($class='error', $message='No message supplied'){
		echo "<div class='{$class}'>{$message}</div>";
	}

	public function setError($message){
		$this->errorMessage = $message;
		$this->error = true;
	}

	public function setWarning($message){
		$this->warningMessage = $message;
		$this->warning = true;
	}

	public function setSuccess($message){
		$this->successMessage = $message;
		$this->success = true;
	}

	public function unsetError(){
		$this->error = false;
	}

	public function unsetWarning(){
		$this->warning = false;
	}

	public function unsetSuccess(){
		$this->success = false;
	}
}
$alerts = new Alerts();
if(isset($_GET['error'])){
	$alerts->setError($_GET['error']);
}

if(isset($_GET['warning'])){
	$alerts->setWarning($_GET['warning']);
}

if(isset($_GET['success'])){
	$alerts->setSuccess($_GET['success']);
}
?>