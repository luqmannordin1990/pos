<?php

namespace App\Filament\Auth;

class ldap{

    private $ldap = null;
    private $ldapServer = '10.23.205.74';
    private $ldapPort = '389';
    public $suffix = '@LPPSA.GOV';
    public $baseDN = 'dc=LPPSA,dc=GOV';
    //private $ldapUser = 'LDAPUser';
    //private $ldapPassword = 'Pas5w0rd';

    public function  __construct(){
        $this->ldap = ldap_connect($this->ldapServer,$this->ldapPort);

        //these next two lines are required for windows server 03
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    public function auth($user,$pass){
        if(empty($user) or empty($pass)){
            return false;
        }

        @$good = ldap_bind($this->ldap,$user.$this->suffix,$pass);
        if($good === true){
            return true;
        } else {
            return false;
        }
    }

    public function __destruct(){
        ldap_unbind($this->ldap);
    }

    public function getInfo($user,$pass){
        $username = $user.$this->suffix;
        $attributes = array('givenname','sn','initials','samaccountname','memberof');
        $filter = "(userPrincipalName=$username)";

        ldap_bind($this->ldap,$username,$pass);
		//ldap_bind($this->ldap,$this->ldapUser.$this->suffix,$this->ldapPassword);

        $result = ldap_search($this->ldap, $this->baseDN, $filter,$attributes);
        $entries = ldap_get_entries($this->ldap, $result);

        return $this->formatInfo($entries);
    }

    private function formatInfo($array){
        $info = array();
        $info['first_name'] = $array[0]['givenname'][0];
        $info['last_name'] = null;
        $info['name'] = $info['first_name'];
        //$info['email'] = $array[0]['mail'][0];
        //$info['user'] = $array[0]['ou'][0];
        //$info['groups'] = $this->groups($array[0]['memberof']);

        return $info;
    }

    private function groups($array){
        $groups = array();
        $tmp = array();

        foreach($array as $entry){
            $tmp = array_merge($tmp,explode(',',$entry));
        }

        foreach($tmp as $value){
            if(substr($value,0,2) == 'CN'){
                $groups[] = substr($value,3);
            }
        }

        return $groups;
    }
}
