<?php

namespace App;

use App\Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Validate password (at least 8 characters and at least 2 numbers)
     * @param $password
     * @return bool
     */
    private static function validatePassword($password)
    {
        // validate length
        if (mb_strlen($password) < 8) {
            return false;
        }

        // validate numbers
        $chars = array();
        $numeric = 0;
        for ($i = 0; $i < mb_strlen($password); $i++ ) {
            $character = mb_substr($password, $i, 1);
            if (is_numeric($character)) {
                $numeric++;
            }
        }

        // at least 2 numbers are required
        if ($numeric < 2) {
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @param bool $validateAdminData
     * @throws Exception
     */
    public static function validateUserData(array $data, $validateAdminData = true)
    {
        // validate mandatory inputs
        $fields = ['first_name', 'last_name', 'email'];

        // validate additonal data
        if ($validateAdminData) {
            $fields[]= 'group';
        }

        foreach ($fields as $field) {
            if (empty($data[$field])) {
                throw new Exception('input field '.$field.' is missing');
            }
        }

        // validate passwords (we process password update only when both passwords are specified)
        if (!empty($data['password']) && !empty($data['conf_password'])) {
            if ($data['password'] !=  $data['conf_password']) {
                throw new Exception('password and password confirmation must be the same');
            }

            if (!self::validatePassword($data['password'])) {
                throw new Exception('invalid password field');
            }

            if (!self::validatePassword( $data['conf_password'])) {
                throw new Exception('invalid confirm password field');
            }
        }

        // validate user group
        if ($validateAdminData && !in_array($data['group'], ['user', 'admin'])) {
            throw new Exception('invalid group value '.$data['group']);
        }

        // validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('invalid email value');
        }
    }

    /**
     * @return array
     */
    public static function defaultData()
    {
        // default data for new user
        return [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'active' => true,
            'group' => 'user',
            'fb_id' => '',
        ];
    }
}
