<?php
/*
Copyright 2016-2017 Daniil Gentili
(https://daniil.it)
This file is part of MadelineProto.
MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with MadelineProto.
If not, see <http://www.gnu.org/licenses/>.
*/

namespace danog\MadelineProto;

class APIFactory
{
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var langpack
     */
    public $langpack;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var phone
     */
    public $phone;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var stickers
     */
    public $stickers;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var payments
     */
    public $payments;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var bots
     */
    public $bots;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var channels
     */
    public $channels;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var help
     */
    public $help;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var upload
     */
    public $upload;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var photos
     */
    public $photos;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var updates
     */
    public $updates;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var messages
     */
    public $messages;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var contacts
     */
    public $contacts;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var users
     */
    public $users;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var account
     */
    public $account;
    /**
     * @internal this is a internal property generated by build_docs.php, don't change manually
     *
     * @var auth
     */
    public $auth;
    use Tools;

    public $namespace = '';
    public $API;
    public $lua = false;

    public function __construct($namespace, $API)
    {
        $this->namespace = $namespace.'.';
        $this->API = $API;
    }

    public function __call($name, $arguments)
    {
        if ($this->API->setdem) {
            $this->API->setdem = false;
            $this->API->__construct($this->API->settings);
        }
        $this->API->get_config([], ['datacenter' => $this->API->datacenter->curdc]);
        $aargs = isset($arguments[1]) && is_array($arguments[1]) ? $arguments[1] : [];
        $aargs['datacenter'] = $this->API->datacenter->curdc;
        if ($name === 'get_updates' && isset($this->session) && !is_null($this->session) && time() - $this->serialized > 30) {
            $this->serialize($this->session);
            Logger::log(["Didn't serialize in a while, doing that now..."]);
        }
        if ($this->lua === false) {
            return method_exists($this->API, $this->namespace.$name) ? $this->API->{$this->namespace.$name}(...$arguments) : $this->API->method_call($this->namespace.$name, (isset($arguments[0]) && is_array($arguments[0])) ? $arguments[0] : [], $aargs);
        }

        try {
            $deserialized = method_exists($this->API, $this->namespace.$name) ? $this->API->{$this->namespace.$name}(...$arguments) : $this->API->method_call($this->namespace.$name, (isset($arguments[0]) && is_array($arguments[0])) ? $arguments[0] : [], $aargs);
            Lua::convert_objects($deserialized);

            return $deserialized;
        } catch (\danog\MadelineProto\Exception $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\RPCErrorException $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\TL\Exception $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\NothingInTheSocketException $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\PTSException $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\SecurityException $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        } catch (\danog\MadelineProto\TL\Conversion\Exception $e) {
            return ['error_code' => $e->getCode(), 'error' => $e->getMessage()];
        }
    }
}
