<?php namespace Pongo\Cms\Services\Managers;

use Pongo\Cms\Classes\Access;
use Pongo\Cms\Services\Validators\UserValidator as Validator;
use Pongo\Cms\Repositories\UserRepositoryInterface as User;
use Pongo\Cms\Repositories\UserDetailRepositoryInterface as UserDetail;

class UserManager extends BaseManager {

	public function __construct(Access $access, Validator $validator, User $user, UserDetail $userdetail)
	{
		$this->access = $access;
		$this->validator = $validator;
		$this->model = $user;
		$this->related = $userdetail;
	}

	/**
	 * Create a new empty role
	 * @return bool
	 */
	public function createEmptyUser()
	{
		$msg = t('alert.success.user_created');

		$user_account = \Pongo::settings('user_account');
		
		$default_user = array(
			'role_id'	=> $user_account['role_id'],
			'username' 	=> $user_account['username'],
			'email'		=> $user_account['email'],
			'password'	=> \Hash::make($user_account['password']),
			'lang'		=> CMSLANG,
			'editor'	=> 0,
			'is_valid' 	=> 0
		);

		$user = $this->model->create($default_user);
		\Event::fire('user.create', array($user->id));
		$this->related->createUserDetails($user->id);

		$response = array(
			'render'		=> 'user',
			'status' 		=> 'success',
			'msg'			=> $msg,
			'id'			=> $user->id,
			'username'		=> $user_account['username'],
			'url_edit'		=> route('user.edit', array('user_id' => $user->id)),
			'url_delete'	=> route('api.user.delete', array('user_id' => $user->id))
		);

		return $this->setSuccess($response);
	}

	/**
	 * [deleteRole description]
	 * @return [type] [description]
	 */
	public function deleteUser()
	{
		$user_id = $this->input['item_id'];
		if($this->delete($user_id)) {

			\Event::fire('user.delete', array($user_id));
			$this->related->deleteUserDetails($user_id);

			$response = array(
				'remove' 	=> $user_id,
				'status' 	=> 'success',
				'msg'		=> t('alert.success.user_deleted')
			);

			return $this->setSuccess($response);
		}
		return false;
	}

	/**
	 * Get full list of roles
	 * @return array
	 */
	public function getUsersList()
	{	
		return $this->model->getUsersWithRole(XPAGE);
	}

	/**
	 * [validRole description]
	 * @return [type] [description]
	 */
	public function validUser()
	{
		if($this->input) {
			$user_id = $this->input['item_id'];
			$value = $this->input['action'];
			$user = $this->model->find($user_id);
			$user->is_valid = $value;
			$user->save();
			return $this->setSuccess('alert.success.user_modified');
		}
	}

}