<?php
use PhpRbac\Rbac;

class Rbac_manager 
{
    public function __construct()
    {
        $this->rbac = new Rbac();
    }

    public function add_permission($title, $desc)
    {
        return $this->rbac->Permissions->add($title, $desc);
    }

    public function add_role($title, $desc)
    {
        return $this->rbac->Roles->add($title, $desc);
    }

    public function has_permission($user_id, $permission)
    {
        return $this->rbac->check($permission, $user_id);
    }

    public function has_role($user_id, $role_id)
    {
        return $this->rbac->check($permission, $user_id);
    }

}