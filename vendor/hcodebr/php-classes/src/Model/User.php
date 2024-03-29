<?php 

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class User extends Model {

	const SESSION = "User";

protected $fields = [
        "iduser", "idperson", "desperson", "deslogin", "despassword", "desemail", "nrphone", "inadmin", "dtregister"
    ];

	public static function login($login, $password):User
	{

		$db = new Sql();

		$results = $db->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível fazer login.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"])) {

			$user = new User();
			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Não foi possível fazer login.");

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		) {
			
			header("Location: /admin/login");
			exit;

		}

	}

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :indadmin)", array(
    ":desperson"=>$this->getdesperson(),
    ":deslogin"=>$this->getdeslogin(),
    ":despassword"=>$this->getdespassword(),
    ":desemail"=>$this->getdesemail(),
    ":nrphone"=>$this->getnrphone(),
    ":inadmin"=>$this->getinadmin()
		));

	$this->setData($results[0]);

	}

	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser

		));
 
$this->setData($results[0]);
	}

public function update()
{

	$sql = new Sql();

	$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :indadmin)", array(
    ":iduser"=>$this->getiduser(),
    ":desperson"=>$this->getdesperson(),
    ":deslogin"=>$this->getdeslogin(),
    ":despassword"=>$this->getdespassword(),
    ":desemail"=>$this->getdesemail(),
    ":nrphone"=>$this->getnrphone(),
    ":inadmin"=>$this->getinadmin()
		));

	$this->setData($results[0]);

}

	public function delete()
	{


		$sql = new Sql();

		$sql->query("CALL sp_users_delete", array(
			":iduser"=>$this->getiduser()
		));

	}

}

 ?>