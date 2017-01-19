<?php
class RgUserApiRes extends AppApi
{  
    //--------------------------------------------------------------------------------------------------------
    // API commands
    //--------------------------------------------------------------------------------------------------------    
    public function isRegisteredUser($param)
    {
        $mac = $param['pc_mac'];
        $pcId = $param['pc_id'];
        $version = $param['version'];

        //find user using pc id or mac
        $rgUserInfo = $this->getUserInfoByPcId($pcId);
        if(empty($rgUserInfo)) {
            $rgUserInfo = $this->getUserInfoByMac($mac);          
        }
        
        //update last access time
        if(!empty($rgUserInfo)) {
            //get UTC time
             $time = gmdate("Y-m-d\TH:i:s\Z");
             $this->updateLastAcTime($time, $mac, $version);

             return RT_TRUE;
        }

        return RT_FALSE;
    }

    public function setUser($param)
    {
        $time = gmdate("Y-m-d\TH:i:s\Z");        
        
        $insert_data['pc_id'] = $param['pc_id'];        
        $insert_data['pc_ip'] = $_SERVER['REMOTE_ADDR'];
        $insert_data['first_access'] = $time;
        $insert_data['last_access'] = $time;
        $insert_data['version'] = $param['version'];

        //log
        AppLogger::writeLine('Api', "UserInfo : ", $insert_data);
        
        //add user
        $userNum = $this->addNewUser($insert_data);

        //add geoInfo         
        if($userNum) {
            $result = $this->setGeolocation($userNum, $this->settings->geoApiUrl, $insert_data['pc_ip']);
         
            if($result) {
                return RT_TRUE;                   
            }  
        }

        return RT_FALSE;
    }
    
    //--------------------------------------------------------------------------------------------------------
    // Utils
    //--------------------------------------------------------------------------------------------------------      
    public function setGeolocation($userNum, $apiUrl, $ip)
    {
        $geoInfo = $this->getGeoLocation($apiUrl, $ip);
        
        //match colums
        $insert_data['userInfo_num'] = $userNum;
        $insert_data['country'] = $geoInfo['country'];
        $insert_data['country_code'] = $geoInfo['countryCode'];
        $insert_data['region'] = $geoInfo['region'];
        $insert_data['region_name'] = $geoInfo['regionName'];
        $insert_data['city'] = $geoInfo['city'];
        $insert_data['zip'] = $geoInfo['zip'];
        $insert_data['latitude'] = $geoInfo['lat'];
        $insert_data['longitude'] = $geoInfo['lon'];
        $insert_data['timezone'] = $geoInfo['timezone'];
        $insert_data['isp'] = $geoInfo['isp'];
        $insert_data['org'] = $geoInfo['org'];
        $insert_data['as_name'] = $geoInfo['as'];
        $insert_data['query'] = $geoInfo['query'];
        $insert_data['datetime'] = gmdate("Y-m-d\TH:i:s\Z");
        $insert_data['status'] = $geoInfo['status'];

        AppLogger::writeLine('Debug', "set geolocation", $insert_data);

        return $this->addGeolocation($insert_data);
    }
    
    //--------------------------------------------------------------------------------------------------------
    // API request
    //--------------------------------------------------------------------------------------------------------  
    private function getGeoLocation($apiUrl, $ip)
    {
        $requestUrl =  AppHelper::pathCombine($apiUrl, $ip);
        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        curl_close($curl);

        $geoInfo = (array)json_decode($curl_response);
        return $geoInfo;
    }
    
    //--------------------------------------------------------------------------------------------------------
    // Queries
    //--------------------------------------------------------------------------------------------------------
    private function getUserInfoByPcId($pcId)
    {
        $db = $this->model->db();       
        $query = "SELECT * FROM " . $this->model->rg_user_table . " " . 
                 "WHERE pc_id = '$pcId' LIMIT 1";
        
        $db->query($query);
        return $db->single();
    }    

    private function getUserInfoByMac($mac)
    {
        $db = $this->model->db();       
        $query = "SELECT * FROM " . $this->model->rg_user_table . " " . 
                 "WHERE pc_mac = '$mac' LIMIT 1";
        
        $db->query($query);
        return $db->single();
    }

    private function updateLastAcTime($time, $mac, $version)
    {
        $db = $this->model->db();
        $query = "UPDATE  " . $this->model->rg_user_table . " " . 
                 "SET last_access = '$time', version = '$version' " . 
                 "WHERE pc_mac = '$mac'";

        $db->query($query);
        return $db->execute();
    }

    private function addNewUser($userInfo)
    {
        $db = $this->model->db();        
        return $db->insert($this->model->rg_user_table, $userInfo);
    }

    private function addGeolocation($geoInfo)
    {
        $db = $this->model->db();    
        return $db->insert($this->model->rg_geolocation_table, $geoInfo);
    }      
}