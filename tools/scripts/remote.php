<?php
// curl -i -X POST -d '{"method": "getusers", "id": 1}' http://joomla.magebridge.dev/components/magebridge/jsonrpc/call/

$client = new Client('http://joomla.magebridge.dev/');
$data = $client->event('mageCustomerSaveAfter');
//$data = $client->getUsers('j%');
//$data = $client->getMethods();
print_r($data);

class Client {

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function log($message, $type = 2, $section = null, $time = null)
    {
        if(empty($time)) $time = time();
        $params = array('message' => $message, 'type' => $type, 'section' => $section, 'time' => $time);
        $data = $this->fetch('call', 'log', $params);

        if(is_object($data) && $data->result == 1) {
            return true;
        }

        return false;
    }

    public function event($event, $arguments = null)
    {
        $params = array('event' => $event, 'arguments' => $arguments);
        $data = $this->fetch('call', 'event', $params);
        return $data;
    }

    public function getUsers($search = null)
    {
        $params = array('search' => $search);
        $data = $this->fetch('call', 'getUsers', $params);
        return $data;
    }

    public function getServiceMap()
    {
        $data = $this->fetch('servicemap');
        return $data;
    }

    public function getMethods()
    {
        $data = $this->getServiceMap();
        if(isset($data->methods)) {
            return $data->methods;
        }
    }

    private function fetch($task = 'call', $method = null, $params = array(), $id = null )
    {
        $url = $this->url.'components/magebridge/jsonrpc/'.$task.'/';
        $api_key = 'test!@#$%^&*()';
        $api_user = 'joomla_api_demo';
        
        if($task == 'call') {

            if($id == null) $id = md5($method);
            if(!is_array($params)) $params = array();
            $params['api_auth'] = array('api_user' => $api_user, 'api_key' => $api_key);

            $post = array(
                'method' => $method,
                'params' => $params,
                'id' => $id,
            );
            $post = json_encode($post);
    
        } else {
            $post = null;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $data = curl_exec($ch);

        $decoded = json_decode($data);
        if(!empty($decoded)) {
            $data = $decoded;
        }

        if(!is_array($data)) {
            // @todo: Error because this is not JSON
        } else {
            if(isset($data['error']) && !empty($data['error']['message'])) {
                // @todo: Error because this is an error
                // $data['error']['message']
                // $data['error']['code']
                // $data['error']['data']
            }
        }
    
        return $data;
    }
}

