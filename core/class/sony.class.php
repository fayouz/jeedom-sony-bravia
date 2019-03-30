<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class sony extends eqLogic
{

    /**
     * @param null $_eqlogic_id
     */
    public function cron30($_eqlogic_id = null) {

        self::updateInformations($_eqlogic_id);
    }

    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */


    /**
     * @param null $_eqlogic_id
     */
    public function updateInformations($_eqlogic_id = null){
        if($_eqlogic_id !== null){
            $eqLogics = array(eqLogic::byId($_eqlogic_id));
        }else{
            $eqLogics = eqLogic::byType('sony');
        }
        foreach ($eqLogics as $tv) {


            if ($tv->getIsEnable() == 1 ) {

                log::add('tv', 'debug', 'Pull Cron pour Sony Tv');
                if( $tv->getConfiguration('auth') == ""){
                    continue;

                }


                $cmdStatut = sonyCmd::byEqLogicIdAndLogicalId($tv->getId(), 'etat');

                $cmdStatut->event(self::getPowerStatus($tv->getId()));

                $tv->refreshWidget();
            }
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function getPowerStatus($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":10,"method":"getPowerStatus","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        $state = $jsonstatus["result"][0]["status"] == "active" ? 1 : 0;

        return $state;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetPowerSavingMode($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":10,"method":"getPowerSavingMode","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);


        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        return $jsonstatus['result'][0]['mode'];
    }

    /**
     *
     * @todo : errror
     * @param $id
     * @return mixed
     */
    public function systemGetDeviceMode($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":10,"method":"getDeviceMode","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,   array(
            "cookie: " . $tv->getConfiguration('auth')));


        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        return $jsonstatus;
    }

    /**
     *
     * @param $id
     * @return mixed
     */
    public function systemGetInterfaceInformation($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":10,"method":"getInterfaceInformation","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,   array(
            "cookie: " . $tv->getConfiguration('auth')));
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);


        return $jsonstatus['result'][0];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function guideGetVersion($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/guide');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":2,"method":"getVersions","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        $state = $jsonstatus["result"][0][0];

        return $state;

    }

    /**
     * @param $id
     * @return mixed
     */
    public function guideGetMethodTypes($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/guide');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getMethodTypes","version":"1.0","params":["1.0"]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        $state = $jsonstatus["result"][0][0];

        return $jsonstatus;
    }

    /**
     * @todo error
     * @param $id
     * @return mixed
     */
    public function guideGetServiceProtocols($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/guide');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getServiceProtocols","version":"1.0","params":["1.0"]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);

        return $response;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetVersions($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":2,"method":"getVersions","version":"1.0","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        $state = $jsonstatus["result"][0][0];
        return $state;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetMethodTypes10($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getMethodTypes","version":"1.0","params":["1.0"]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);



        return $jsonstatus['results'];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetMethodTypes11($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getMethodTypes","version":"1.0","params":["1.1"]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        return $jsonstatus;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetCurrentTime($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getCurrentTime","params":[],"version":"1.0"}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        return $jsonstatus['result'][0];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function systemGetSystemSupportedFunction($id){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":3,"method":"getSystemSupportedFunction","params":[],"version":"1.0"}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $jsonstatus=json_decode($response, true);
        return $jsonstatus['result'][0];
    }


    public function systemGetRemoteControllerInfo($id, $cookie){
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/system');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"method":"getRemoteControllerInfo","params":[],"id":10,"version":"1.0"}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: " . $cookie));
        $response = curl_exec($ch);
        curl_close($ch);

        $controllerinfo = json_decode($response);

        return $controllerinfo;
    }


    /**
     * @param $id
     * @param $code
     * @return array
     */
    public function authenticateRequest($id, $code)
    {
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = $tv->getName();

        $ch = curl_init('http://' . $ip . '/sony/accessControl');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::generateDataAuth('10', $name, 'SONY_' . $id));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization: Basic ' . base64_encode(":" . $code)
        ));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);


        if ($info['http_code'] != 200) {
            return $response;
        }

        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        if (!preg_match('/auth=([A-Za-z0-9]+)/', $header, $matches)) {

            return array('cookie non trouvé', 'header' => $header, 'body' => $body);
        }


        $tv->setConfiguration('auth', $matches[0]);



        $codes = array();
        $controllerinfo = self::systemGetRemoteControllerInfo($id, $matches[0]);

        $interfaceInformation = self::systemGetInterfaceInformation($id);
        $tv->setConfiguration('interfaceInformationProductName', $interfaceInformation['productName']);
        $tv->setConfiguration('interfaceInformationModelName', $interfaceInformation['modelName']);

        foreach ($controllerinfo->result as $k => $v) {
            foreach ($v as $code) {
                if (isset($code->name)) {
                    $codes[$code->name] = $code->value;

                }
            }
        }


        $cmdlogic = sonyCmd::byEqLogicIdAndLogicalId($id, 'etat');
        if (!is_object($cmdlogic)) {
            $cmdlogic = new sonyCmd();
            $cmdlogic->setEqLogic_id($id);
            $cmdlogic->setEqType('sony');
            $cmdlogic->setType('info');
            $cmdlogic->setSubType('string');
            $cmdlogic->setName(__('Statut', __FILE__));
            $cmdlogic->setLogicalId('etat');
            $cmdlogic->setEventOnly(1);
            $cmdlogic->setIsVisible(1);
            $cmdlogic->save();
        }

        $cmdlogic = sonyCmd::byEqLogicIdAndLogicalId($id, 'refresh');

        if (!is_object($cmdlogic)) {
            $cmdlogic = new sonyCmd();
            $cmdlogic->setEqLogic_id($id);
            $cmdlogic->setEqType('sony');
            $cmdlogic->setType('action');
            $cmdlogic->setSubType('other');
            $cmdlogic->setName(__('Rafraichir', __FILE__));
            $cmdlogic->setLogicalId('refresh');
            $cmdlogic->setIsVisible(1);
            $cmdlogic->save();
        }


        foreach ($codes as $name => $code) {
            $cmdlogic = sonyCmd::byEqLogicIdAndLogicalId($id, $name);
            if (!is_object($cmdlogic)) {
                $cmdlogic = new sonyCmd();
                $cmdlogic->setEqLogic_id($id);
                $cmdlogic->setEqType('sony');
                $cmdlogic->setType('action');
                $cmdlogic->setName($name);
                $cmdlogic->setLogicalId($name);
                $cmdlogic->setSubType('other');
                $cmdlogic->setConfiguration('code', $code);
                $cmdlogic->save();
            }

        }


        $tv->save();
        return $codes;
    }

    /**
     * @param $id
     * @param $name
     * @param $uuid
     * @return string
     */
    public function generateDataAuth($id, $name, $uuid)
    {

        $clientid = $name . ":" . $uuid;

        return '{"id":' . $id . ',"method":"actRegister","version":"1.0","params":[{"clientid":"' . $clientid . '","nickname":"' . $name . '(Jeedom)'. '"},[{"clientid":"' . $clientid . '","value":"yes","nickname":"' . $name . '","function":"WOL"}]]}';

    }

    /**
     * @param null $id
     * @return mixed
     */
    public function authenticateRequestCode($id = null)
    {
        $tv = sony::byId($id);
        $ip = $tv->getConfiguration('SONY_IP');
        $name = config::byKey('name', 'sony');
        $name = $tv->getName();


        $ch = curl_init('http://' . $ip . '/sony/accessControl');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::generateDataAuth('10', $name, 'SONY_' . $id));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);


        return $info['http_code'];
    }

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert()
    {

    }

    public function postInsert()
    {

    }

    public function preSave()
    {

    }

    public function postSave()
    {

    }

    public function preUpdate()
    {


    }

    public function postUpdate()
    {
        self::updateInformations($this->getId());
    }

    public function preRemove()
    {

    }

    public function postRemove()
    {

    }

    public function toHtml($_version = 'dashboard') {

        $_version = jeedom::versionAlias($_version);
        $background=$this->getBackgroundColor($_version);
        if (is_object($this->getCmd(null,'etat')) && $this->getCmd(null,'etat')->execCmd()=='1'){
            $state='on';
        } else {
            $state='off';
        }


        $replace = array(
            '#name#' => $this->getName(),
            '#id#' => $this->getId(),
            '#background_color#' => $background,
            '#eqLink#' => $this->getLinkToConfiguration(),
            '#state#' => $state,
        );
        foreach ($this->getCmd('info') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_history#'] = '';
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
            $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            }
        }
        foreach ($this->getCmd('action') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
        }

        $html =  getTemplate('core', $_version, 'sony', 'sony');
        $html = template_replace($replace, getTemplate('core', $_version, 'sony', 'sony'));

        return $html;
    }

    /*     * **********************Getteur Setteur*************************** */
}

class sonyCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array())
    {
        $tv = $this->getEqLogic();

        if ($this->getLogicalId() == 'refresh') {
            $tv->cron30($tv->getId());
            return true;
        }
        if ($this->type == 'action') {
            $codeCommmande = $this->getConfiguration('code');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://' . $tv->getConfiguration('SONY_IP') . '/sony/IRCC',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "<?xml version=\"1.0\"?>\n<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\" s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n\t<s:Body>\n\t<u:X_SendIRCC xmlns:u=\"urn:schemas-sony-com:service:IRCC:1\">\n\t\t<IRCCCode>" . $codeCommmande . "</IRCCCode>\n\t</u:X_SendIRCC>\n\t</s:Body>\n</s:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: text/xml",
                    "cookie: " . $tv->getConfiguration('auth'),
                    "soapaction: \"urn:schemas-sony-com:service:IRCC:1#X_SendIRCC\"",
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
        }

    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
